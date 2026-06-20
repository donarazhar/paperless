@extends('layouts.app')
@section('title', 'Detail Surat')

@section('content')
@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);
    $statusMap = [
        'pending_agenda'    => ['pill'=>'sp-warn',  'label'=>'Antre Agenda',    'icon'=>'bi-hourglass-split'],
        'in_review_kasubag' => ['pill'=>'sp-blue',  'label'=>'Review Kasubag',  'icon'=>'bi-eye-fill'],
        'in_consideration'  => ['pill'=>'sp-purple','label'=>'Disposisi Aktif', 'icon'=>'bi-arrow-repeat'],
        'completed'         => ['pill'=>'sp-green', 'label'=>'Selesai',          'icon'=>'bi-check-circle-fill'],
        'draft'             => ['pill'=>'sp-gray',  'label'=>'Draft',            'icon'=>'bi-pencil'],
    ];
    $sm = $statusMap[$letter->status] ?? ['pill'=>'sp-gray','label'=>ucfirst($letter->status),'icon'=>'bi-info-circle'];
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

<style>
    .sp-warn   { background:#fef9c3; color:#92400e; }
    .sp-blue   { background:#dbeafe; color:#1d4ed8; }
    .sp-purple { background:#ede9fe; color:#7c3aed; }
    .sp-green  { background:#dcfce7; color:#166534; }
    .sp-gray   { background:#f1f5f9; color:#475569; }
    .status-pill { display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:700;padding:0.3rem 0.8rem;border-radius:100px; }

    .panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.4rem; }

    /* Info header */
    .info-bar { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.1rem 1.35rem;margin-bottom:1.25rem; }
    .info-chips { display:flex;flex-wrap:wrap;gap:0.5rem 1.5rem;align-items:center; }
    .info-chip { display:flex;align-items:center;gap:0.4rem;font-size:0.82rem;color:#334155; }
    .info-chip .chip-key { font-size:0.68rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;color:#94a3b8;margin-right:2px; }
    .info-chip .chip-val { font-weight:600;color:#0f172a; }
    .info-body { margin-top:0.9rem;padding-top:0.9rem;border-top:1px solid #f1f5f9;font-size:0.875rem;color:#334155;line-height:1.8; }

    .sec-title { font-size:0.7rem;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#94a3b8;margin-bottom:0.85rem; }

    /* Attachments */
    .att-btn { display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 0.9rem;border-radius:0.6rem;font-size:0.78rem;font-weight:700;border:1.5px solid;cursor:pointer;transition:background .15s;text-decoration:none; }
    .att-pdf  { background:#fff5f5;border-color:#fecaca;color:#dc2626; }
    .att-pdf:hover  { background:#fee2e2;color:#dc2626; }
    .att-doc  { background:#eff6ff;border-color:#bfdbfe;color:#2563eb; }
    .att-other{ background:#f8fafc;border-color:#e2e8f0;color:#475569; }

    /* Timeline */
    .tl { position:relative;padding-left:1.75rem; }
    .tl::before { content:'';position:absolute;left:9px;top:4px;bottom:4px;width:2px;background:#e8edf4; }
    .tl-item { position:relative;margin-bottom:1rem; }
    .tl-dot { position:absolute;left:-1.75rem;top:3px;width:18px;height:18px;border-radius:50%;background:#fff;border:2px solid #94a3b8;display:flex;align-items:center;justify-content:center;z-index:1; }
    .tl-dot.sent { border-color:#16a34a; }
    .tl-dot.disp { border-color:#d97706; }
    .tl-dot.done { border-color:#16a34a; }
    .tl-dot.blue { border-color:#2563eb; }
    .tl-body { background:#f8faff;border:1px solid #eef1f7;border-radius:0.65rem;padding:0.7rem 0.9rem; }
    .tl-action { font-size:0.8rem;font-weight:700;color:#0f172a; }
    .tl-time   { font-size:0.68rem;color:#94a3b8; }
    .tl-note   { font-size:0.75rem;color:#64748b;margin-top:3px;font-style:italic; }
    .tl-by     { font-size:0.7rem;color:#94a3b8;margin-top:4px; }

    /* Action panels */
    .action-panel { border-radius:0.9rem;padding:1rem 1.15rem;margin-bottom:0.85rem; }
    .action-panel.need { background:#fffbeb;border:1.5px solid #fde68a; }
    .action-panel.dispose { background:#fff;border:1.5px solid #e8edf4; }
    .action-panel.agenda { background:#eff6ff;border:1.5px solid #bfdbfe; }

    /* Main grid */
    .show-grid { display:grid;grid-template-columns:340px 1fr;gap:1.25rem;align-items:start; }
    @media(max-width:991px) { .show-grid { grid-template-columns:1fr; } }

    .btn-back { display:inline-flex;align-items:center;gap:5px;background:#f8faff;border:1.5px solid #e8edf4;color:#475569;border-radius:0.6rem;padding:0.45rem 1rem;font-size:0.85rem;font-weight:600;text-decoration:none;transition:background .15s; }
    .btn-back:hover { background:#eff6ff;color:#2563eb;border-color:#bfdbfe; }
</style>

{{-- ═══ PAGE HEADER ═══ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="status-pill {{ $sm['pill'] }}"><i class="bi {{ $sm['icon'] }}"></i> {{ $sm['label'] }}</span>
            @if($letter->agenda_number)
                <span class="status-pill sp-blue"><i class="bi bi-hash"></i> {{ $letter->agenda_number }}</span>
            @endif
        </div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">{{ $letter->subject }}</h1>
        <p class="text-muted mb-0" style="font-size:0.78rem;"><i class="bi bi-calendar-event me-1"></i>{{ $letter->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY · HH:mm') }}</p>
    </div>
    <a href="{{ url()->previous() }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

{{-- ═══ 1. INFORMASI SURAT (Full Width) ═══ --}}
<div class="info-bar mb-4">
    <div class="info-chips">
        <div class="info-chip">
            <span class="chip-key">No. Surat</span>
            <span class="chip-val">{{ $letter->letter_number ?: '—' }}</span>
        </div>
        <div class="info-chip">
            <span class="chip-key">Jenis</span>
            <span class="chip-val text-capitalize">{{ str_replace('_',' ',$letter->type) }}</span>
        </div>
        <div class="info-chip">
            <span class="chip-key">Pengirim</span>
            <span class="chip-val">
                @if($letter->type==='external')
                    <span style="color:#7c3aed;">{{ $letter->external_sender_name }}</span>
                    <span class="text-muted" style="font-weight:400;font-size:0.72rem;"> · Instansi Luar (diinput: {{ $letter->creator->name ?? 'Admin' }})</span>
                @else
                    {{ $letter->sender->name ?? 'Sistem' }}
                    @if($letter->sender->unit->name ?? false)
                        <span class="text-muted" style="font-weight:400;font-size:0.72rem;"> · {{ $letter->sender->unit->name }}</span>
                    @endif
                @endif
            </span>
        </div>
        <div class="info-chip">
            <span class="chip-key">Tujuan</span>
            <span class="chip-val">
                @if($letter->type==='outbound_external')
                    <span style="color:#2563eb;">{{ $letter->external_recipient_name }}</span>
                    <span class="text-muted" style="font-weight:400;font-size:0.72rem;"> · Instansi Luar</span>
                @elseif($letter->recipientUser)
                    {{ $letter->recipientUser->name }}
                    @if($letter->recipientUser->unit->name ?? false)
                        <span class="text-muted" style="font-weight:400;font-size:0.72rem;"> · {{ $letter->recipientUser->unit->name }}</span>
                    @endif
                @else
                    Unit {{ $letter->recipientUnit->name ?? '—' }}
                @endif
            </span>
        </div>
    </div>
    @if($letter->body)
    <div class="info-body">
        <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;color:#94a3b8;margin-right:8px;">Isi</span>
        {!! nl2br(e($letter->body)) !!}
    </div>
    @endif

    {{-- External Notes --}}
    @if($letter->type==='outbound_external')
    <div style="margin-top:0.85rem;padding-top:0.85rem;border-top:1px solid #f1f5f9;">
        <span style="font-size:0.68rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;color:#94a3b8;margin-right:8px;">Keterangan Eksternal</span>
        <span style="font-size:0.875rem;color:#374151;">{!! nl2br(e($letter->external_notes ?: 'Belum ada keterangan.')) !!}</span>
        @if($letter->from_user_id == Auth::id())
            <div class="mt-2">
                <button class="att-btn att-other" data-bs-toggle="collapse" data-bs-target="#extNoteForm" style="font-size:0.75rem;">
                    <i class="bi bi-pencil-square"></i> Perbarui
                </button>
                <div class="collapse mt-2" id="extNoteForm">
                    <form action="{{ route('letters.updateExternalNotes', $letter) }}" method="POST">
                        @csrf
                        <textarea name="external_notes" class="form-control mb-2" rows="2">{{ $letter->external_notes }}</textarea>
                        <button type="submit" class="btn btn-sm btn-warning fw-bold">Simpan</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>

{{-- ═══ 2. MAIN GRID (30% Aksi+Timeline | 70% Preview) ═══ --}}
<div class="show-grid">

    {{-- KIRI (30%): AKSI + TIMELINE --}}
    <div>
        {{-- Perlu Tindakan --}}
        @if($dispRecv && $dispRecv->status==='pending')
        <div class="action-panel need">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                <span style="font-weight:800;font-size:0.9rem;">Tindakan Diperlukan</span>
            </div>
            <p style="font-size:0.82rem;color:#64748b;margin-bottom:0.85rem;">Disposisi dari <strong>{{ $dispRecv->fromUser->name }}</strong>: <em>"{{ $dispRecv->note }}"</em></p>
            <div class="d-flex flex-column gap-2">
                <button class="btn fw-bold w-100" style="background:#dbeafe;color:#1d4ed8;border:none;font-size:0.85rem;" data-bs-toggle="modal" data-bs-target="#pertimbanganModal">
                    <i class="bi bi-chat-text"></i> Beri Pertimbangan
                </button>
                <button class="btn btn-success fw-bold w-100" style="font-size:0.85rem;" data-bs-toggle="modal" data-bs-target="#acceptModal">
                    <i class="bi bi-check-circle"></i> Tandai Selesai
                </button>
            </div>
        </div>
        @endif

        {{-- Agenda --}}
        @if($role==='staf_tu' && $letter->status==='pending_agenda')
        <div class="action-panel agenda">
            <div class="sec-title" style="color:#1d4ed8;">Beri Nomor Agenda</div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <input type="text" name="agenda_number" class="form-control" placeholder="Nomor agenda…" required style="height:40px;font-size:0.875rem;">
                </div>
                <div class="mb-2">
                    <textarea name="note" class="form-control" rows="2" placeholder="Catatan pengantar…" style="font-size:0.875rem;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold" style="height:42px;">
                    <i class="bi bi-send-fill me-1"></i> Agendakan & Teruskan
                </button>
            </form>
        </div>
        @endif

        {{-- Disposisi --}}
        @if($canDispose)
        <div class="action-panel dispose">
            <div class="sec-title">Teruskan / Disposisi</div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="d-flex gap-3 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                        <label class="form-check-label" for="typeUnit" style="font-size:0.82rem;font-weight:600;">Ke Unit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                        <label class="form-check-label" for="typeUser" style="font-size:0.82rem;font-weight:600;">Ke Personal</label>
                    </div>
                </div>
                <div class="mb-2" id="selectUnit">
                    <select name="to_unit_id" class="form-select" style="height:40px;font-size:0.875rem;">
                        <option value="">— Pilih Unit —</option>
                        @foreach(\App\Models\Unit::all() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->branch->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2" id="selectUser" style="display:none;">
                    <select name="to_user_id" class="form-select" style="height:40px;font-size:0.875rem;">
                        <option value="">— Pilih Pengguna —</option>
                        @foreach(\App\Models\User::where('unit_id',$user->unit_id)->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <textarea name="note" class="form-control" rows="2" placeholder="Catatan disposisi…" required style="font-size:0.875rem;"></textarea>
                </div>
                <button type="submit" class="btn w-100 fw-bold" style="height:42px;background:#fef9c3;color:#92400e;border:1.5px solid #fde68a;">
                    <i class="bi bi-arrow-right-circle-fill me-1"></i> Kirim Disposisi
                </button>
            </form>
        </div>
        @endif

        {{-- Selesaikan --}}
        @if($role==='staf_tu' && !in_array($letter->status,['draft','pending_agenda','completed']))
        <div class="mb-3">
            <form action="{{ route('letters.complete', $letter) }}" method="POST">
                @csrf
                <button class="btn btn-success w-100 fw-bold py-2" onclick="return confirm('Tandai surat ini sebagai Selesai?')">
                    <i class="bi bi-check-all me-1"></i> Perjalanan Surat Selesai
                </button>
            </form>
        </div>
        @endif

        @if(!$hasAction)
        <div class="panel text-center p-4 mb-3">
            <i class="bi bi-shield-check text-success" style="font-size:2.5rem;"></i>
            <div style="font-size:0.85rem;font-weight:600;color:#64748b;margin-top:0.5rem;">Tidak ada aksi yang diperlukan saat ini.</div>
        </div>
        @endif

        {{-- LACAK PERJALANAN SURAT (di bawah card aksi) --}}
        <div class="panel" style="margin-top:0;">
            <div class="sec-title">Lacak Perjalanan Surat</div>
            @if($letter->histories->isEmpty())
                <div style="text-align:center;padding:1.5rem;color:#94a3b8;font-size:0.85rem;">Belum ada riwayat.</div>
            @else
            <div class="tl">
                @foreach($letter->histories as $h)
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
                            'disposed'                => 'Surat Didisposisikan',
                            'disposition_responded'   => 'Pertimbangan Diberikan',
                            'disposition_accepted'    => 'Disposisi Diselesaikan',
                            'completed'               => 'Surat Selesai',
                            'draft'                   => 'Disimpan sebagai Draft',
                            default                   => ucfirst(str_replace('_', ' ', $h->action)),
                        };
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $dc }}">
                            <i class="bi bi-circle-fill" style="font-size:5px;color:inherit;"></i>
                        </div>
                        <div class="tl-body">
                            <div class="d-flex justify-content-between">
                                <span class="tl-action">{{ $actionLabel }}</span>
                                <span class="tl-time">{{ $h->created_at->format('d M H:i') }}</span>
                            </div>
                            @if($h->note)<div class="tl-note">"{{ $h->note }}"</div>@endif
                            <div class="tl-by"><i class="bi bi-person-fill me-1"></i>{{ $h->user ? $h->user->name.' ('.$h->user->unit->name.')' : 'System' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- KANAN (70%): PREVIEW DOKUMEN --}}
    <div class="panel" style="margin-top:0;">
        <div class="sec-title">Pratinjau Dokumen Lampiran</div>
        @if($letter->attachments->count())
            <div class="d-flex flex-wrap gap-2 mb-3" id="attachmentButtons">
                @foreach($letter->attachments as $att)
                    @php
                        $url  = Storage::url($att->file_path);
                        $ext  = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                        $name = basename($att->file_path);
                    @endphp
                    @if($ext==='pdf')
                        <button class="att-btn att-pdf view-pdf" data-src="{{ $url }}" data-name="{{ $name }}">
                            <i class="bi bi-file-earmark-pdf-fill"></i> {{ $name }}
                        </button>
                    @elseif(in_array($ext,['doc','docx']))
                        <a href="{{ $url }}" download class="att-btn att-doc">
                            <i class="bi bi-file-earmark-word-fill"></i> {{ $name }}
                        </a>
                    @else
                        <a href="{{ $url }}" download class="att-btn att-other">
                            <i class="bi bi-file-earmark-fill"></i> {{ $name }}
                        </a>
                    @endif
                @endforeach
            </div>
            <div id="pdfInlinePreview" style="display:none;">
                <div class="d-flex align-items-center gap-2 mb-2 px-1">
                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                    <span style="font-size:0.75rem;font-weight:700;color:#64748b;" id="pdfPreviewName"></span>
                </div>
                <iframe id="pdfInlineFrame" style="width:100%;height:680px;border:none;border-radius:0.5rem;background:#fff;"></iframe>
            </div>
        @else
            <div class="text-center p-5" style="border:1.5px dashed #e8edf4;border-radius:0.75rem;">
                <i class="bi bi-file-earmark-x" style="font-size:2.5rem;color:#cbd5e1;"></i>
                <div style="font-size:0.85rem;color:#94a3b8;font-weight:500;margin-top:0.5rem;">Surat ini tidak memiliki lampiran dokumen.</div>
            </div>
        @endif
    </div>

</div>

{{-- MODALS --}}
@if($dispRecv)
<div class="modal fade" id="pertimbanganModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                @csrf <input type="hidden" name="action" value="pertimbangan">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Beri Pertimbangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="response_note" class="form-control" rows="4" required placeholder="Tulis hasil pertimbangan…"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Kirim Respons</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;">
            <form action="{{ route('dispositions.respond', $dispRecv) }}" method="POST">
                @csrf <input type="hidden" name="action" value="accepted">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem;"></i>
                    <h5 class="mt-3 fw-bold">Selesaikan Disposisi?</h5>
                    <p style="font-size:0.875rem;color:#64748b;">Tugas/arahan ini sudah selesai dikerjakan?</p>
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Ya, Selesai</button>
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
    const prev=document.getElementById('pdfInlinePreview');
    const frame=document.getElementById('pdfInlineFrame');
    const nameEl=document.getElementById('pdfPreviewName');
    const pdfBtns = document.querySelectorAll('.view-pdf');

    pdfBtns.forEach(btn => btn.addEventListener('click', (e) => {
        if(!frame||!prev) return;
        frame.src = btn.dataset.src;
        nameEl.textContent = btn.dataset.name;
        prev.style.display = 'block';
        if(e.isTrusted) prev.scrollIntoView({behavior:'smooth', block:'start'});
    }));

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
@endsection