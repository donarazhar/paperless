@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    $statusMap = [
        'pending_agenda'    => ['bg'=>'#fef9c3', 'color'=>'#92400e', 'border'=>'#fde68a', 'label'=>'Antre Agenda',    'icon'=>'bi-hourglass-split'],
        'in_review_kasubag' => ['bg'=>'#dbeafe', 'color'=>'#1d4ed8', 'border'=>'#bfdbfe', 'label'=>'Review Kasubag',  'icon'=>'bi-eye-fill'],
        'in_consideration'  => ['bg'=>'#ede9fe', 'color'=>'#7c3aed', 'border'=>'#ddd6fe', 'label'=>'Disposisi Aktif', 'icon'=>'bi-arrow-repeat'],
        'completed'         => ['bg'=>'#dcfce7', 'color'=>'#166534', 'border'=>'#bbf7d0', 'label'=>'Selesai',         'icon'=>'bi-check-circle-fill'],
        'draft'             => ['bg'=>'#f1f5f9', 'color'=>'#475569', 'border'=>'#e2e8f0', 'label'=>'Draft',           'icon'=>'bi-pencil'],
    ];
    $sm = $statusMap[$letter->status] ?? ['bg'=>'#f1f5f9', 'color'=>'#475569', 'border'=>'#e2e8f0', 'label'=>ucfirst($letter->status),'icon'=>'bi-info-circle'];
    
    $canDispose = false;
    if ($role==='kasubag_tu' && in_array($letter->status,['in_review_kasubag','in_consideration'])) $canDispose=true;
    elseif ($letter->to_unit_id==$user->unit_id && $letter->status!=='completed') $canDispose=true;
    elseif ($dispRecv && $dispRecv->status==='pending') $canDispose=true;
    if ($role==='staf_tu' && $letter->to_unit_id==$user->unit_id && $letter->status!=='completed') $canDispose=true;
    $hasAction = ($dispRecv && $dispRecv->status==='pending')
        || ($role==='staf_tu' && $letter->status==='pending_agenda')
        || $canDispose
        || ($role==='staf_tu' && !in_array($letter->status,['draft','pending_agenda','completed']));
@endphp



{{-- ═══ HERO HEADER ═══ --}}
<div class="detail-hero">
    <div class="hero-content">
        <div class="status-badge mb-3" style="background:{{$sm['bg']}};color:{{$sm['color']}};border-color:{{$sm['border']}};">
            <i class="bi {{$sm['icon']}}"></i> {{$sm['label']}}
        </div>
        <h1 class="hero-title">{{ $letter->subject }}</h1>
        <div class="hero-meta">
            <span><i class="bi bi-calendar-event"></i> {{ $letter->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            @if($letter->agenda_number)
                <span><i class="bi bi-hash"></i> Agenda: {{ $letter->agenda_number }}</span>
            @endif
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

{{-- ═══ INFORMASI SURAT ═══ --}}
<div class="modern-panel">
    <div class="panel-title"><i class="bi bi-info-circle-fill"></i> Detail Informasi</div>
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Nomor Surat</span>
            <span class="info-value" style="font-family: monospace; font-size: 1rem;">{{ $letter->letter_number ?: '—' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Sifat / Jenis</span>
            <span class="info-value text-capitalize">{{ str_replace('_',' ',$letter->type) }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Pengirim</span>
            <span class="info-value">
                @if($letter->type==='external')
                    <span style="color:var(--accent);">{{ $letter->external_sender_name }}</span>
                    <span class="info-sub">Instansi Luar (Diinput: {{ $letter->creator->name ?? 'Admin' }})</span>
                @else
                    {{ $letter->sender->name ?? 'Sistem' }}
                    @if($letter->sender->unit->name ?? false)
                        <span class="info-sub">{{ $letter->sender->unit->name }}</span>
                    @endif
                @endif
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Tujuan Utama</span>
            <span class="info-value">
                @if($letter->type==='outbound_external')
                    <span style="color:var(--primary);">{{ $letter->external_recipient_name }}</span>
                    <span class="info-sub">Instansi Luar</span>
                @elseif($letter->recipientUser)
                    {{ $letter->recipientUser->name }}
                    @if($letter->recipientUser->unit->name ?? false)
                        <span class="info-sub">{{ $letter->recipientUser->unit->name }}</span>
                    @endif
                @else
                    Unit {{ $letter->recipientUnit->name ?? '—' }}
                @endif
            </span>
        </div>
    </div>

    <div class="row mt-4 gx-3 gy-3">
        @if($letter->body)
        <div class="col-lg-{{ $letter->attachments->count() ? '6' : '12' }}">
            <div class="content-box m-0 h-100">
                <span class="info-label d-block mb-2">Isi Surat / Pengantar</span>
                {!! nl2br(e($letter->body)) !!}
            </div>
        </div>
        @endif

        @if($letter->attachments->count())
        <div class="col-lg-{{ $letter->body ? '6' : '12' }}">
            <div class="content-box m-0 h-100" style="background:var(--surface); border-color:var(--border);">
                <span class="info-label d-block mb-2">Dokumen Lampiran</span>
                <div class="d-flex flex-column gap-2" id="attachmentButtons">
                    @foreach($letter->attachments as $att)
                        @php
                            $url  = Storage::url($att->file_path);
                            $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                            $name = basename($att->file_path);
                        @endphp
                        @if($ext==='pdf')
                            <div class="att-card att-pdf view-pdf w-100" style="max-width:none;" data-src="{{ $url }}" data-name="{{ $name }}">
                                <div class="att-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                                <div class="att-details">
                                    <div class="att-name">{{ $name }}</div>
                                    <div class="att-ext">Dokumen PDF</div>
                                </div>
                            </div>
                        @elseif(in_array($ext,['doc','docx']))
                            <a href="{{ $url }}" download class="att-card att-doc w-100" style="max-width:none;">
                                <div class="att-icon"><i class="bi bi-file-earmark-word-fill"></i></div>
                                <div class="att-details">
                                    <div class="att-name">{{ $name }}</div>
                                    <div class="att-ext">Dokumen Word</div>
                                </div>
                            </a>
                        @else
                            <a href="{{ $url }}" download class="att-card att-other w-100" style="max-width:none;">
                                <div class="att-icon"><i class="bi bi-file-earmark-fill"></i></div>
                                <div class="att-details">
                                    <div class="att-name">{{ $name }}</div>
                                    <div class="att-ext">Dokumen Lampiran</div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($letter->type==='outbound_external')
    <div class="content-box" style="background:#fffbeb; border-color:#fde68a; margin-top:1rem;">
        <span class="info-label d-block mb-2" style="color:#92400e;">Keterangan Eksternal</span>
        <span style="color:#92400e;">{!! nl2br(e($letter->external_notes ?: 'Belum ada keterangan.')) !!}</span>
        @if($letter->from_user_id == Auth::id())
            <div class="mt-3">
                <button class="btn btn-sm btn-warning fw-bold text-dark" data-bs-toggle="collapse" data-bs-target="#extNoteForm"><i class="bi bi-pencil-square"></i> Perbarui Keterangan</button>
                <div class="collapse mt-2" id="extNoteForm">
                    <form action="{{ route('letters.updateExternalNotes', $letter) }}" method="POST">
                        @csrf
                        <textarea name="external_notes" class="form-control mb-2" rows="2">{{ $letter->external_notes }}</textarea>
                        <button type="submit" class="btn btn-sm btn-dark fw-bold">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>

{{-- ═══ MAIN LAYOUT GRID ═══ --}}
<div class="layout-grid">
    
    {{-- KOLOM KIRI (Aksi & Timeline) --}}
    <div>
        {{-- Perlu Tindakan --}}
        @if($dispRecv && $dispRecv->status==='pending')
        <div class="action-box warning">
            <div class="action-title text-warning-emphasis"><i class="bi bi-exclamation-circle-fill"></i> Tindakan Diperlukan</div>
            <div class="action-desc">Disposisi dari <strong>{{ $dispRecv->fromUser->name }}</strong>:<br><em style="color:#92400e;">"{{ $dispRecv->note }}"</em></div>
            <div class="d-flex flex-column gap-2">
                <button class="btn-custom outline w-100 text-primary-emphasis" data-bs-toggle="modal" data-bs-target="#pertimbanganModal"><i class="bi bi-chat-text"></i> Beri Pertimbangan</button>
                <button class="btn-custom success w-100" data-bs-toggle="modal" data-bs-target="#acceptModal"><i class="bi bi-check-circle"></i> Tandai Selesai</button>
            </div>
        </div>
        @endif

        {{-- Agenda --}}
        @if($role==='staf_tu' && $letter->status==='pending_agenda')
        <div class="action-box primary">
            <div class="action-title text-primary"><i class="bi bi-journal-plus"></i> Beri Nomor Agenda</div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <input type="text" name="agenda_number" class="form-control mb-2" placeholder="Nomor agenda…" required>
                <textarea name="note" class="form-control mb-3" rows="2" placeholder="Catatan pengantar…"></textarea>
                <button type="submit" class="btn-custom primary w-100"><i class="bi bi-send-fill"></i> Agendakan & Teruskan</button>
            </form>
        </div>
        @endif

        {{-- Disposisi --}}
        @if($canDispose)
        <div class="action-box default">
            <div class="action-title"><i class="bi bi-arrow-right-circle-fill text-primary"></i> Teruskan / Disposisi</div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="d-flex gap-3 mb-3 p-2 rounded" style="background:var(--surface-2); border:1px solid var(--border);">
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                        <label class="form-check-label fw-semibold" for="typeUnit" style="font-size:0.85rem;">Ke Unit</label>
                    </div>
                    <div class="form-check m-0">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                        <label class="form-check-label fw-semibold" for="typeUser" style="font-size:0.85rem;">Ke Personal</label>
                    </div>
                </div>
                <div class="mb-3" id="selectUnit">
                    <select name="to_unit_id" class="form-select">
                        <option value="">— Pilih Unit —</option>
                        @foreach(\App\Models\Unit::all() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->branch->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="selectUser" style="display:none;">
                    <select name="to_user_id" class="form-select">
                        <option value="">— Pilih Pengguna —</option>
                        @foreach(\App\Models\User::where('unit_id',$user->unit_id)->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <textarea name="note" class="form-control mb-3" rows="2" placeholder="Tulis catatan instruksi / arahan disposisi…" required></textarea>
                <button type="submit" class="btn-custom w-100" style="background:#fef9c3; color:#92400e; border:1.5px solid #fde68a;"><i class="bi bi-send-check-fill"></i> Kirim Disposisi</button>
            </form>
        </div>
        @endif

        {{-- Selesaikan --}}
        @if($role==='staf_tu' && !in_array($letter->status,['draft','pending_agenda','completed']))
        <form action="{{ route('letters.complete', $letter) }}" method="POST" class="mb-4">
            @csrf
            <button class="btn-custom success w-100" onclick="return confirm('Tandai perjalanan surat ini sebagai Selesai secara final?')"><i class="bi bi-check-all"></i> Perjalanan Surat Selesai</button>
        </form>
        @endif

        @if(!$hasAction)
        <div class="d-flex align-items-center gap-3 p-3 mb-4" style="background:var(--surface); border:1.5px solid var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(15,23,42,0.03);">
            <div style="width:36px; height:36px; background:var(--green-soft); color:var(--green); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="bi bi-shield-check" style="font-size:1.1rem;"></i>
            </div>
            <div>
                <div style="font-size:0.85rem; font-weight:700; color:var(--text);">Semua Beres</div>
                <div style="font-size:0.75rem; color:var(--muted);">Tidak ada tindakan yang diperlukan oleh Anda saat ini.</div>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="modern-panel">
            <div class="panel-title"><i class="bi bi-clock-history"></i> Disposisi Surat</div>
            @if($letter->histories->isEmpty())
                <div class="text-center p-3 text-muted" style="font-size:0.85rem;">Belum ada riwayat.</div>
            @else
            <div class="tl">
                @foreach($letter->histories->sortBy('created_at') as $h)
                    @if(in_array($h->action, ['agenda_assigned', 'pending_agenda'])) @continue @endif
                    @php
                        $dc = 'blue';
                        if($h->action==='sent') $dc='sent';
                        elseif(str_contains($h->action,'dispos')) $dc='disp';
                        elseif($h->action==='disposition_accepted') $dc='done';
                        $actionLabel = match($h->action) {
                            'sent'                    => 'Surat Dikirim',
                            'created'                 => 'Surat Dibuat',
                            'read'                    => 'Surat Dibaca',
                            'agenda_set'              => 'Nomor Agenda Ditetapkan',
                            'forwarded'               => 'Diteruskan ke Kasubag TU',
                            'disposed'                => 'Disposisi',
                            'disposition_responded'   => 'Pertimbangan',
                            'disposition_accepted'    => 'Disposisi Diselesaikan',
                            'completed'               => 'Surat Selesai',
                            'draft'                   => 'Disimpan Draft',
                            default                   => ucfirst(str_replace('_', ' ', $h->action)),
                        };

                        $dispMatch = null;
                        if ($h->action === 'disposed') {
                            $dispMatch = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                        }
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $dc }}"><i class="bi bi-circle-fill" style="font-size:6px;color:inherit;"></i></div>
                        <div class="tl-card">
                            <div class="tl-header">
                                <span class="tl-title">{{ $actionLabel }}</span>
                                <span class="tl-time">{{ $h->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($h->action === 'disposed' && $dispMatch)
                                <div class="tl-user">Ke: <strong style="color:var(--text);">{{ $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—') }}</strong></div>
                            @else
                                <div class="tl-user"><i class="bi bi-person-fill"></i> Oleh: {{ $h->user ? $h->user->name : 'System' }}</div>
                            @endif
                            @if($h->note)
                                @php $cleanNote = preg_replace('/^\[.*?\]\s*/', '', $h->note); @endphp
                                <div class="tl-note">"{{ $cleanNote }}"</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- KOLOM KANAN (Preview) --}}
    <div>
        <div class="modern-panel h-100" style="margin-bottom:0; padding:0; overflow:hidden;">
            @if($letter->attachments->filter(fn($att) => strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION)) === 'pdf')->count() > 0)
                <div id="pdfInlinePreview" style="display:none;" class="h-100">
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="background:#fff;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--text);" id="pdfPreviewName"></span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Pratinjau Dokumen</span>
                    </div>
                    <iframe id="pdfInlineFrame" style="width:100%;height:800px;border:none;background:#f8fafc;display:block;"></iframe>
                </div>
            @else
                <div class="text-center py-5 my-4 m-3" style="border:2px dashed var(--border); border-radius:1rem; background:var(--surface-2);">
                    <div style="width:72px; height:72px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                        <i class="bi bi-file-earmark-x" style="font-size:2rem;color:var(--muted-light);"></i>
                    </div>
                    <div style="font-size:1rem; font-weight:700; color:var(--text); margin-bottom:0.25rem;">Tidak Ada Pratinjau Dokumen</div>
                    <div style="font-size:0.85rem; color:var(--muted);">Hanya lampiran format PDF yang dapat ditampilkan di sini.</div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- MODALS --}}
@if($dispRecv)
<div class="modal fade" id="pertimbanganModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem; overflow:hidden;">
            <div class="modal-header border-0 bg-primary-subtle px-4 py-3">
                <h5 class="modal-title fw-bold text-primary-emphasis"><i class="bi bi-chat-text-fill me-2"></i>Beri Pertimbangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                @csrf <input type="hidden" name="action" value="pertimbangan">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">Tuliskan catatan pertimbangan Anda</label>
                    <textarea name="response_note" class="form-control" rows="4" required placeholder="Contoh: Terkait arahan tersebut, kami menyarankan..." style="border-radius:0.75rem;"></textarea>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" style="border-radius:0.75rem;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:0.75rem;">Kirim Pertimbangan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1.5rem;">
            <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                @csrf <input type="hidden" name="action" value="accepted">
                <div class="modal-body text-center p-4 pt-5">
                    <div style="width:80px;height:80px;background:var(--green-soft);color:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                        <i class="bi bi-check-lg" style="font-size:3rem;line-height:0;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Selesaikan Tugas?</h5>
                    <p style="font-size:0.85rem;color:var(--muted);margin-bottom:2rem;">Tugas atau arahan dari disposisi ini sudah selesai Anda kerjakan secara penuh?</p>
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-success fw-bold py-2" style="border-radius:0.75rem;">Ya, Selesai</button>
                        <button type="button" class="btn btn-light fw-bold py-2" style="border-radius:0.75rem;" data-bs-dismiss="modal">Batal</button>
                    </div>
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
    const nameEl = document.getElementById('pdfPreviewName');
    const pdfBtns = document.querySelectorAll('.view-pdf');

    pdfBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if(!frame || !prev) return;
            frame.src = btn.dataset.src;
            nameEl.textContent = btn.dataset.name;
            prev.style.display = 'block';
            
            // Activate state
            pdfBtns.forEach(b => b.style.borderColor = 'var(--border)');
            btn.style.borderColor = '#dc2626';

            if(e.isTrusted) prev.scrollIntoView({behavior:'smooth', block:'center'});
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
