@extends('layouts.mailbox')
@section('title', 'Tugas Disposisi Surat')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   DISPOSISI SURAT — Gmail-Modified Design
═══════════════════════════════════════════ */

.dp-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.dp-toolbar {
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
.dp-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.dp-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.dp-chk {
    width: 16px; height: 16px;
    accent-color: #9333ea;
    cursor: pointer; flex-shrink: 0;
}
.dp-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.dp-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.dp-tb-btn:hover { background: #f1f3f4; color: #202124; }

.dp-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.dp-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.dp-pg-btn:hover { background: #f1f3f4; }
.dp-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ CONTEXT INFOBAR ══ */
.dp-infobar {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem 1rem;
    background: #faf5ff;
    border-bottom: 1px solid #e9d5ff;
    font-size: .78rem;
    color: #7e22ce;
    font-weight: 600;
    flex-shrink: 0;
}
.dp-infobar i { font-size: .85rem; }
.dp-role-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #f3e8ff; color: #9333ea;
    border-radius: 100px;
    padding: .25rem .75rem;
    font-size: .72rem; font-weight: 700;
    margin-left: .5rem;
}

/* ══ MAIL LIST ══ */
.dp-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.dp-list::-webkit-scrollbar { width: 5px; }
.dp-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.dp-row {
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
.dp-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; border-radius: 0 2px 2px 0;
    background: #9333ea; /* Purple untuk disposisi baru */
    transition: background .15s;
}
.dp-row.read { background: #fafafa; }
.dp-row.read::before { background: transparent; }
.dp-row.read .dp-to { font-weight: 500; color: #3c4043; }
.dp-row.read .dp-subject { font-weight: 500; color: #3c4043; }
.dp-row.read .dp-date { font-weight: 500; color: #3c4043; }

.dp-row:hover {
    background: #fafaf8;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.dp-row:hover .dp-hover-actions { opacity: 1; pointer-events: auto; }
.dp-row:hover .dp-date { opacity: 0; }

/* ── Checkbox ── */
.dp-chk-row {
    width: 15px; height: 15px;
    accent-color: #9333ea;
    cursor: pointer; flex-shrink: 0;
    opacity: 0; transition: opacity .12s;
}
.dp-row:hover .dp-chk-row,
.dp-chk-row:checked { opacity: 1; }

/* ── Avatar ── */
.dp-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
    background: #f3e8ff; color: #9333ea;
}

/* ── "Dari:" label ── */
.dp-to {
    width: 175px; flex-shrink: 0;
    font-size: .875rem; font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #202124;
}
.dp-to-label {
    font-size: .65rem; font-weight: 700;
    color: #80868b; text-transform: uppercase;
    letter-spacing: .06em; margin-right: .3rem;
}

/* ── Middle content ── */
.dp-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .45rem;
    overflow: hidden;
}
.dp-badges { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
.dp-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}
.dp-badge.dispo { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }

.dp-subject {
    font-size: .875rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #202124; flex-shrink: 0; max-width: 230px;
}
.dp-sep  { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.dp-snip {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Date ── */
.dp-date {
    font-size: .78rem; color: #9333ea; font-weight: 600;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}

/* ── Hover quick-actions ── */
.dp-hover-actions {
    display: flex; align-items: center; gap: .35rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.dp-act-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .85rem;
    border: none; background: #fff;
    border-radius: 100px; cursor: pointer;
    font-size: .75rem; font-weight: 700;
    transition: background .12s, color .12s, box-shadow .12s, transform .12s;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
    text-decoration: none; color: #5f6368;
}
.dp-act-btn:hover { background: #f1f3f4; color: #202124; transform: scale(1.03); }
.dp-act-btn.view { color: #5f6368; }
.dp-act-btn.view:hover { background: #f1f3f4; color: #202124; }

/* ══ EMPTY STATE ══ */
.dp-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 4rem 2rem;
    height: 100%; gap: 1rem;
}
.dp-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #f3e8ff;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #9333ea;
}
.dp-empty h3 { font-size: 1.05rem; font-weight: 700; color: #3c4043; margin: 0; }
.dp-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .dp-row {
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
    .dp-chk-row  { display: none; }
    .dp-avatar   { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .dp-to       { grid-area: to; width: 100%; font-size: .9rem; }
    .dp-date     { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .dp-mid      { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .dp-subject  { max-width: 100%; width: 100%; font-size: .835rem; }
    .dp-sep      { display: none; }
    .dp-snip     { display: block; width: 100%; font-size: .8rem; }
    .dp-hover-actions { display: none !important; }
    .dp-toolbar  { padding: .45rem .75rem; }
    .dp-infobar  { flex-wrap: wrap; font-size: .72rem; padding: .6rem .75rem; }
    .dp-row:hover .dp-date { opacity: 1; }
}
</style>
@endpush

<div class="dp-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="dp-toolbar">
        <div class="dp-toolbar-left">
            <input type="checkbox" class="dp-chk" id="checkAll" title="Pilih Semua">
            <div class="dp-tb-sep"></div>
            <button class="dp-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="dp-toolbar-right">
            <span class="dp-pg-info">
                @if(isset($letters) && $letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada surat
                @endif
            </span>
            @if(isset($letters))
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="dp-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="dp-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
            @endif
        </div>
    </div>

    {{-- ══ CONTEXT INFOBAR ══ --}}
    <div class="dp-infobar">
        <i class="bi bi-arrow-return-right"></i>
        <span>Surat perlu disposisi</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="dp-role-chip">
            <i class="bi bi-person-badge"></i>
            {{ ucwords(str_replace('_', ' ', $userRole)) }}
        </span>
        @if(isset($letters) && $letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#9333ea;">
                {{ $letters->total() }} Tugas
            </span>
        @endif
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="dp-list">
        @forelse($letters as $letter)
            @php
                $isUnread   = $letter->is_unread;
                $showUrl    = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Unknown');
                $initial    = mb_strtoupper(mb_substr($senderName, 0, 1));
            @endphp

            <div class="dp-row {{ $isUnread ? '' : 'read' }}"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="dp-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Avatar --}}
                <div class="dp-avatar">{{ $initial }}</div>

                {{-- Sender --}}
                <span class="dp-to">
                    <span class="dp-to-label">Dari:</span>{{ $senderName }}
                </span>

                {{-- Middle: badges + subject + snippet --}}
                <div class="dp-mid">
                    <div class="dp-badges">
                        <span class="dp-badge dispo"><i class="bi bi-arrow-return-right"></i> Disposisi</span>
                    </div>
                    <span class="dp-subject {{ !$letter->subject ? 'empty' : '' }}">
                        {{ $letter->subject ?: '(Tanpa Judul)' }}
                    </span>
                    <span class="dp-sep">–</span>
                    <span class="dp-snip">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="dp-date">
                    {{ $letter->created_at->format('d/m/Y') }}
                </span>

                {{-- Hover quick-actions --}}
                <div class="dp-hover-actions" onclick="event.stopPropagation()">
                    <button class="dp-act-btn view" onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-eye"></i> Buka
                    </button>
                </div>

            </div>
        @empty
            <div class="dp-empty">
                <div class="dp-empty-icon">
                    <i class="bi bi-journal-check"></i>
                </div>
                <h3>Bebas Tugas!</h3>
                <p>Anda tidak memiliki surat yang perlu didisposisikan saat ini.</p>
            </div>
        @endforelse
    </div>

</div>{{-- /.dp-root --}}

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