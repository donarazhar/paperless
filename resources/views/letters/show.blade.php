@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
@php
    $user = Auth::user();
    $role = $user->role;
    $dispRecv = $letter->dispositions->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    $dispSent = $letter->dispositions->filter(fn($d) => $d->from_user_id === $user->id);
    $allDisp = $letter->dispositions;
    $isAdmin = $role === 'admin';
@endphp

<style>
    /* Timeline CSS */
    .timeline {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 11px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-icon {
        position: absolute;
        left: -2rem;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    .timeline-icon i { font-size: 0.7rem; }
    .timeline-content {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.5rem;
        border: 1px solid #f1f5f9;
    }
    .letter-label { width: 130px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Detail Surat #{{ $letter->letter_number }}</h1>
    <a href="{{ url()->previous() }}" class="btn btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-4">
    {{-- KOLOM KIRI: Informasi Surat & Lampiran --}}
    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 class="fw-bold mb-1">{{ $letter->subject }}</h5>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar-event"></i> {{ $letter->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
                </div>
                <span class="badge bg-primary px-3 py-2 text-uppercase">{{ str_replace('_', ' ', $letter->status) }}</span>
            </div>

            <div class="mb-4">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="letter-label">Jenis Surat</td>
                            <td>: <span class="text-capitalize fw-medium">{{ $letter->type }}</span></td>
                        </tr>
                        <tr>
                            <td class="letter-label">Pengirim</td>
                            <td>: 
                                @if($letter->type === 'external')
                                    <span class="fw-bold text-primary">{{ $letter->external_sender_name }}</span> (Instansi Luar)
                                    <div class="small text-muted mt-1" style="margin-left: 10px;">Diinput oleh: {{ $letter->creator->name ?? 'Admin' }}</div>
                                @else
                                    <span class="fw-medium">{{ $letter->sender->name ?? 'Sistem' }}</span> ({{ str_replace('_', ' ', $letter->sender->role ?? '') }} – {{ $letter->sender->unit->name ?? '' }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="letter-label">Tujuan</td>
                            <td>: 
                                <span class="fw-medium">
                                @if($letter->type === 'outbound_external')
                                    <span class="fw-bold text-primary">{{ $letter->external_recipient_name }}</span> (Instansi Luar)
                                @elseif($letter->recipientUser)
                                    {{ $letter->recipientUser->name }} (Unit {{ $letter->recipientUser->unit->name }})
                                @else
                                    Unit {{ $letter->recipientUnit->name ?? '' }}
                                @endif
                                </span>
                            </td>
                        </tr>
                        @if($letter->agenda_number)
                        <tr>
                            <td class="letter-label">No Agenda YPIA</td>
                            <td>: <span class="fw-bold text-primary">{{ $letter->agenda_number }}</span></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <hr class="text-muted">

            <div class="mb-4 mt-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.85rem;">Isi Surat</h6>
                <div class="p-4 bg-light rounded" style="line-height: 1.8;">
                    {!! nl2br(e($letter->body)) !!}
                </div>
            </div>

            @if($letter->type === 'outbound_external')
            <div class="mb-4 mt-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.85rem;">Keterangan / Status Eksternal</h6>
                <div class="p-4 bg-warning bg-opacity-10 border border-warning rounded" style="line-height: 1.8;">
                    <div class="text-dark fw-medium mb-2">{!! nl2br(e($letter->external_notes ?: 'Belum ada keterangan.')) !!}</div>
                    
                    @if($letter->from_user_id == Auth::id())
                        <div class="mt-3 border-top border-warning border-opacity-25 pt-3">
                            <button class="btn btn-sm btn-outline-warning fw-bold text-dark" data-bs-toggle="collapse" data-bs-target="#updateNotesForm">
                                <i class="bi bi-pencil-square"></i> Perbarui Keterangan
                            </button>
                            <div class="collapse mt-2" id="updateNotesForm">
                                <form action="{{ route('letters.updateExternalNotes', $letter) }}" method="POST">
                                    @csrf
                                    <textarea name="external_notes" class="form-control mb-2 border-warning" rows="2" placeholder="Masukkan keterangan terbaru...">{{ $letter->external_notes }}</textarea>
                                    <button type="submit" class="btn btn-sm btn-warning fw-bold">Simpan Keterangan</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Lampiran --}}
            @if($letter->attachments->count())
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.85rem;">Lampiran Dokumen</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach($letter->attachments as $att)
                        @php
                            $url = Storage::url($att->file_path);
                            $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                            $name = basename($att->file_path);
                        @endphp
                        @if($ext === 'pdf')
                            <button class="btn btn-outline-danger view-pdf" data-src="{{ $url }}">
                                <i class="bi bi-file-earmark-pdf-fill"></i> {{ $name }}
                            </button>
                        @else
                            <a href="{{ $url }}" download class="btn btn-outline-secondary">
                                <i class="bi bi-file-earmark-fill"></i> {{ $name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Aksi --}}
            <div class="mt-auto pt-3 border-top d-flex gap-2">
                @if($role === 'staf_tu' && !in_array($letter->status, ['draft', 'pending_agenda', 'completed']))
                    <form action="{{ route('letters.complete', $letter) }}" method="POST" class="w-100">
                        @csrf
                        <button class="btn btn-success w-100 py-2" onclick="return confirm('Tandai surat ini sebagai Selesai?')">
                            <i class="bi bi-check-all"></i> Tandai Perjalanan Surat Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Aksi Disposisi & Timeline History --}}
    <div class="col-lg-5">
        
        {{-- Form Agenda (Staf TU) --}}
        @if($role === 'staf_tu' && $letter->status === 'pending_agenda')
            <div class="card p-4 mb-4 border border-primary shadow-sm">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-journal-plus"></i> Beri Nomor Agenda</h5>
                <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nomor Agenda</label>
                        <input type="text" name="agenda_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Catatan Pengantar</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Mohon arahan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send-fill"></i> Agendakan & Teruskan
                    </button>
                </form>
            </div>
        @endif

        {{-- Tindakan Disposisi Penerima --}}
        @if($dispRecv && $dispRecv->status === 'pending')
            <div class="card p-4 mb-4 bg-warning bg-opacity-10 border-warning">
                <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-exclamation-circle-fill text-warning"></i> Tindakan Diperlukan</h5>
                <p class="small text-muted mb-3">Anda menerima disposisi dari <strong>{{ $dispRecv->fromUser->name }}</strong> dengan pesan: <em>"{{ $dispRecv->note }}"</em></p>
                <div class="d-grid gap-2">
                    <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#pertimbanganModal">
                        <i class="bi bi-chat-text"></i> Beri Pertimbangan
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#acceptModal">
                        <i class="bi bi-check-circle"></i> Selesai/Terima
                    </button>
                </div>
            </div>
        @endif

        {{-- Form Teruskan / Disposisi --}}
        @php
            $canDispose = false;
            if ($role === 'kasubag_tu' && in_array($letter->status, ['in_review_kasubag', 'in_consideration'])) {
                $canDispose = true;
            } elseif ($letter->to_unit_id == $user->unit_id && $letter->status !== 'completed') {
                $canDispose = true;
            } elseif ($dispRecv && $dispRecv->status === 'pending') {
                $canDispose = true;
            }
            // Staf TU Sekretariat juga bisa mem-forward jika surat ditujukan ke Sekretariat (Administrator)
            if ($role === 'staf_tu' && $letter->to_unit_id == $user->unit_id && $letter->status !== 'completed') {
                $canDispose = true;
            }
        @endphp

        @if($canDispose)
            <div class="card p-4 mb-4 border border-warning shadow-sm">
                <h5 class="fw-bold mb-3 text-warning"><i class="bi bi-arrow-repeat"></i> Teruskan / Buat Disposisi</h5>
                <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                            <label class="form-check-label text-muted small fw-bold" for="typeUnit">Ke Unit</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                            <label class="form-check-label text-muted small fw-bold" for="typeUser">Ke Personal</label>
                        </div>
                    </div>
                    <div class="mb-3" id="selectUnit">
                        <select name="to_unit_id" class="form-select">
                            <option value="">— Pilih Unit —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} (Cab. {{ $unit->branch->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="selectUser" style="display:none;">
                        <select name="to_user_id" class="form-select">
                            <option value="">— Pilih Pengguna —</option>
                            @foreach(\App\Models\User::where('unit_id', $user->unit_id)->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="note" class="form-control" rows="2" placeholder="Catatan Disposisi..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Disposisi</button>
                </form>
            </div>
        @endif

        {{-- TIMELINE HISTORY --}}
        <div class="card p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-clock-history text-muted me-2"></i> Lacak Perjalanan</h5>
            
            @if($letter->histories->isEmpty())
                <div class="text-center text-muted py-3">Belum ada riwayat.</div>
            @else
                <div class="timeline">
                    @foreach($letter->histories as $h)
                        @php
                            $iconColor = 'text-primary';
                            $borderColor = 'var(--primary-color)';
                            if($h->action == 'sent') { $iconColor = 'text-success'; $borderColor = '#198754'; }
                            if(str_contains($h->action, 'dispos')) { $iconColor = 'text-warning'; $borderColor = '#ffc107'; }
                            if($h->action == 'disposition_accepted') { $iconColor = 'text-success'; $borderColor = '#198754'; }
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-icon" style="border-color: {{ $borderColor }}">
                                <i class="bi bi-circle-fill {{ $iconColor }}" style="font-size: 8px;"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="text-dark small">{{ ucfirst(str_replace('_', ' ', $h->action)) }}</strong>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $h->created_at->format('d M H:i') }}</small>
                                </div>
                                @if($h->note)
                                    <p class="small text-muted mb-1">"{{ $h->note }}"</p>
                                @endif
                                <small class="text-secondary d-block mt-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-person-fill"></i> 
                                    @if($h->user)
                                        {{ $h->user->name }} (Unit {{ $h->user->unit->name }})
                                    @else
                                        System
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODALS --}}
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-secondary">
                <iframe id="pdfFrame" style="width:100%;height:85vh; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

@if($dispRecv)
    <div class="modal fade" id="pertimbanganModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="pertimbangan">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Beri Pertimbangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="response_note" class="form-control" rows="4" required placeholder="Ketik hasil pertimbangan atau respons..."></textarea>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white">Kirim Respons</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="acceptModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="accepted">
                    <div class="modal-body text-center p-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 fw-bold">Selesaikan Disposisi</h4>
                        <p class="text-muted">Apakah tugas/arahan ini sudah selesai dikerjakan?</p>
                        <div class="mt-4">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success px-4">Ya, Selesai</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // PDF Preview
            const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            document.querySelectorAll('.view-pdf').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('pdfFrame').src = btn.dataset.src;
                    pdfModal.show();
                });
            });
            document.getElementById('pdfModal').addEventListener('hidden.bs.modal', () => document.getElementById('pdfFrame').src = '');

            // Disposisi Radio Toggle
            const selUnit = document.getElementById('selectUnit');
            const selUser = document.getElementById('selectUser');
            document.getElementsByName('recipient_type').forEach(radio => {
                radio.addEventListener('change', () => {
                    if (document.getElementById('typeUser').checked) {
                        if(selUnit) selUnit.style.display = 'none';
                        if(selUser) selUser.style.display = 'block';
                    } else {
                        if(selUnit) selUnit.style.display = 'block';
                        if(selUser) selUser.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endpush
@endsection