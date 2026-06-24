@extends('layouts.mailbox')
@section('title', 'Detail Surat')

@section('content')
<style>
    .mail-detail-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }
    .mail-subject-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    .mail-sender-block {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .mail-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #4f46e5;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
    }
    .mail-sender-info { flex: 1; }
    .mail-sender-name { font-weight: 700; color: #0f172a; font-size: 0.95rem; }
    .mail-sender-meta { font-size: 0.8rem; color: #64748b; }
    .mail-date-text { font-size: 0.85rem; color: #64748b; }
    
    .mail-body {
        padding: 2rem;
        font-size: 0.95rem;
        color: #334155;
        line-height: 1.6;
        background: #fff;
    }

    .mail-attachments {
        padding: 1rem 2rem;
        border-top: 1px solid var(--border);
        background: #f8fafc;
    }
    .attachment-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        font-size: 0.85rem;
        color: #0f172a;
        text-decoration: none;
        transition: all 0.2s;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }
    .attachment-chip:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
        color: #0f172a;
    }
    
    .mail-actions-wrapper {
        padding: 1.5rem 2rem;
        background: #fff;
        border-top: 1px solid var(--border);
    }
    
    .action-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    /* Utility grids */
    .detail-main { border-right: none; }
</style>

@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->sortByDesc('created_at')->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    
    $canDispose = false;
    if ($letter->status !== 'completed') {
        if (in_array($role, ['bagian_tu', 'kepala_sekretariat'])) {
            if (in_array($letter->status, ['in_review_bagian_tu', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } elseif ($role === 'subag_persuratan') {
            if (in_array($letter->status, ['in_review_subag', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } else {
            if ($letter->to_unit_id == $user->unit_id && in_array($role, ['admin_unit', 'kepala_unit'])) {
                $canDispose = true;
            }
            elseif ($dispRecv && $dispRecv->status === 'pending' && in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit'])) {
                $canDispose = true;
            }
        }
    }

    $isDispoToMyUnit = $dispRecv && $dispRecv->to_unit_id === $user->unit_id && is_null($dispRecv->to_user_id) && $dispRecv->status === 'pending';

    $hasAction = $letter->status !== 'completed' && (
        (in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status==='pending_approval')
        || ($role==='admin_unit' && $letter->status==='pending_sending')
        || ($role==='admin_unit' && $isDispoToMyUnit)
        || ($role==='admin_sekretariat' && $letter->status==='pending_agenda')
        || ($role==='subag_persuratan' && $letter->status==='in_review_subag')
        || ($role==='bagian_tu' && $letter->status==='in_review_bagian_tu')
        || ($dispRecv && $dispRecv->status==='pending')
        || $canDispose
    );
@endphp

<div class="list-header" style="background: #fff; padding: 0.75rem 1rem;">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm text-muted border-0 bg-transparent rounded-circle" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
            <i class="bi bi-arrow-left" style="font-size: 1.2rem;"></i>
        </a>
        <h5 class="m-0 text-truncate" style="font-weight: 600; font-size: 1.1rem;">Kembali</h5>
    </div>
</div>

<div class="mail-scroll" style="max-width:1000px; margin:0 auto; width:100%;">
    <div class="detail-main bg-white border rounded shadow-sm my-4">
        {{-- HEADER & SENDER --}}
        <div class="mail-detail-header">
            <h2 class="mail-subject-title">
                {{ $letter->subject }} 
                <span class="badge bg-light text-dark border ms-2" style="font-size:0.75rem; vertical-align:middle;">{{ $letter->status }}</span>
                @if($letter->agenda_number)
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle ms-1" style="font-size:0.75rem; vertical-align:middle;">Agenda: {{ $letter->agenda_number }}</span>
                @endif
            </h2>
            
            <div class="mail-sender-block mt-4">
                @php
                    if($letter->type==='outbound_external') {
                        $senderName = $letter->sender->name ?? 'Sistem';
                        $senderMeta = 'Eksternal: ' . $letter->external_recipient_name;
                        $initial = substr($senderName, 0, 1);
                    } else if($letter->type==='internal') {
                        $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                        $senderMeta = 'Ke: ' . ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? '—'));
                        $initial = substr($senderName, 0, 1);
                    } else { // incoming external
                        $senderName = $letter->external_sender_name;
                        $senderMeta = 'Eksternal Masuk';
                        $initial = substr($senderName, 0, 1);
                    }
                @endphp
                <div class="mail-avatar">{{ strtoupper($initial) }}</div>
                <div class="mail-sender-info">
                    <div class="mail-sender-name">{{ $senderName }}</div>
                    <div class="mail-sender-meta">{{ $senderMeta }}</div>
                </div>
                <div class="mail-date-text">
                    {{ $letter->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                </div>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    @php 
                        $encodedId = \Vinkla\Hashids\Facades\Hashids::encode($letter->id); 
                        $isRespondingDispo = $dispRecv && $dispRecv->status==='pending' && $dispRecv->to_user_id === $user->id && in_array($role, ['subag_persuratan', 'kepala_unit', 'kepala_sekretariat']);
                    @endphp
                    
                    @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']) && !$isRespondingDispo)
                        <a href="{{ route('letters.create', ['reply_to' => $encodedId]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Balas"><i class="bi bi-reply-fill me-1"></i> Balas</a>
                        <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Teruskan"><i class="bi bi-forward-fill me-1"></i> Teruskan</a>
                    @endif
                    
                    @php
                        $canCompleteSekretariat = in_array($role, ['bagian_tu', 'subag_persuratan']) && $letter->status !== 'completed' && !in_array($letter->status, ['draft', 'pending_approval', 'pending_sending', 'pending_agenda']);
                        $canCompleteUnit = in_array($role, ['admin_unit', 'kepala_unit', 'sub_unit']) && $canDispose && $letter->status !== 'completed';
                    @endphp

                    @if($hasAction || in_array($role, ['kepala_unit', 'subag_persuratan', 'admin_unit', 'admin_sekretariat', 'bagian_tu', 'sub_unit']))
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Lainnya">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:0.9rem;">
                            @if(in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status === 'pending_approval')
                                <li><a class="dropdown-item fw-bold text-warning-emphasis" href="#" onclick="event.preventDefault(); document.getElementById('formApprove').submit();"><i class="bi bi-check-circle-fill me-2"></i>ACC Surat</a></li>
                            @endif

                            @if(in_array($role, ['admin_unit', 'admin_sekretariat']) && $letter->status === 'pending_sending')
                                <li><a class="dropdown-item fw-bold text-info-emphasis" href="#" onclick="event.preventDefault(); document.getElementById('formSendFinal').submit();"><i class="bi bi-send-fill me-2"></i>Kirim Surat Fisik</a></li>
                            @endif

                            @if($dispRecv && $dispRecv->status==='pending' && $dispRecv->to_user_id === $user->id)
                                <li><a class="dropdown-item fw-bold text-danger-emphasis" href="#" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-reply-fill me-2"></i>Tanggapi Disposisi</a></li>
                            @endif

                            @if($canDispose && !$isRespondingDispo)
                                <li><a class="dropdown-item fw-bold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#dispoModal"><i class="bi bi-arrow-right-circle-fill me-2"></i>Disposisi Baru</a></li>
                            @endif

                            @if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
                                <li><a class="dropdown-item fw-bold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#agendaModal"><i class="bi bi-journal-plus me-2"></i>Beri Agenda</a></li>
                            @endif

                            @if($role==='subag_persuratan' && $letter->status==='in_review_subag' && !$isRespondingDispo)
                                <li><a class="dropdown-item fw-bold text-primary" href="#" onclick="event.preventDefault(); document.getElementById('formForwardTU').submit();"><i class="bi bi-arrow-right me-2"></i>Teruskan ke TU</a></li>
                            @endif

                            @if(($canCompleteSekretariat || $canCompleteUnit) && !$isRespondingDispo)
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fw-bold text-success" href="#" onclick="event.preventDefault(); if(confirm('Selesaikan surat ini?')) document.getElementById('formComplete').submit();"><i class="bi bi-archive-fill me-2"></i>Arsipkan Selesai</a></li>
                            @endif
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="mail-body">
            @if($letter->body && $letter->body !== '-')
                {!! nl2br(e($letter->body)) !!}
            @else
                <em class="text-muted">Tidak ada teks pengantar.</em>
            @endif

            @if($letter->type==='outbound_external' && $letter->external_notes)
                <div class="mt-4 p-3 bg-warning-subtle border border-warning-subtle rounded">
                    <strong>Catatan Eksternal:</strong><br>
                    {!! nl2br(e($letter->external_notes)) !!}
                </div>
            @endif
        </div>

        {{-- ATTACHMENTS --}}
        @if($letter->attachments->count() > 0)
        <div class="mail-attachments">
            <div class="fw-bold mb-3" style="font-size: 0.85rem; color:#64748b;">{{ $letter->attachments->count() }} Lampiran</div>
            <div class="d-flex flex-wrap">
                @foreach($letter->attachments as $att)
                    @php
                        $url  = Storage::url($att->file_path);
                        $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                        $name = basename($att->file_path);
                        $icon = 'bi-file-earmark';
                        if($ext==='pdf') $icon = 'bi-file-earmark-pdf-fill text-danger';
                        elseif(in_array($ext, ['doc','docx'])) $icon = 'bi-file-earmark-word-fill text-primary';
                        elseif(in_array($ext, ['jpg','jpeg','png'])) $icon = 'bi-file-earmark-image-fill text-success';
                    @endphp
                    @if($ext === 'pdf')
                        <div class="attachment-chip view-pdf" data-src="{{ $url }}">
                            <i class="bi {{ $icon }}"></i> {{ $name }}
                        </div>
                    @else
                        <a href="{{ $url }}" target="_blank" class="attachment-chip">
                            <i class="bi {{ $icon }}"></i> {{ $name }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- INLINE PDF PREVIEW --}}
        @if($letter->attachments->filter(fn($att) => strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION)) === 'pdf')->count() > 0)
        <div id="pdfInlinePreview" style="display:none; border-top:1px solid var(--border);">
            <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-light border-bottom">
                <div class="fw-bold text-muted" style="font-size:0.85rem;"><i class="bi bi-eye-fill me-2"></i>Pratinjau PDF</div>
                <button type="button" class="btn-close" onclick="document.getElementById('pdfInlinePreview').style.display='none'" style="font-size:0.75rem;"></button>
            </div>
            <iframe id="pdfInlineFrame" style="width:100%;height:600px;border:none;display:block;"></iframe>
        </div>
        @endif

        {{-- GMAIL-STYLE REPLY BOX --}}
        @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']) && !$isRespondingDispo)
        <div class="p-4 bg-white border-top">
            <div class="d-flex align-items-center gap-3">
                <div class="mail-avatar" style="width:40px;height:40px;font-size:1rem;background:#4f46e5;color:#fff;">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('letters.create', ['reply_to' => $encodedId]) }}" class="flex-grow-1 text-decoration-none px-4 py-2" style="border:1px solid #cbd5e1; border-radius:100px; color:#64748b; font-size:0.9rem; transition:background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-reply-fill me-1"></i> Balas...
                    </a>
                    <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="flex-grow-1 text-decoration-none px-4 py-2" style="border:1px solid #cbd5e1; border-radius:100px; color:#64748b; font-size:0.9rem; transition:background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-forward-fill me-1"></i> Teruskan...
                    </a>
                </div>
            </div>
        </div>
        @endif
        {{-- History / Timeline --}}
        @if($letter->histories->where('action', 'disposed')->isNotEmpty())
        <div class="px-4 py-3 bg-white border-top">
            <h6 class="fw-bold mb-3" style="font-size:0.85rem;color:#64748b;"><i class="bi bi-clock-history me-2"></i>Riwayat Disposisi</h6>
            <div class="border-start border-2 ms-2 ps-3 border-primary-subtle">
                @foreach($letter->histories->where('action', 'disposed')->sortBy('created_at') as $h)
                    @php
                        $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                    @endphp
                    <div class="mb-3 position-relative">
                        <div class="position-absolute bg-primary rounded-circle" style="width:10px;height:10px;left:-22px;top:4px;"></div>
                        <div style="font-size:0.75rem; color:#64748b;">{{ $h->created_at->format('d/m/Y H:i') }}</div>
                        @if($dispMatch)
                            <div style="font-size:0.85rem; font-weight:600; color:#0f172a;">Ke: {{ $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—') }}</div>
                        @endif
                        <div style="font-size:0.85rem; color:#475569; font-style:italic;">"{{ preg_replace('/^\[.*?\]\s*/', '', $h->note) }}"</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- HIDDEN FORMS FOR SIMPLE ACTIONS --}}
<form id="formApprove" action="{{ route('letters.approve', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formSendFinal" action="{{ route('letters.sendFinal', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formForwardTU" action="{{ route('letters.forwardToBagianTu', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formComplete" action="{{ route('letters.complete', $letter) }}" method="POST" style="display:none;">@csrf</form>

{{-- MODALS FOR COMPLEX ACTIONS --}}

{{-- Modal Disposisi Baru --}}
@if($canDispose)
<div class="modal fade" id="dispoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Disposisi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="d-flex gap-3 mb-3 p-2 bg-light rounded border">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                            <label class="form-check-label fw-semibold" for="typeUnit" style="font-size:0.8rem;">Ke Unit</label>
                        </div>
                        <div class="form-check m-0">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                            <label class="form-check-label fw-semibold" for="typeUser" style="font-size:0.8rem;">Ke Personal</label>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="selectUnit">
                        <select name="to_unit_id" class="form-select form-select-sm">
                            <option value="">— Pilih Unit —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->branch->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3" id="selectUser" style="display:none;">
                        <select name="to_user_id" class="form-select form-select-sm">
                            <option value="">— Pilih Organ / Jabatan —</option>
                            @foreach(\App\Models\Unit::with('organs.users')->get() as $unit)
                                @if($unit->organs->isNotEmpty())
                                    <optgroup label="{{ $unit->name }}">
                                        @foreach($unit->organs as $organ)
                                            @foreach($organ->users as $u)
                                                <option value="{{ $u->id }}">{{ $organ->name }} — {{ $u->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <textarea name="note" class="form-control mb-3" rows="3" placeholder="Catatan instruksi..." required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-send-check-fill"></i> Kirim Disposisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Beri Agenda --}}
@if($role==='admin_sekretariat' && $letter->status==='pending_agenda')
<div class="modal fade" id="agendaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Beri Agenda Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Agenda <span class="text-danger">*</span></label>
                        <input type="text" name="agenda_number" class="form-control" placeholder="Contoh: 123/YPIA/2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-journal-plus"></i> Simpan & Teruskan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODALS --}}
@if($dispRecv)
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Tanggapi Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.respond', $dispRecv->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Tindakan <span class="text-danger">*</span></label>
                        <select name="action" class="form-select" required>
                            <option value="">— Pilih Tindakan —</option>
                            <option value="accepted">Selesai / Dikerjakan</option>
                            <option value="pertimbangan">Butuh Pertimbangan</option>
                            <option value="followup">Akan Ditindaklanjuti</option>
                            <option value="rejected">Tolak / Tidak Valid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan <span class="text-danger">*</span></label>
                        <textarea name="response_note" class="form-control" rows="3" placeholder="Hasil tindakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-save-fill"></i> Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const prev = document.getElementById('pdfInlinePreview');
    const frame = document.getElementById('pdfInlineFrame');
    const pdfBtns = document.querySelectorAll('.view-pdf');

    pdfBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if(!frame || !prev) return;
            frame.src = btn.dataset.src;
            prev.style.display = 'block';
            
            pdfBtns.forEach(b => b.classList.remove('bg-danger-subtle'));
            btn.classList.add('bg-danger-subtle');

            if(e.isTrusted) prev.scrollIntoView({behavior:'smooth', block:'start'});
        });
    });

    if(pdfBtns.length > 0) pdfBtns[0].click();

    const sU = document.getElementById('selectUnit');
    const sP = document.getElementById('selectUser');
    document.getElementsByName('recipient_type').forEach(r => r.addEventListener('change', () => {
        const u = document.getElementById('typeUser').checked;
        if(sU) sU.style.display = u ? 'none' : 'block';
        if(sP) sP.style.display = u ? 'block' : 'none';
    }));
});
</script>
@endpush
