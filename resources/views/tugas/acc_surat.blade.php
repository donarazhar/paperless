@extends('layouts.mailbox')
@section('title', 'Draft — Menunggu ACC')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   ACC SURAT — Gmail-Modified Design
═══════════════════════════════════════════ */

.ac-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.ac-toolbar {
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
.ac-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.ac-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.ac-chk {
    width: 16px; height: 16px;
    accent-color: #c5221f;
    cursor: pointer; flex-shrink: 0;
}
.ac-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.ac-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.ac-tb-btn:hover { background: #f1f3f4; color: #202124; }

.ac-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.ac-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.ac-pg-btn:hover { background: #f1f3f4; }
.ac-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ CONTEXT INFOBAR ══ */
.ac-infobar {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem 1rem;
    background: #fce8e6;
    border-bottom: 1px solid #f5c6c6;
    font-size: .78rem;
    color: #a50e0e;
    font-weight: 600;
    flex-shrink: 0;
}
.ac-infobar i { font-size: .85rem; }
.ac-role-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #f8d8d8; color: #b91c1c;
    border-radius: 100px;
    padding: .25rem .75rem;
    font-size: .72rem; font-weight: 700;
    margin-left: .5rem;
}

/* ══ MAIL LIST ══ */
.ac-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.ac-list::-webkit-scrollbar { width: 5px; }
.ac-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.ac-row {
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
.ac-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; border-radius: 0 2px 2px 0;
    background: #c5221f; /* Selalu merah untuk ACC */
}

.ac-row:hover {
    background: #fafaf8;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.ac-row:hover .ac-hover-actions { opacity: 1; pointer-events: auto; }
.ac-row:hover .ac-date { opacity: 0; }

/* ── Checkbox ── */
.ac-chk-row {
    width: 15px; height: 15px;
    accent-color: #c5221f;
    cursor: pointer; flex-shrink: 0;
    opacity: 0; transition: opacity .12s;
}
.ac-row:hover .ac-chk-row,
.ac-chk-row:checked { opacity: 1; }

/* ── Avatar ── */
.ac-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
}
.av-red { background: #fce8e6; color: #a50e0e; }
.av-ext { background: #fce8f3; color: #9c1c6a; }

/* ── "Ke:" label ── */
.ac-to {
    width: 175px; flex-shrink: 0;
    font-size: .875rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #3c4043;
}
.ac-to-label {
    font-size: .65rem; font-weight: 700;
    color: #80868b; text-transform: uppercase;
    letter-spacing: .06em; margin-right: .3rem;
}

/* ── Middle content ── */
.ac-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .45rem;
    overflow: hidden;
}
.ac-badges { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
.ac-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}
.ac-badge.acc { background: #fce8e6; color: #c5221f; border: 1px solid #f5c6c6; }
.ac-badge.ext { background: #fce8f3; color: #9c1c6a; border: 1px solid #f8bbd9; }
.ac-badge.int { background: #e8f0fe; color: #1557b0; border: 1px solid #a8c7fa; }

.ac-subject {
    font-size: .875rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #202124; flex-shrink: 0; max-width: 230px;
}
.ac-sep  { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.ac-snip {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Date ── */
.ac-date {
    font-size: .78rem; color: #a50e0e; font-weight: 600;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}

/* ── Hover quick-actions ── */
.ac-hover-actions {
    display: flex; align-items: center; gap: .35rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.ac-act-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .85rem;
    border: none; background: #fff;
    border-radius: 100px; cursor: pointer;
    font-size: .75rem; font-weight: 700;
    transition: background .12s, color .12s, box-shadow .12s, transform .12s;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
    text-decoration: none; color: #5f6368;
}
.ac-act-btn:hover { background: #f1f3f4; color: #202124; transform: scale(1.03); }
.ac-act-btn.view { color: #5f6368; }
.ac-act-btn.view:hover { background: #f1f3f4; color: #202124; }
.ac-act-btn.approve { color: #c5221f; }
.ac-act-btn.approve:hover { background: #fce8e6; color: #a50e0e; }

/* ══ EMPTY STATE ══ */
.ac-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 4rem 2rem;
    height: 100%; gap: 1rem;
}
.ac-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #e6f4ea;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #188038;
}
.ac-empty h3 { font-size: 1.05rem; font-weight: 700; color: #3c4043; margin: 0; }
.ac-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .ac-row {
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
    .ac-chk-row  { display: none; }
    .ac-avatar   { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .ac-to       { grid-area: to; width: 100%; font-size: .9rem; }
    .ac-date     { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .ac-mid      { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .ac-subject  { max-width: 100%; width: 100%; font-size: .835rem; }
    .ac-sep      { display: none; }
    .ac-snip     { display: block; width: 100%; font-size: .8rem; }
    .ac-hover-actions { display: none !important; }
    .ac-toolbar  { padding: .45rem .75rem; }
    .ac-infobar  { flex-wrap: wrap; font-size: .72rem; padding: .6rem .75rem; }
    .ac-row:hover .ac-date { opacity: 1; }
}
</style>
@endpush

<div class="ac-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="ac-toolbar">
        <div class="ac-toolbar-left">
            <input type="checkbox" class="ac-chk" id="checkAll" title="Pilih Semua">
            <div class="ac-tb-sep"></div>
            <button class="ac-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="ac-toolbar-right">
            <span class="ac-pg-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada surat
                @endif
            </span>
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="ac-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="ac-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ══ CONTEXT INFOBAR ══ --}}
    <div class="ac-infobar">
        <i class="bi bi-shield-exclamation"></i>
        <span>Surat butuh ACC</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="ac-role-chip">
            <i class="bi bi-person-badge"></i>
            {{ $userRole === 'kepala_unit' ? 'Kepala Unit' : 'Subag Persuratan' }}
        </span>
        @if($letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#c5221f;">
                {{ $letters->total() }} Menunggu
            </span>
        @endif
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="ac-list">
        @forelse($letters as $letter)
            @php
                $isExternal    = $letter->type === 'outbound_external';
                $showUrl       = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $approveUrl    = route('letters.approve', $letter->id);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Tanpa Tujuan Eksternal')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial  = mb_strtoupper(mb_substr($recipientName, 0, 1));
            @endphp

            <div class="ac-row"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="ac-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Avatar --}}
                <div class="ac-avatar {{ $isExternal ? 'av-ext' : 'av-red' }}">{{ $initial }}</div>

                {{-- Recipient --}}
                <span class="ac-to">
                    <span class="ac-to-label">Ke:</span>{{ $recipientName }}
                </span>

                {{-- Middle: badges + subject + snippet --}}
                <div class="ac-mid">
                    <div class="ac-badges">
                        @if($isExternal)
                            <span class="ac-badge ext">Eksternal</span>
                        @else
                            <span class="ac-badge int">Internal</span>
                        @endif
                        <span class="ac-badge acc"><i class="bi bi-hourglass-split"></i> Perlu ACC</span>
                    </div>
                    <span class="ac-subject {{ !$letter->subject ? 'empty' : '' }}">
                        {{ $letter->subject ?: '(Tanpa Judul)' }}
                    </span>
                    <span class="ac-sep">–</span>
                    <span class="ac-snip">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="ac-date">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @elseif($letter->created_at->isCurrentYear())
                        {{ $letter->created_at->format('d M') }}
                    @else
                        {{ $letter->created_at->format('d/m/y') }}
                    @endif
                </span>

                {{-- Hover quick-actions --}}
                <div class="ac-hover-actions" onclick="event.stopPropagation()">
                    <button class="ac-act-btn view" onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-eye"></i> Lihat
                    </button>
                    <form method="POST" action="{{ $approveUrl }}"
                          onsubmit="return confirm('ACC surat ini?')"
                          style="display:inline;">
                        @csrf
                        <button type="submit" class="ac-act-btn approve">
                            <i class="bi bi-check-lg"></i> ACC
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="ac-empty">
                <div class="ac-empty-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3>Semua Beres!</h3>
                <p>Tidak ada surat yang menunggu persetujuan (ACC) dari Anda saat ini.</p>
            </div>
        @endforelse
    </div>

</div>{{-- /.ac-root --}}

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