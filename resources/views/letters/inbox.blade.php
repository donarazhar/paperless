@extends('layouts.mailbox')
@section('title', 'Kotak Masuk')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   INBOX — Gmail-Modified Design
═══════════════════════════════════════════ */

/* ── Root ── */
.ib-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.ib-toolbar {
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

.ib-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.ib-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.ib-chk {
    width: 16px; height: 16px;
    accent-color: #1a73e8;
    cursor: pointer; flex-shrink: 0;
}
.ib-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.ib-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.ib-tb-btn:hover { background: #f1f3f4; color: #202124; }

.ib-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.ib-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.ib-pg-btn:hover { background: #f1f3f4; }
.ib-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ FILTER TABS ══ */
.ib-tabs {
    display: flex;
    border-bottom: 1px solid #e9eef6;
    background: #fff;
    flex-shrink: 0;
    overflow-x: auto;
    scrollbar-width: none;
}
.ib-tabs::-webkit-scrollbar { display: none; }

.ib-tab {
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
.ib-tab:hover { color: #1a73e8; background: #f8faff; }
.ib-tab.active {
    color: #1a73e8;
    border-bottom-color: #1a73e8;
}
.ib-tab-badge {
    font-size: .6rem; font-weight: 700;
    padding: .1rem .35rem; border-radius: 10px;
    min-width: 16px; text-align: center; line-height: 1.4;
}
.ib-tab-badge.primary { background: #1a73e8; color: #fff; }
.ib-tab-badge.muted   { background: #e8eaed; color: #5f6368; }

/* ══ MAIL LIST ══ */
.ib-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.ib-list::-webkit-scrollbar { width: 5px; }
.ib-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.ib-row {
    display: flex;
    align-items: center;
    padding: 0 .875rem;
    height: 54px;
    border-bottom: 1px solid #f1f3f4;
    position: relative;
    cursor: pointer;
    gap: .6rem;
    overflow: hidden;
    transition: box-shadow .1s;
    text-decoration: none; color: inherit;
}

/* Unread accent line */
.ib-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px;
    background: transparent;
    transition: background .15s;
    border-radius: 0 2px 2px 0;
}
.ib-row.unread { background: #fff; }
.ib-row.unread::before { background: #1a73e8; }
.ib-row.read { background: #f8f9fa; }

.ib-row:hover {
    background: #f1f3f4;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.ib-row:hover .ib-hover-actions { opacity: 1; pointer-events: auto; }
.ib-row:hover .ib-date { opacity: 0; }

/* ── Checkbox ── */
.ib-chk-row {
    width: 15px; height: 15px;
    accent-color: #1a73e8;
    cursor: pointer; flex-shrink: 0;
    opacity: 0;
    transition: opacity .12s;
}
.ib-row:hover .ib-chk-row,
.ib-chk-row:checked { opacity: 1; }

/* ── Star ── */
.ib-star {
    font-size: .95rem; flex-shrink: 0;
    color: #dadce0; cursor: pointer;
    transition: color .15s, transform .15s;
    background: none; border: none; padding: 0;
    line-height: 1;
}
.ib-star:hover { color: #f29900; transform: scale(1.2); }
.ib-star.starred { color: #f29900; }

/* ── Avatar ── */
.ib-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
    letter-spacing: 0;
}
.av-int { background: #d2e3fc; color: #1557b0; }
.av-ext { background: #fce8f3; color: #9c1c6a; }

/* ── Sender ── */
.ib-sender {
    width: 175px; flex-shrink: 0;
    font-size: .875rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #3c4043; font-weight: 400;
}
.ib-row.unread .ib-sender { font-weight: 700; color: #202124; }

/* ── Middle content ── */
.ib-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .5rem;
    overflow: hidden;
}
.ib-subject {
    font-size: .875rem; font-weight: 400;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #3c4043; flex-shrink: 0; max-width: 240px;
}
.ib-row.unread .ib-subject { font-weight: 700; color: #202124; }
.ib-sep { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.ib-snippet {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Badges ── */
.ib-badges {
    display: flex; align-items: center; gap: .25rem; flex-shrink: 0;
}
.ib-badge {
    display: inline-flex; align-items: center;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}
.ib-badge.ext     { background: #fce8f3; color: #9c1c6a; border: 1px solid #f8bbd9; }
.ib-badge.agenda  { background: #d2e3fc; color: #1557b0; border: 1px solid #a8c7fa; }
.ib-badge.pending { background: #fce8e6; color: #c5221f; border: 1px solid #f5c6c6; }
.ib-badge.done    { background: #e6f4ea; color: #137333; border: 1px solid #a8d5b5; }
.ib-badge.default { background: #f1f3f4; color: #5f6368; border: 1px solid #dadce0; }
.ib-badge.draft   { background: #fef7e0; color: #7d4b00; border: 1px solid #fde293; }

/* ── Date ── */
.ib-date {
    font-size: .78rem; color: #80868b; font-weight: 400;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}
.ib-row.unread .ib-date { color: #202124; font-weight: 700; }

/* ── Hover quick-actions (overlay on date) ── */
.ib-hover-actions {
    display: flex; align-items: center; gap: .2rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.ib-act-btn {
    width: 30px; height: 30px;
    border: none; background: #fff;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; color: #5f6368;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
    transition: background .12s, color .12s, transform .12s;
}
.ib-act-btn:hover {
    background: #d2e3fc; color: #1a73e8;
    transform: scale(1.08);
}

/* ══ EMPTY STATE ══ */
.ib-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
    padding: 4rem 2rem;
    height: 100%;
    gap: 1rem;
}
.ib-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #e8f0fe;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #1a73e8;
}
.ib-empty h3 { font-size: 1.1rem; font-weight: 700; color: #3c4043; margin: 0; }
.ib-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .ib-row {
        height: auto;
        padding: .8rem .875rem;
        display: grid;
        grid-template-columns: 36px 1fr auto;
        grid-template-areas:
            "avatar sender  date"
            "avatar mid     mid";
        gap: 3px 10px;
        align-items: start;
    }
    .ib-chk-row { display: none; }
    .ib-star    { display: none; }
    .ib-avatar  { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .ib-sender  { grid-area: sender; width: 100%; font-size: .9rem; }
    .ib-date    { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .ib-mid     { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .ib-subject { max-width: 100%; width: 100%; font-size: .835rem; }
    .ib-sep     { display: none; }
    .ib-snippet { display: block; width: 100%; font-size: .8rem; }
    .ib-hover-actions { display: none !important; }
    .ib-tab     { padding: .5rem .75rem; font-size: .75rem; }
    .ib-row.unread .ib-date { opacity: 1; }
    .ib-toolbar { padding: .45rem .75rem; }
}

@media (max-width: 480px) {
    .ib-sender { width: 100%; }
    .ib-subject { font-size: .82rem; }
}
</style>
@endpush

@php
    $filterType  = request('type', 'all');
    $unreadCount = $letters->where('is_unread', true)->count();
@endphp

<div class="ib-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="ib-toolbar">
        <div class="ib-toolbar-left">
            <input type="checkbox" class="ib-chk" id="checkAll" title="Pilih Semua">
            <div class="ib-tb-sep"></div>
            <button class="ib-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="ib-toolbar-right">
            <span class="ib-pg-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada surat
                @endif
            </span>
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="ib-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="ib-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ══ FILTER TABS ══ --}}
    <div class="ib-tabs">
        <a href="{{ route('letters.inbound', ['type' => 'all']) }}"
           class="ib-tab {{ $filterType === 'all' ? 'active' : '' }}">
            <i class="bi bi-inbox"></i> Semua
            @if($letters->total() > 0)
                <span class="ib-tab-badge muted">{{ $letters->total() }}</span>
            @endif
        </a>
        <a href="{{ route('letters.inbound', ['type' => 'unread']) }}"
           class="ib-tab {{ $filterType === 'unread' ? 'active' : '' }}">
            <i class="bi bi-record-circle" style="font-size:.55rem;"></i> Belum Dibaca
            @if($unreadCount > 0)
                <span class="ib-tab-badge primary">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.inbound', ['type' => 'internal']) }}"
           class="ib-tab {{ $filterType === 'internal' ? 'active' : '' }}">
            <i class="bi bi-building"></i> Internal
        </a>
        <a href="{{ route('letters.inbound', ['type' => 'external']) }}"
           class="ib-tab {{ $filterType === 'external' ? 'active' : '' }}">
            <i class="bi bi-globe2"></i> Eksternal
        </a>
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="ib-list" id="mailList">
        @forelse($letters as $letter)
            @php
                $isUnread   = $letter->is_unread;
                $isExternal = $letter->type === 'external';
                $showUrl    = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);

                $senderName = $isExternal
                    ? ($letter->external_sender_name ?? 'Unknown')
                    : ($letter->sender->unit->name ?? ($letter->sender->name ?? 'Unknown'));

                $initial = mb_strtoupper(mb_substr($senderName, 0, 1));

                $badgeClass = match($letter->status) {
                    'pending_approval' => 'pending',
                    'sent', 'completed' => 'done',
                    'draft' => 'draft',
                    default => 'default',
                };
            @endphp

            <div class="ib-row {{ $isUnread ? 'unread' : 'read' }}"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="ib-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Star (visual only) --}}
                <button class="ib-star" onclick="event.stopPropagation(); this.classList.toggle('starred')" title="Tandai">
                    <i class="bi bi-star{{ '' }}"></i>
                </button>

                {{-- Avatar --}}
                <div class="ib-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">{{ $initial }}</div>

                {{-- Sender --}}
                <span class="ib-sender">{{ $senderName }}</span>

                {{-- Middle: badges + subject + snippet --}}
                <div class="ib-mid">
                    {{-- Badges --}}
                    <div class="ib-badges">
                        @if($isExternal)
                            <span class="ib-badge ext">Eksternal</span>
                        @endif
                        @if($letter->agenda_number)
                            <span class="ib-badge agenda"># {{ $letter->agenda_number }}</span>
                        @endif
                        <span class="ib-badge {{ $badgeClass }}">{{ $letter->status_label }}</span>
                    </div>
                    <span class="ib-subject">{{ $letter->subject }}</span>
                    <span class="ib-sep">–</span>
                    <span class="ib-snippet">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="ib-date">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @elseif($letter->created_at->isCurrentYear())
                        {{ $letter->created_at->format('d M') }}
                    @else
                        {{ $letter->created_at->format('d/m/y') }}
                    @endif
                </span>

                {{-- Hover quick-actions --}}
                <div class="ib-hover-actions" onclick="event.stopPropagation()">
                    <button class="ib-act-btn" title="Buka Surat"
                            onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-envelope-open"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="ib-empty">
                <div class="ib-empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>Kotak Masuk Kosong</h3>
                <p>Tidak ada surat yang masuk saat ini. Semua surat yang dikirimkan kepada Anda akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

</div>{{-- /.ib-root --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Select-all checkbox ── */
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.mail-check');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowChecks.forEach(c => c.checked = this.checked);
        });
    }

    /* ── Mark row as read on click (optimistic UI) ── */
    document.querySelectorAll('.ib-row').forEach(function (row) {
        row.addEventListener('click', function () {
            row.classList.remove('unread');
            row.classList.add('read');
        });
    });

    /* ── Star toggle icon update ── */
    document.querySelectorAll('.ib-star').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (this.classList.contains('starred')) {
                icon.className = 'bi bi-star-fill';
            } else {
                icon.className = 'bi bi-star';
            }
        });
    });

});
</script>
@endpush

@endsection