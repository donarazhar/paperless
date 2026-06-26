@extends('layouts.mailbox')
@section('title', 'Kotak Keluar')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   OUTBOX — Gmail-Modified Design
═══════════════════════════════════════════ */

.ob-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.ob-toolbar {
    display: flex;
    align-items: center;
    gap: .35rem;
    padding: .55rem .875rem;
    border-bottom: 1px solid #e9eef6;
    background: #fff;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 20;
}
.ob-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.ob-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.ob-chk {
    width: 16px; height: 16px;
    accent-color: #188038;
    cursor: pointer; flex-shrink: 0;
}
.ob-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.ob-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.ob-tb-btn:hover { background: #f1f3f4; color: #202124; }

.ob-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.ob-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.ob-pg-btn:hover { background: #f1f3f4; }
.ob-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ FILTER TABS ══ */
.ob-tabs {
    display: flex;
    border-bottom: 1px solid #e9eef6;
    background: #fff;
    flex-shrink: 0;
    overflow-x: auto;
    scrollbar-width: none;
}
.ob-tabs::-webkit-scrollbar { display: none; }

.ob-tab {
    display: flex; align-items: center; gap: .35rem;
    padding: .6rem 1.1rem;
    font-size: .795rem; font-weight: 600;
    color: #5f6368;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    text-decoration: none;
    transition: color .15s, background .15s;
    cursor: pointer;
    letter-spacing: .01em;
}
.ob-tab:hover  { color: #188038; background: #f0faf4; }
.ob-tab.active { color: #188038; border-bottom-color: #188038; }

.ob-tab-badge {
    font-size: .6rem; font-weight: 700;
    padding: .1rem .35rem; border-radius: 10px;
    min-width: 16px; text-align: center; line-height: 1.4;
}
.ob-tab-badge.green  { background: #e6f4ea; color: #137333; }
.ob-tab-badge.amber  { background: #fef3c7; color: #78350f; }
.ob-tab-badge.muted  { background: #e8eaed; color: #5f6368; }

/* ══ MAIL LIST ══ */
.ob-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.ob-list::-webkit-scrollbar { width: 5px; }
.ob-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.ob-row {
    display: flex;
    align-items: center;
    padding: 0 .875rem;
    height: 54px;
    border-bottom: 1px solid #f1f3f4;
    position: relative;
    cursor: pointer;
    gap: .6rem;
    overflow: hidden;
    background: #fff;
    transition: background .1s, box-shadow .1s;
    text-decoration: none; color: inherit;
}

/* Status-based accent line */
.ob-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; border-radius: 0 2px 2px 0;
    background: transparent;
}
.ob-row.st-pending_approval::before { background: #e37400; }
.ob-row.st-pending_sending::before  { background: #1a73e8; }
.ob-row.st-pending_agenda::before   { background: #7b2fa7; }
.ob-row.st-completed::before        { background: #188038; }
.ob-row.st-in_consideration::before { background: #6366f1; }

.ob-row:hover {
    background: #f8faf8;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.ob-row:hover .ob-hover-actions { opacity: 1; pointer-events: auto; }
.ob-row:hover .ob-date { opacity: 0; }

/* ── Checkbox ── */
.ob-chk-row {
    width: 15px; height: 15px;
    accent-color: #188038;
    cursor: pointer; flex-shrink: 0;
    opacity: 0; transition: opacity .12s;
}
.ob-row:hover .ob-chk-row,
.ob-chk-row:checked { opacity: 1; }

/* ── Avatar ── */
.ob-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
}
.av-green { background: #e6f4ea; color: #137333; }
.av-ext   { background: #fce8f3; color: #9c1c6a; }

/* ── "Ke:" label ── */
.ob-to {
    width: 175px; flex-shrink: 0;
    font-size: .875rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #3c4043;
}
.ob-to-label {
    font-size: .65rem; font-weight: 700;
    color: #80868b; text-transform: uppercase;
    letter-spacing: .06em; margin-right: .3rem;
}

/* ── Middle content ── */
.ob-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .45rem;
    overflow: hidden;
}
.ob-badges { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
.ob-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}
/* Per-status badge colors */
.ob-badge.ext       { background: #fce8f3; color: #9c1c6a; border: 1px solid #f8bbd9; }
.ob-badge.agenda    { background: #d2e3fc; color: #1557b0; border: 1px solid #a8c7fa; }
.ob-badge.approval  { background: #fef3c7; color: #78350f; border: 1px solid #fde293; }
.ob-badge.sending   { background: #d2e3fc; color: #1557b0; border: 1px solid #a8c7fa; }
.ob-badge.agenda-rv { background: #f3e8fd; color: #7b2fa7; border: 1px solid #d9b3f9; }
.ob-badge.consider  { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
.ob-badge.done      { background: #e6f4ea; color: #137333; border: 1px solid #a8d5b5; }
.ob-badge.default   { background: #f1f3f4; color: #5f6368; border: 1px solid #dadce0; }

.ob-subject {
    font-size: .875rem; font-weight: 500;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #3c4043; flex-shrink: 0; max-width: 240px;
}
.ob-sep  { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.ob-snip {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Date ── */
.ob-date {
    font-size: .78rem; color: #80868b; font-weight: 400;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}

/* ── Hover quick-actions ── */
.ob-hover-actions {
    display: flex; align-items: center; gap: .2rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.ob-act-btn {
    width: 30px; height: 30px;
    border: none; background: #fff;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; color: #5f6368;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
    transition: background .12s, color .12s, transform .12s;
    text-decoration: none;
}
.ob-act-btn:hover {
    background: #e6f4ea; color: #188038;
    transform: scale(1.08);
}

/* ══ EMPTY STATE ══ */
.ob-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 4rem 2rem;
    height: 100%; gap: 1rem;
}
.ob-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #e6f4ea;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #188038;
}
.ob-empty h3 { font-size: 1.05rem; font-weight: 700; color: #3c4043; margin: 0; }
.ob-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .ob-row {
        height: auto;
        padding: .8rem .875rem;
        display: grid;
        grid-template-columns: 36px 1fr auto;
        grid-template-areas:
            "avatar to   date"
            "avatar mid  mid";
        gap: 3px 10px;
        align-items: start;
    }
    .ob-chk-row { display: none; }
    .ob-avatar  { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .ob-to      { grid-area: to; width: 100%; font-size: .9rem; }
    .ob-date    { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .ob-mid     { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .ob-subject { max-width: 100%; width: 100%; font-size: .835rem; }
    .ob-sep     { display: none; }
    .ob-snip    { display: block; width: 100%; font-size: .8rem; }
    .ob-hover-actions { display: none !important; }
    .ob-tab     { padding: .5rem .75rem; font-size: .75rem; }
    .ob-toolbar { padding: .45rem .75rem; }
    .ob-row:hover .ob-date { opacity: 1; }
}
</style>
@endpush

@php
    $filterStatus = request('status', 'all');
    $waitCount = $letters->whereIn('status', ['pending_approval','pending_sending','pending_agenda','in_consideration'])->count();
    $sentCount  = $letters->whereIn('status', ['completed'])->count();
@endphp

<div class="ob-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="ob-toolbar">
        <div class="ob-toolbar-left">
            <input type="checkbox" class="ob-chk" id="checkAll" title="Pilih Semua">
            <div class="ob-tb-sep"></div>
            <button class="ob-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="ob-toolbar-right">
            <span class="ob-pg-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada surat
                @endif
            </span>
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="ob-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="ob-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ══ FILTER TABS ══ --}}
    <div class="ob-tabs">
        <a href="{{ route('letters.outbound', ['status' => 'all']) }}"
           class="ob-tab {{ $filterStatus === 'all' ? 'active' : '' }}">
            <i class="bi bi-send"></i> Semua
            @if($letters->total() > 0)
                <span class="ob-tab-badge muted">{{ $letters->total() }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['status' => 'waiting']) }}"
           class="ob-tab {{ $filterStatus === 'waiting' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Menunggu
            @if($waitCount > 0)
                <span class="ob-tab-badge amber">{{ $waitCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['status' => 'completed']) }}"
           class="ob-tab {{ $filterStatus === 'completed' ? 'active' : '' }}">
            <i class="bi bi-check-circle-fill"></i> Terkirim
            @if($sentCount > 0)
                <span class="ob-tab-badge green">{{ $sentCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['type' => 'outbound_external']) }}"
           class="ob-tab {{ request('type') === 'outbound_external' ? 'active' : '' }}">
            <i class="bi bi-globe2"></i> Eksternal
        </a>
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="ob-list">
        @forelse($letters as $letter)
            @php
                $isExternal    = $letter->type === 'outbound_external';
                $showUrl       = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Pihak Luar')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial  = mb_strtoupper(mb_substr($recipientName, 0, 1));
                $rowClass = 'st-' . $letter->status;

                /* Badge class per status */
                $badgeClass = match($letter->status) {
                    'pending_approval' => 'approval',
                    'pending_sending'  => 'sending',
                    'pending_agenda'   => 'agenda-rv',
                    'in_consideration' => 'consider',
                    'completed'        => 'done',
                    default            => 'default',
                };
            @endphp

            <div class="ob-row {{ $rowClass }}"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="ob-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Avatar --}}
                <div class="ob-avatar {{ $isExternal ? 'av-ext' : 'av-green' }}">{{ $initial }}</div>

                {{-- Recipient --}}
                <span class="ob-to">
                    <span class="ob-to-label">Ke:</span>{{ $recipientName }}
                </span>

                {{-- Middle: badges + subject + snippet --}}
                <div class="ob-mid">
                    <div class="ob-badges">
                        @if($isExternal)
                            <span class="ob-badge ext">Eksternal</span>
                        @endif
                        @if($letter->agenda_number)
                            <span class="ob-badge agenda"># {{ $letter->agenda_number }}</span>
                        @endif
                        <span class="ob-badge {{ $badgeClass }}">{{ $letter->status_label }}</span>
                    </div>
                    <span class="ob-subject">{{ $letter->subject }}</span>
                    <span class="ob-sep">–</span>
                    <span class="ob-snip">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="ob-date">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @elseif($letter->created_at->isCurrentYear())
                        {{ $letter->created_at->format('d M') }}
                    @else
                        {{ $letter->created_at->format('d/m/y') }}
                    @endif
                </span>

                {{-- Hover quick-action --}}
                <div class="ob-hover-actions" onclick="event.stopPropagation()">
                    <button class="ob-act-btn" title="Lihat Detail"
                            onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

            </div>
        @empty
            <div class="ob-empty">
                <div class="ob-empty-icon">
                    <i class="bi bi-send-fill"></i>
                </div>
                <h3>
                    @if($filterStatus === 'waiting') Tidak Ada Surat Menunggu
                    @elseif($filterStatus === 'completed') Belum Ada Surat Terkirim
                    @else Kotak Keluar Kosong
                    @endif
                </h3>
                <p>
                    @if($filterStatus === 'waiting')
                        Tidak ada surat yang sedang menunggu persetujuan atau pengiriman.
                    @else
                        Surat keluar dari unit Anda akan ditampilkan di sini.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

</div>{{-- /.ob-root --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const checks   = document.querySelectorAll('.mail-check');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checks.forEach(c => c.checked = this.checked);
        });
    }
});
</script>
@endpush

@endsection