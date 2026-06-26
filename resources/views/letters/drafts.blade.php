@extends('layouts.mailbox')
@section('title', 'Draft Surat')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   DRAFTS — Gmail-Modified Design
═══════════════════════════════════════════ */

.dr-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.dr-toolbar {
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
.dr-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.dr-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.dr-chk {
    width: 16px; height: 16px;
    accent-color: #e37400;
    cursor: pointer; flex-shrink: 0;
}
.dr-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.dr-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.dr-tb-btn:hover { background: #f1f3f4; color: #202124; }

/* Compose shortcut in toolbar */
.dr-compose-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: #1a73e8; color: #fff;
    border: none; border-radius: 100px;
    padding: .38rem .95rem; font-size: .78rem; font-weight: 700;
    cursor: pointer; transition: background .15s, box-shadow .15s, transform .15s;
    text-decoration: none; flex-shrink: 0;
    box-shadow: 0 1px 4px rgba(26,115,232,.3);
}
.dr-compose-btn:hover {
    background: #1557b0; color: #fff;
    box-shadow: 0 2px 8px rgba(26,115,232,.4);
    transform: translateY(-1px);
}

.dr-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.dr-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.dr-pg-btn:hover { background: #f1f3f4; }
.dr-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ FILTER TABS ══ */
.dr-tabs {
    display: flex;
    border-bottom: 1px solid #e9eef6;
    background: #fff;
    flex-shrink: 0;
    overflow-x: auto;
    scrollbar-width: none;
}
.dr-tabs::-webkit-scrollbar { display: none; }

.dr-tab {
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
.dr-tab:hover { color: #e37400; background: #fff8f0; }
.dr-tab.active {
    color: #e37400;
    border-bottom-color: #e37400;
}

.dr-tab-badge {
    font-size: .6rem; font-weight: 700;
    padding: .1rem .35rem; border-radius: 10px;
    min-width: 16px; text-align: center; line-height: 1.4;
}
.dr-tab-badge.amber  { background: #fef3c7; color: #78350f; }
.dr-tab-badge.red    { background: #fce8e6; color: #c5221f; }
.dr-tab-badge.muted  { background: #e8eaed; color: #5f6368; }

/* ══ MAIL LIST ══ */
.dr-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.dr-list::-webkit-scrollbar { width: 5px; }
.dr-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.dr-row {
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

/* Status accent line */
.dr-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; border-radius: 0 2px 2px 0;
    background: transparent;
}
.dr-row.is-draft::before   { background: #e37400; }
.dr-row.is-pending::before { background: #c5221f; }

.dr-row:hover {
    background: #fafaf8;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.dr-row:hover .dr-hover-actions { opacity: 1; pointer-events: auto; }
.dr-row:hover .dr-date { opacity: 0; }

/* ── Checkbox ── */
.dr-chk-row {
    width: 15px; height: 15px;
    accent-color: #e37400;
    cursor: pointer; flex-shrink: 0;
    opacity: 0; transition: opacity .12s;
}
.dr-row:hover .dr-chk-row,
.dr-chk-row:checked { opacity: 1; }

/* ── Avatar ── */
.dr-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
}
.av-amber { background: #fef3c7; color: #78350f; }
.av-ext   { background: #fce8f3; color: #9c1c6a; }

/* ── "Ke:" label ── */
.dr-to {
    width: 175px; flex-shrink: 0;
    font-size: .875rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #3c4043;
}
.dr-to-label {
    font-size: .65rem; font-weight: 700;
    color: #80868b; text-transform: uppercase;
    letter-spacing: .06em; margin-right: .3rem;
}

/* ── Middle content ── */
.dr-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .45rem;
    overflow: hidden;
}
.dr-badges { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
.dr-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}
.dr-badge.draft   { background: #fef3c7; color: #78350f; border: 1px solid #fde293; }
.dr-badge.pending { background: #fce8e6; color: #c5221f; border: 1px solid #f5c6c6; }
.dr-badge.ext     { background: #fce8f3; color: #9c1c6a; border: 1px solid #f8bbd9; }

.dr-subject {
    font-size: .875rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #202124; flex-shrink: 0; max-width: 230px;
}
.dr-subject.empty { color: #80868b; font-style: italic; font-weight: 400; }
.dr-sep  { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.dr-snip {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Date ── */
.dr-date {
    font-size: .78rem; color: #80868b; font-weight: 400;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}

/* ── Hover quick-actions ── */
.dr-hover-actions {
    display: flex; align-items: center; gap: .25rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.dr-act-btn {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .3rem .7rem;
    border: none; background: #fff;
    border-radius: 100px; cursor: pointer;
    font-size: .72rem; font-weight: 700;
    transition: background .12s, color .12s;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
    text-decoration: none; color: #5f6368;
}
.dr-act-btn:hover { background: #f1f3f4; color: #202124; }
.dr-act-btn.edit  { color: #1a73e8; }
.dr-act-btn.edit:hover  { background: #d2e3fc; color: #1557b0; }
.dr-act-btn.submit { color: #137333; }
.dr-act-btn.submit:hover { background: #e6f4ea; color: #0d652d; }
.dr-act-btn.view   { color: #5f6368; }
.dr-act-btn.view:hover   { background: #f1f3f4; color: #202124; }

/* ══ EMPTY STATE ══ */
.dr-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 4rem 2rem;
    height: 100%; gap: 1rem;
}
.dr-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #fef3c7;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #e37400;
}
.dr-empty h3 { font-size: 1.05rem; font-weight: 700; color: #3c4043; margin: 0; }
.dr-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }
.dr-empty-cta {
    display: inline-flex; align-items: center; gap: .45rem;
    background: #1a73e8; color: #fff;
    border: none; border-radius: 100px;
    padding: .55rem 1.25rem; font-size: .84rem; font-weight: 700;
    cursor: pointer; text-decoration: none;
    box-shadow: 0 1px 4px rgba(26,115,232,.3);
    transition: background .15s, box-shadow .15s;
    margin-top: .25rem;
}
.dr-empty-cta:hover { background: #1557b0; color: #fff; box-shadow: 0 2px 8px rgba(26,115,232,.4); }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .dr-row {
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
    .dr-chk-row  { display: none; }
    .dr-avatar   { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .dr-to       { grid-area: to; width: 100%; font-size: .9rem; }
    .dr-date     { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .dr-mid      { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .dr-subject  { max-width: 100%; width: 100%; font-size: .835rem; }
    .dr-sep      { display: none; }
    .dr-snip     { display: block; width: 100%; font-size: .8rem; }
    .dr-hover-actions { display: none !important; }
    .dr-tab      { padding: .5rem .75rem; font-size: .75rem; }
    .dr-toolbar  { padding: .45rem .75rem; }
    .dr-row:hover .dr-date { opacity: 1; }
}
</style>
@endpush

@php
    $filterStatus = request('status', 'all');
    $draftCount   = $letters->where('status', 'draft')->count();
    $pendingCount = $letters->where('status', 'pending_approval')->count();
@endphp

<div class="dr-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="dr-toolbar">
        <div class="dr-toolbar-left">
            <input type="checkbox" class="dr-chk" id="checkAll" title="Pilih Semua">
            <div class="dr-tb-sep"></div>
            <button class="dr-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <a href="{{ route('letters.create') }}" class="dr-compose-btn">
                <i class="bi bi-pencil-square"></i>
                <span class="d-none d-sm-inline">Tulis Baru</span>
            </a>
        </div>

        <div class="dr-toolbar-right">
            <span class="dr-pg-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada draft
                @endif
            </span>
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="dr-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="dr-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ══ FILTER TABS ══ --}}
    <div class="dr-tabs">
        <a href="{{ route('letters.drafts', ['status' => 'all']) }}"
           class="dr-tab {{ $filterStatus === 'all' ? 'active' : '' }}">
            <i class="bi bi-files"></i> Semua
            @if($letters->total() > 0)
                <span class="dr-tab-badge muted">{{ $letters->total() }}</span>
            @endif
        </a>
        <a href="{{ route('letters.drafts', ['status' => 'draft']) }}"
           class="dr-tab {{ $filterStatus === 'draft' ? 'active' : '' }}">
            <i class="bi bi-pencil"></i> Draft
            @if($draftCount > 0)
                <span class="dr-tab-badge amber">{{ $draftCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.drafts', ['status' => 'pending_approval']) }}"
           class="dr-tab {{ $filterStatus === 'pending_approval' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Menunggu ACC
            @if($pendingCount > 0)
                <span class="dr-tab-badge red">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="dr-list">
        @forelse($letters as $letter)
            @php
                $isExternal    = $letter->type === 'outbound_external';
                $isDraft       = $letter->status === 'draft';
                $isPending     = $letter->status === 'pending_approval';
                $showUrl       = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $editUrl       = route('letters.edit', \Vinkla\Hashids\Facades\Hashids::encode($letter->id));
                $submitUrl     = route('letters.submitDraft', $letter->id);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Tanpa Tujuan')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial  = mb_strtoupper(mb_substr($recipientName, 0, 1));
                $rowClass = $isDraft ? 'is-draft' : ($isPending ? 'is-pending' : '');
            @endphp

            <div class="dr-row {{ $rowClass }}"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="dr-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Avatar --}}
                <div class="dr-avatar {{ $isExternal ? 'av-ext' : 'av-amber' }}">{{ $initial }}</div>

                {{-- Recipient --}}
                <span class="dr-to">
                    <span class="dr-to-label">Ke:</span>{{ $recipientName }}
                </span>

                {{-- Middle: badges + subject + snippet --}}
                <div class="dr-mid">
                    <div class="dr-badges">
                        @if($isExternal)
                            <span class="dr-badge ext">Eksternal</span>
                        @endif
                        @if($isDraft)
                            <span class="dr-badge draft"><i class="bi bi-pencil"></i> Draft</span>
                        @elseif($isPending)
                            <span class="dr-badge pending"><i class="bi bi-hourglass-split"></i> Menunggu ACC</span>
                        @endif
                    </div>
                    <span class="dr-subject {{ !$letter->subject ? 'empty' : '' }}">
                        {{ $letter->subject ?: '(Tanpa Judul)' }}
                    </span>
                    <span class="dr-sep">–</span>
                    <span class="dr-snip">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="dr-date">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @elseif($letter->created_at->isCurrentYear())
                        {{ $letter->created_at->format('d M') }}
                    @else
                        {{ $letter->created_at->format('d/m/y') }}
                    @endif
                </span>

                {{-- Hover quick-actions --}}
                @if($isDraft)
                <div class="dr-hover-actions" onclick="event.stopPropagation()">
                    <a href="{{ $editUrl }}" class="dr-act-btn edit">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form method="POST" action="{{ $submitUrl }}"
                          onsubmit="return confirm('Ajukan draft ini untuk proses ACC Pimpinan?')"
                          style="display:inline;">
                        @csrf
                        <button type="submit" class="dr-act-btn submit">
                            <i class="bi bi-send-fill"></i> Ajukan
                        </button>
                    </form>
                </div>
                @elseif($isPending)
                <div class="dr-hover-actions" onclick="event.stopPropagation()">
                    <a href="{{ $editUrl }}" class="dr-act-btn edit">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button class="dr-act-btn view"
                            onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-eye"></i> Lihat
                    </button>
                </div>
                @endif

            </div>
        @empty
            <div class="dr-empty">
                <div class="dr-empty-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h3>
                    @if($filterStatus === 'draft') Tidak Ada Draft
                    @elseif($filterStatus === 'pending_approval') Tidak Ada Surat Menunggu ACC
                    @else Folder Draft Kosong
                    @endif
                </h3>
                <p>
                    @if($filterStatus === 'pending_approval')
                        Belum ada surat yang menunggu persetujuan ACC Pimpinan.
                    @else
                        Semua surat sudah dikirim. Buat surat baru untuk memulai.
                    @endif
                </p>
                <a href="{{ route('letters.create') }}" class="dr-empty-cta">
                    <i class="bi bi-pencil-square"></i> Tulis Surat Baru
                </a>
            </div>
        @endforelse
    </div>

</div>{{-- /.dr-root --}}

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