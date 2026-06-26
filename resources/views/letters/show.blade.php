@extends('layouts.mailbox')
@section('title', 'Detail Surat')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   SHOW — Gmail Email Detail Design
═══════════════════════════════════════════ */

.sv-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    background: #f6f8fc;
    font-family: 'Inter', sans-serif;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.sv-root::-webkit-scrollbar { width: 5px; }
.sv-root::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ TOP ACTION BAR (like Gmail toolbar) ══ */
.sv-bar {
    display: flex;
    align-items: center;
    gap: .35rem;
    padding: .55rem .875rem;
    background: #fff;
    border-bottom: 1px solid #e9eef6;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 20;
}
.sv-bar-left  { display: flex; align-items: center; gap: .35rem; }
.sv-bar-right { margin-left: auto; display: flex; align-items: center; gap: .35rem; }

.sv-back-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; color: #5f6368;
    transition: background .12s;
    text-decoration: none; color: inherit;
}
.sv-back-btn:hover { background: #f1f3f4; color: #202124; }

.sv-bar-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

/* Action buttons in bar */
.sv-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .4rem .9rem;
    border: none; border-radius: 100px;
    font-size: .8rem; font-weight: 700;
    cursor: pointer; transition: background .15s, box-shadow .15s;
    text-decoration: none; white-space: nowrap;
    font-family: inherit;
}
.sv-btn-outline {
    background: #fff; color: #3c4043;
    border: 1.5px solid #dadce0;
}
.sv-btn-outline:hover { background: #f1f3f4; border-color: #bdc1c6; color: #202124; }
.sv-btn-blue {
    background: #1a73e8; color: #fff;
    box-shadow: 0 1px 3px rgba(26,115,232,.3);
}
.sv-btn-blue:hover { background: #1557b0; color: #fff; box-shadow: 0 2px 6px rgba(26,115,232,.4); }
.sv-btn-green {
    background: #188038; color: #fff;
    box-shadow: 0 1px 3px rgba(24,128,56,.3);
}
.sv-btn-green:hover { background: #0d652d; color: #fff; box-shadow: 0 2px 6px rgba(24,128,56,.4); }
.sv-btn-red {
    background: #c5221f; color: #fff;
}
.sv-btn-red:hover { background: #a50e0e; color: #fff; }
.sv-btn-amber {
    background: #e37400; color: #fff;
}
.sv-btn-amber:hover { background: #b06000; color: #fff; }

/* ══ BODY LAYOUT ══ */
.sv-body {
    padding: 1rem 1rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ══ EMAIL CARD (main content — Gmail style) ══ */
.sv-email-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

/* Subject headline */
.sv-subject-bar {
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.sv-subject {
    font-size: 1.3rem; font-weight: 700;
    color: #202124; letter-spacing: -.02em;
    flex: 1; min-width: 0;
    line-height: 1.35;
}
.sv-status-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; padding: .3rem .75rem;
    border-radius: 100px; flex-shrink: 0;
    border: 1.5px solid transparent;
}
.sp-draft     { background: #fef3c7; color: #78350f; border-color: #fde293; }
.sp-pending   { background: #fce8e6; color: #c5221f; border-color: #f5c6c6; }
.sp-process   { background: #d2e3fc; color: #1557b0; border-color: #a8c7fa; }
.sp-done      { background: #e6f4ea; color: #137333; border-color: #a8d5b5; }
.sp-consider  { background: #ede9fe; color: #5b21b6; border-color: #ddd6fe; }
.sp-default   { background: #f1f3f4; color: #5f6368; border-color: #dadce0; }

/* Sender row — Gmail header style */
.sv-sender-row {
    display: flex;
    align-items: flex-start;
    gap: .875rem;
    padding: .875rem 1.5rem;
    border-bottom: 1px solid #f1f3f4;
}
.sv-s-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; font-weight: 800;
    flex-shrink: 0;
}
.sv-s-av-int { background: #d2e3fc; color: #1557b0; }
.sv-s-av-ext { background: #fce8f3; color: #9c1c6a; }

.sv-s-info { flex: 1; min-width: 0; }
.sv-s-name { font-size: .9rem; font-weight: 700; color: #202124; }
.sv-s-meta {
    font-size: .78rem; color: #80868b;
    display: flex; flex-wrap: wrap; gap: .35rem .75rem;
    margin-top: .2rem;
}
.sv-s-meta span { display: flex; align-items: center; gap: .25rem; }
.sv-s-date { font-size: .78rem; color: #80868b; white-space: nowrap; flex-shrink: 0; }

/* Meta grid below sender */
.sv-meta-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0;
    border-bottom: 1px solid #f1f3f4;
}
.sv-meta-cell {
    padding: .75rem 1.5rem;
    border-right: 1px solid #f1f3f4;
}
.sv-meta-cell:last-child { border-right: none; }
.sv-m-label {
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: #80868b; margin-bottom: .2rem;
}
.sv-m-value {
    font-size: .875rem; font-weight: 600; color: #202124;
}
.sv-m-value.empty { color: #9aa0a6; font-style: italic; font-weight: 400; }

/* Letter body */
.sv-letter-body {
    padding: 1.5rem;
    font-size: .95rem; color: #3c4043;
    line-height: 1.9; white-space: pre-line;
}

/* External notes */
.sv-ext-note {
    margin: 0 1.5rem 1.25rem;
    background: #fef8e1;
    border: 1px solid #fde293;
    border-radius: 8px;
    padding: .875rem 1rem;
    font-size: .875rem; color: #5f4000;
    display: flex; align-items: flex-start; gap: .6rem;
}
.sv-ext-note i { color: #e37400; font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

/* Attachments */
.sv-att-section {
    padding: 1rem 1.5rem 1.5rem;
    border-top: 1px solid #f1f3f4;
}
.sv-att-title {
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: #80868b; margin-bottom: .75rem;
    display: flex; align-items: center; gap: .4rem;
}
.sv-att-grid {
    display: flex; flex-wrap: wrap; gap: .5rem;
}
.sv-att-chip {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .45rem .85rem;
    border: 1.5px solid #e2e8f0; border-radius: 100px;
    text-decoration: none; color: #3c4043;
    font-size: .8rem; font-weight: 600;
    transition: border-color .15s, background .15s;
    background: #fff;
    max-width: 200px; overflow: hidden;
}
.sv-att-chip:hover { border-color: #1a73e8; background: #e8f0fe; color: #1a73e8; }
.sv-att-chip i { font-size: .95rem; flex-shrink: 0; }
.sv-att-chip i.pdf { color: #d93025; }
.sv-att-chip i.img { color: #188038; }
.sv-att-chip i.doc { color: #1a73e8; }
.sv-att-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* ══ BOTTOM PANEL: Meta + Timeline in 2-col ══ */
.sv-panels {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.sv-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}
.sv-panel-header {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f1f3f4;
    display: flex; align-items: center; gap: .5rem;
    font-size: .82rem; font-weight: 700; color: #3c4043;
}
.sv-panel-header i { color: #1a73e8; }
.sv-panel-body { padding: 1rem 1.25rem; }

/* ── Timeline ── */
.sv-tl { position: relative; padding-left: 1.25rem; }
.sv-tl::before {
    content: ''; position: absolute;
    left: 5px; top: 6px; bottom: 6px;
    width: 2px; background: #e2e8f0; border-radius: 2px;
}
.sv-tl-item { position: relative; margin-bottom: 1.1rem; }
.sv-tl-item:last-child { margin-bottom: 0; }
.sv-tl-dot {
    position: absolute; left: -1.25rem; top: 4px;
    width: 12px; height: 12px; border-radius: 50%;
    border: 2px solid #fff; flex-shrink: 0;
}
.sv-tl-dot.blue   { background: #1a73e8; }
.sv-tl-dot.green  { background: #188038; }
.sv-tl-dot.orange { background: #e37400; }
.sv-tl-dot.purple { background: #7b2fa7; }
.sv-tl-dot.gray   { background: #80868b; }

.sv-tl-card {
    background: #f8f9fa; border: 1px solid #e9eef6;
    border-radius: 8px; padding: .7rem .875rem;
}
.sv-tl-card.note-card { background: #fff8f0; border-color: #fde293; }

.sv-tl-actor {
    font-size: .82rem; font-weight: 700; color: #202124;
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem; flex-wrap: wrap;
}
.sv-tl-time { font-size: .72rem; color: #80868b; font-weight: 400; }
.sv-tl-note { font-size: .82rem; color: #5f6368; margin-top: .3rem; line-height: 1.5; }
.sv-tl-mine {
    font-size: .6rem; font-weight: 700;
    background: #1a73e8; color: #fff;
    padding: .1rem .4rem; border-radius: 4px; text-transform: uppercase;
}
.sv-tl-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: .5rem; padding: 1.5rem 1rem; color: #9aa0a6;
    text-align: center;
}
.sv-tl-empty i { font-size: 1.75rem; color: #dadce0; }
.sv-tl-empty p { font-size: .82rem; margin: 0; }

/* ── Info panel items ── */
.sv-info-row {
    display: flex; align-items: flex-start;
    gap: .75rem; padding: .5rem 0;
    border-bottom: 1px solid #f1f3f4;
}
.sv-info-row:last-child { border-bottom: none; }
.sv-info-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: #e8f0fe; color: #1a73e8;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; flex-shrink: 0;
}
.sv-info-label { font-size: .7rem; color: #80868b; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
.sv-info-val   { font-size: .875rem; color: #202124; font-weight: 600; margin-top: 1px; }

/* ══ RESPONSIVE ══ */
@media (max-width: 640px) {
    .sv-body { padding: .6rem .6rem 2rem; }
    .sv-subject { font-size: 1.1rem; }
    .sv-letter-body { padding: 1rem; }
    .sv-meta-strip { grid-template-columns: repeat(2, 1fr); }
    .sv-meta-cell { padding: .6rem 1rem; }
    .sv-sender-row { padding: .75rem 1rem; }
    .sv-btn span { display: none; }
    .sv-btn { padding: .4rem .55rem; }
}
</style>
@endpush

@php
    $user = Auth::user(); $role = $user->role;
    $dispRecv = $letter->dispositions->sortByDesc('created_at')->first(fn($d) => $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id);

    $canDispose = false;
    if ($letter->status !== 'completed') {
        if (in_array($role, ['admin_sekretariat', 'admin_unit'])) {
            $canDispose = true;
        } elseif (in_array($role, ['bagian_tu', 'kepala_sekretariat'])) {
            if (in_array($letter->status, ['in_review_bagian_tu', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } elseif ($role === 'subag_persuratan') {
            if (in_array($letter->status, ['in_review_subag', 'in_consideration']) || ($dispRecv && $dispRecv->status === 'pending')) {
                $canDispose = true;
            }
        } else {
            if ($letter->to_unit_id == $user->unit_id && in_array($role, ['kepala_unit'])) {
                $canDispose = true;
            } elseif ($dispRecv && $dispRecv->status === 'pending' && in_array($role, ['kepala_unit', 'sub_unit'])) {
                $canDispose = true;
            }
        }
    }

    $isDispoToMyUnit = $dispRecv && $dispRecv->to_unit_id === $user->unit_id && is_null($dispRecv->to_user_id) && $dispRecv->status === 'pending';
    $hasAction = $letter->status !== 'completed' && (
        (in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status==='pending_approval')
        || ($role==='admin_unit' && $letter->status==='pending_sending')
        || (in_array($role, ['admin_unit', 'admin_sekretariat']) && $isDispoToMyUnit)
        || ($role==='admin_sekretariat' && $letter->status==='pending_agenda')
        || ($role==='subag_persuratan' && $letter->status==='in_review_subag')
        || ($role==='bagian_tu' && $letter->status==='in_review_bagian_tu')
        || ($dispRecv && $dispRecv->status==='pending')
        || $canDispose
    );

    $encodedId = \Vinkla\Hashids\Facades\Hashids::encode($letter->id);
    $isRespondingDispo = $dispRecv && $dispRecv->status === 'pending';
    $hideCatatanAndModifyDispo = in_array($role, ['admin_unit', 'admin_sekretariat']) && $isDispoToMyUnit;

    /* Sender / Recipient names */
    $isExt = in_array($letter->type, ['outbound_external', 'external']);
    if ($letter->type === 'outbound_external') {
        $senderName    = $letter->sender->name ?? 'Sistem';
        $recipientName = $letter->external_recipient_name ?? '—';
    } elseif ($letter->type === 'internal') {
        $senderName    = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
        $recipientName = $letter->recipientUnit->name ?? ($letter->recipientUser->name ?? '—');
    } else {
        $senderName    = $letter->external_sender_name ?? '—';
        $recipientName = 'Unit Tujuan (Sistem)';
    }

    $senderInitial = mb_strtoupper(mb_substr($senderName, 0, 1));

    /* Status badge class */
    $spClass = match($letter->status) {
        'draft'            => 'sp-draft',
        'pending_approval' => 'sp-pending',
        'completed'        => 'sp-done',
        'in_consideration' => 'sp-consider',
        default            => 'sp-process',
    };
@endphp

<div class="sv-root">

    {{-- ══ TOP ACTION BAR ══ --}}
    <div class="sv-bar">
        <div class="sv-bar-left">
            <a href="{{ url()->previous() }}" class="sv-back-btn" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="sv-bar-sep"></div>
        </div>

        <div class="sv-bar-right">
            {{-- Cetak --}}
            @if(in_array($role, ['admin_sekretariat', 'admin_unit']))
                <a href="{{ route('letters.printDisposition', ['letter' => $encodedId]) }}"
                   target="_blank" class="sv-btn sv-btn-outline">
                    <i class="bi bi-printer"></i>
                    <span>Cetak PDF</span>
                </a>
            @endif

            {{-- Catatan / Balas --}}
            @if(!in_array($letter->status, ['pending_approval', 'pending_sending', 'pending_agenda', 'draft']))
                @if(!$hideCatatanAndModifyDispo)
                    @if($isRespondingDispo)
                        <button class="sv-btn sv-btn-outline" data-bs-toggle="modal" data-bs-target="#acceptModal">
                            <i class="bi bi-reply"></i>
                            <span>Balas</span>
                        </button>
                    @else
                        <button class="sv-btn sv-btn-outline" data-bs-toggle="modal" data-bs-target="#replyModal">
                            <i class="bi bi-chat-left-text"></i>
                            <span>Catatan</span>
                        </button>
                    @endif
                @endif

                {{-- Disposisi / Teruskan --}}
                @if($canDispose)
                    <button class="sv-btn sv-btn-blue" data-bs-toggle="modal" data-bs-target="#dispoModal">
                        <i class="bi bi-forward-fill"></i>
                        <span>{{ $hideCatatanAndModifyDispo ? 'Teruskan' : 'Disposisi' }}</span>
                    </button>
                @else
                    <a href="{{ route('letters.create', ['forward' => $encodedId]) }}" class="sv-btn sv-btn-blue">
                        <i class="bi bi-forward-fill"></i>
                        <span>Teruskan</span>
                    </a>
                @endif
            @endif

            {{-- ACC Surat --}}
            @if(in_array($role, ['kepala_unit', 'subag_persuratan']) && $letter->status === 'pending_approval')
                <button class="sv-btn sv-btn-green"
                        onclick="event.preventDefault(); document.getElementById('formApprove').submit();">
                    <i class="bi bi-check2-circle"></i>
                    <span>ACC Surat</span>
                </button>
            @endif

            {{-- Kirim Fisik --}}
            @if(in_array($role, ['admin_unit', 'admin_sekretariat']) && $letter->status === 'pending_sending')
                <button class="sv-btn sv-btn-green"
                        onclick="event.preventDefault(); document.getElementById('formSendFinal').submit();">
                    <i class="bi bi-send-fill"></i>
                    <span>Kirim Fisik</span>
                </button>
            @endif

            {{-- Beri Agenda --}}
            @if(($role==='admin_sekretariat' || $role==='admin_unit') && $letter->status==='pending_agenda' && $letter->to_unit_id === $user->unit_id)
                <button class="sv-btn sv-btn-amber" data-bs-toggle="modal" data-bs-target="#agendaModal">
                    <i class="bi bi-journal-plus"></i>
                    <span>Beri Agenda</span>
                </button>
            @endif

            {{-- Arsipkan --}}
            @if(in_array($role, ['admin_sekretariat', 'admin_unit']) && $letter->status !== 'completed' && !in_array($letter->status, ['draft', 'pending_approval', 'pending_sending']))
                <button class="sv-btn sv-btn-outline"
                        onclick="if(confirm('Yakin ingin mengarsipkan surat ini?')) document.getElementById('formComplete').submit();">
                    <i class="bi bi-archive-fill"></i>
                    <span>Arsipkan</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ══ BODY ══ --}}
    <div class="sv-body">

        {{-- ── EMAIL CARD ── --}}
        <div class="sv-email-card">

            {{-- Subject + Status --}}
            <div class="sv-subject-bar">
                <div class="sv-subject">{{ $letter->subject }}</div>
                <span class="sv-status-pill {{ $spClass }}">
                    <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                    {{ $letter->status_label }}
                </span>
            </div>

            {{-- Sender header --}}
            <div class="sv-sender-row">
                <div class="sv-s-avatar {{ $isExt ? 'sv-s-av-ext' : 'sv-s-av-int' }}">
                    {{ $senderInitial }}
                </div>
                <div class="sv-s-info">
                    <div class="sv-s-name">{{ $senderName }}</div>
                    <div class="sv-s-meta">
                        <span><i class="bi bi-arrow-right-short"></i> {{ $recipientName }}</span>
                        @if($letter->letter_number)
                            <span><i class="bi bi-hash"></i> {{ $letter->letter_number }}</span>
                        @endif
                        @if($letter->agenda_number)
                            <span><i class="bi bi-journals"></i> Agenda: {{ $letter->agenda_number }}</span>
                        @endif
                    </div>
                </div>
                <div class="sv-s-date">
                    {{ $letter->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                </div>
            </div>

            {{-- Meta strip --}}
            <div class="sv-meta-strip">
                <div class="sv-meta-cell">
                    <div class="sv-m-label">No. Surat</div>
                    <div class="sv-m-value {{ !$letter->letter_number ? 'empty' : '' }}">
                        {{ $letter->letter_number ?: 'Belum ada' }}
                    </div>
                </div>
                <div class="sv-meta-cell">
                    <div class="sv-m-label">No. Agenda</div>
                    <div class="sv-m-value {{ !$letter->agenda_number ? 'empty' : '' }}">
                        {{ $letter->agenda_number ?: 'Belum diisi' }}
                    </div>
                </div>
                <div class="sv-meta-cell">
                    <div class="sv-m-label">Pengirim</div>
                    <div class="sv-m-value">{{ $senderName }}</div>
                </div>
                <div class="sv-meta-cell">
                    <div class="sv-m-label">Tujuan</div>
                    <div class="sv-m-value">{{ $recipientName }}</div>
                </div>
            </div>

            {{-- Letter body --}}
            <div class="sv-letter-body">{{ $letter->body }}</div>

            {{-- External notes --}}
            @if($letter->external_notes)
                <div class="sv-ext-note">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <div style="font-weight:700; margin-bottom:.25rem;">Catatan Eksternal</div>
                        {{ $letter->external_notes }}
                    </div>
                </div>
            @endif

            {{-- Attachments --}}
            @if($letter->attachments->count() > 0)
                <div class="sv-att-section">
                    <div class="sv-att-title">
                        <i class="bi bi-paperclip"></i>
                        {{ $letter->attachments->count() }} Lampiran
                    </div>
                    <div class="sv-att-grid">
                        @foreach($letter->attachments as $att)
                            @php
                                $url  = asset('storage/' . $att->file_path);
                                $name = basename($att->file_path);
                                $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                $icon = $ext === 'pdf' ? 'pdf' : (in_array($ext, ['jpg','jpeg','png','gif','webp']) ? 'img' : 'doc');
                                $iconClass = $icon === 'pdf' ? 'bi-file-pdf-fill pdf' : ($icon === 'img' ? 'bi-file-image-fill img' : 'bi-file-word-fill doc');
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="sv-att-chip" title="{{ $name }}">
                                <i class="bi {{ $iconClass }}"></i>
                                <span>{{ $name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- /.sv-email-card --}}

        {{-- ── BOTTOM PANELS ── --}}
        <div class="sv-panels">


            {{-- Timeline Panel --}}
            <div class="sv-panel">
                <div class="sv-panel-header">
                    <i class="bi bi-clock-history"></i> Riwayat & Catatan
                </div>
                <div class="sv-panel-body">
                    @php
                        $histories = $letter->histories->whereIn('action', ['agenda_assigned', 'disposed', 'replied'])->sortBy('created_at');
                    @endphp
                    @if($histories->isNotEmpty())
                        <div class="sv-tl">
                            @foreach($histories as $h)
                                @if($h->action === 'agenda_assigned')
                                    <div class="sv-tl-item">
                                        <div class="sv-tl-dot blue"></div>
                                        <div class="sv-tl-card">
                                            <div class="sv-tl-actor">
                                                <span>{{ $h->user->role === 'admin_sekretariat' ? 'Subag Persuratan' : ($h->user->unit->name ?? 'Admin Unit') }}</span>
                                                <span class="sv-tl-time">{{ $h->created_at->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="sv-tl-note">Mengagendakan surat</div>
                                        </div>
                                    </div>

                                @elseif($h->action === 'disposed' && !Str::contains($h->note, 'Diteruskan kepada personal terkait di unit'))
                                    @php
                                        $dispMatch  = $letter->dispositions->where('from_user_id', $h->user_id)->where('note', $h->note)->first();
                                        $targetName = $dispMatch->toUser->name ?? ($dispMatch->unit->name ?? '—');
                                    @endphp
                                    <div class="sv-tl-item">
                                        <div class="sv-tl-dot blue"></div>
                                        <div class="sv-tl-card">
                                            <div class="sv-tl-actor">
                                                <span>
                                                    Ke: {{ $targetName }}
                                                    @if($h->user_id === Auth::id())
                                                        <span class="sv-tl-mine">Anda</span>
                                                    @endif
                                                </span>
                                                <span class="sv-tl-time">{{ $h->created_at->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="sv-tl-note">
                                                Oleh {{ $h->user->name ?? 'User' }} —
                                                "{{ preg_replace('/^\[.*?\]\s*/', '', $h->note) }}"
                                            </div>
                                        </div>
                                    </div>

                                @elseif($h->action === 'replied')
                                    <div class="sv-tl-item">
                                        <div class="sv-tl-dot orange"></div>
                                        <div class="sv-tl-card note-card">
                                            <div class="sv-tl-actor">
                                                <span>
                                                    {{ $h->user->name ?? 'User' }}
                                                    @if($h->user_id === Auth::id())
                                                        <span class="sv-tl-mine">Anda</span>
                                                    @endif
                                                </span>
                                                <span class="sv-tl-time">{{ $h->created_at->format('d M, H:i') }}</span>
                                            </div>
                                            <div class="sv-tl-note">"{{ $h->note }}"</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="sv-tl-empty">
                            <i class="bi bi-clock-history"></i>
                            <p>Belum ada riwayat tercatat untuk surat ini.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>{{-- /.sv-panels --}}

    </div>{{-- /.sv-body --}}

</div>{{-- /.sv-root --}}

{{-- ══ HIDDEN FORMS ══ --}}
<form id="formApprove"  action="{{ route('letters.approve', $letter) }}"   method="POST" style="display:none;">@csrf</form>
<form id="formSendFinal" action="{{ route('letters.sendFinal', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formForwardTU" action="{{ route('letters.forwardToBagianTu', $letter) }}" method="POST" style="display:none;">@csrf</form>
<form id="formComplete" action="{{ route('letters.complete', $letter) }}"  method="POST" style="display:none;">@csrf</form>

{{-- ══ MODALS ══ --}}
<style>
.modal-content { border-radius: 14px; border: none; box-shadow: 0 12px 48px rgba(0,0,0,.14); }
.modal-header  { border-bottom: 1px solid #e9eef6; padding: 1.1rem 1.4rem; }
.modal-body    { padding: 1.25rem 1.4rem; }
.modal-footer  { border-top: 1px solid #e9eef6; padding: .875rem 1.4rem; }
.form-control, .form-select {
    border: 1.5px solid #e2e8f0; border-radius: .65rem;
    font-size: .875rem; padding: .55rem .85rem;
}
.form-control:focus, .form-select:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26,115,232,.1);
}
</style>

{{-- Modal Disposisi --}}
@if($canDispose)
<div class="modal fade" id="dispoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:.95rem;">
                    <i class="bi bi-forward-fill text-primary me-2"></i>
                    {{ $hideCatatanAndModifyDispo ? 'Teruskan Surat' : 'Buat Disposisi Baru' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.store', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($hideCatatanAndModifyDispo)
                        <input type="hidden" name="recipient_type" value="user">
                    @else
                    <div class="d-flex gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUnit" value="unit" checked>
                            <label class="form-check-label fw-bold" for="typeUnit">Ke Unit Kerja</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recipient_type" id="typeUser" value="user">
                            <label class="form-check-label fw-bold" for="typeUser">Ke Personal</label>
                        </div>
                    </div>
                    @endif

                    @if(!$hideCatatanAndModifyDispo)
                    <div class="mb-3" id="selectUnit">
                        <select name="to_unit_id" class="form-select">
                            <option value="">— Pilih Unit Kerja —</option>
                            @foreach(\App\Models\Unit::all() as $unit)
                                @if($unit->id !== $user->unit_id)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3" id="selectUser"
                         style="{{ $hideCatatanAndModifyDispo ? '' : 'display:none;' }}">
                        <select name="to_user_id" class="form-select"
                                {{ $hideCatatanAndModifyDispo ? 'required' : '' }}>
                            <option value="">— Pilih Pegawai di Unit Anda —</option>
                            @php $myUnit = \App\Models\Unit::with('organs.users')->find($user->unit_id); @endphp
                            @if($myUnit && $myUnit->organs->isNotEmpty())
                                @foreach($myUnit->organs as $organ)
                                    @foreach($organ->users as $u)
                                        @if($u->id !== $user->id)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                    </div>

                    @if($hideCatatanAndModifyDispo)
                        <input type="hidden" name="note" value="Diteruskan kepada personal terkait di unit">
                    @else
                        <textarea name="note" class="form-control" rows="3"
                                  placeholder="Tulis instruksi disposisi..." required></textarea>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill"></i> Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Agenda --}}
@if(($role==='admin_sekretariat' || $role==='admin_unit') && $letter->status==='pending_agenda' && $letter->to_unit_id === $user->unit_id)
<div class="modal fade" id="agendaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:.95rem;">
                    <i class="bi bi-journal-plus text-warning me-2"></i> Beri Nomor Agenda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.agenda', $letter) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3" style="background:#eef2ff; color:#4f46e5; border:1px solid #c7d2fe; border-radius:8px; font-size:0.85rem; font-weight:600;">
                        <i class="bi bi-info-circle-fill me-1"></i> Nomor agenda akan di-generate secara otomatis sesuai unit dan tahun.
                    </div>
                    @if($role === 'admin_unit')
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Teruskan ke Personal Unit</label>
                        <select name="to_user_id" class="form-select" required>
                            <option value="">— Pilih Pegawai —</option>
                            @php $myUnit2 = \App\Models\Unit::with('organs.users')->find($user->unit_id); @endphp
                            @if($myUnit2 && $myUnit2->organs->isNotEmpty())
                                @foreach($myUnit2->organs as $organ)
                                    @foreach($organ->users as $u)
                                        @if($u->id !== $user->id)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                    </div>
                    @endif
                    <textarea name="note" class="form-control" rows="2"
                              placeholder="Catatan (Opsional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi {{ $role === 'admin_unit' ? 'bi-send-fill' : 'bi-save' }}"></i>
                        {{ $role === 'admin_unit' ? 'Simpan & Teruskan' : 'Simpan Agenda' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Catatan --}}
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:.95rem;">
                    <i class="bi bi-chat-left-text text-secondary me-2"></i> Tambahkan Catatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.reply', $letter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <textarea name="response_note" class="form-control mb-3" rows="3"
                              placeholder="Isi catatan Anda..." required></textarea>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tanggapi Disposisi --}}
@if($dispRecv)
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:.95rem;">
                    <i class="bi bi-reply-fill text-primary me-2"></i> Tanggapi Disposisi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('letters.dispositions.respond', $dispRecv->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <select name="action" class="form-select mb-3" required>
                        <option value="">— Pilih Status —</option>
                        <option value="accepted">Selesai / Dikerjakan</option>
                        <option value="pertimbangan">Butuh Pertimbangan</option>
                        <option value="followup">Akan Ditindaklanjuti</option>
                        <option value="rejected">Tolak</option>
                    </select>
                    <textarea name="response_note" class="form-control mb-3" rows="3"
                              placeholder="Catatan hasil..." required></textarea>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ── Dispo modal: toggle unit/personal ── */
    const sU = document.getElementById('selectUnit');
    const sP = document.getElementById('selectUser');

    document.getElementsByName('recipient_type').forEach(r => r.addEventListener('change', () => {
        const isUser = document.getElementById('typeUser')?.checked;
        if (sU) sU.style.display = isUser ? 'none' : 'block';
        if (sP) sP.style.display = isUser ? 'block' : 'none';
        if (isUser) {
            sU?.querySelector('select')?.removeAttribute('required');
            sP?.querySelector('select')?.setAttribute('required', 'required');
        } else {
            sP?.querySelector('select')?.removeAttribute('required');
            sU?.querySelector('select')?.setAttribute('required', 'required');
        }
    }));
});
</script>
@endpush

@endsection
