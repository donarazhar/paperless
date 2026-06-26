@extends('layouts.mailbox')
@section('title', 'Task Log — Riwayat Pekerjaan')

@section('content')
@push('styles')
<style>
/* ═══════════════════════════════════════════
   TASK LOG — Gmail-Modified Design
═══════════════════════════════════════════ */

.tk-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #fff;
    font-family: 'Inter', sans-serif;
}

/* ══ TOOLBAR ══ */
.tk-toolbar {
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
.tk-toolbar-left  { display: flex; align-items: center; gap: .35rem; }
.tk-toolbar-right { display: flex; align-items: center; gap: .25rem; margin-left: auto; }

.tk-chk {
    width: 16px; height: 16px;
    accent-color: #4f46e5;
    cursor: pointer; flex-shrink: 0;
}
.tk-tb-sep { width: 1px; height: 18px; background: #e2e8f0; margin: 0 .1rem; flex-shrink: 0; }

.tk-tb-btn {
    width: 32px; height: 32px;
    border: none; background: none;
    color: #5f6368; border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; transition: background .12s, color .12s;
}
.tk-tb-btn:hover { background: #f1f3f4; color: #202124; }

.tk-pg-info {
    font-size: .78rem; font-weight: 500;
    color: #80868b; white-space: nowrap;
    padding: 0 .25rem;
}
.tk-pg-btn {
    width: 28px; height: 28px;
    border: none; background: none;
    border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; transition: background .12s;
    text-decoration: none; color: inherit;
}
.tk-pg-btn:hover { background: #f1f3f4; }
.tk-pg-btn.disabled { opacity: .35; pointer-events: none; }

/* ══ CONTEXT INFOBAR ══ */
.tk-infobar {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem 1rem;
    background: #eef2ff;
    border-bottom: 1px solid #c7d2fe;
    font-size: .78rem;
    color: #3730a3;
    font-weight: 600;
    flex-shrink: 0;
}
.tk-infobar i { font-size: .85rem; }
.tk-role-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #e0e7ff; color: #4338ca;
    border-radius: 100px;
    padding: .25rem .75rem;
    font-size: .72rem; font-weight: 700;
    margin-left: .5rem;
}

/* ══ MAIL LIST ══ */
.tk-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #dadce0 transparent;
}
.tk-list::-webkit-scrollbar { width: 5px; }
.tk-list::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 5px; }

/* ══ MAIL ROW ══ */
.tk-row {
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
.tk-row::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; border-radius: 0 2px 2px 0;
    background: #4f46e5; /* Indigo default */
    transition: background .15s;
}
.tk-row.task-acc::before { background: #c5221f; }
.tk-row.task-dispo::before { background: #9333ea; }

.tk-row.read { background: #fafafa; }
.tk-row.read::before { background: transparent; }
.tk-row.read .tk-to { font-weight: 500; color: #3c4043; }
.tk-row.read .tk-subject { font-weight: 500; color: #3c4043; }
.tk-row.read .tk-date { font-weight: 500; color: #3c4043; }

.tk-row:hover {
    background: #fafaf8;
    box-shadow: 0 1px 6px rgba(32,33,36,.06);
    z-index: 2;
}
.tk-row:hover .tk-hover-actions { opacity: 1; pointer-events: auto; }
.tk-row:hover .tk-date { opacity: 0; }

/* ── Checkbox ── */
.tk-chk-row {
    width: 15px; height: 15px;
    accent-color: #4f46e5;
    cursor: pointer; flex-shrink: 0;
    opacity: 0; transition: opacity .12s;
}
.tk-row:hover .tk-chk-row,
.tk-chk-row:checked { opacity: 1; }

/* ── Avatar ── */
.tk-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
}
.av-int { background: #e0e7ff; color: #3730a3; }
.av-ext { background: #fce8f3; color: #9c1c6a; }

/* ── "Dari/Ke:" label ── */
.tk-to {
    width: 175px; flex-shrink: 0;
    font-size: .875rem; font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    color: #202124;
}
.tk-to-label {
    font-size: .65rem; font-weight: 700;
    color: #80868b; text-transform: uppercase;
    letter-spacing: .06em; margin-right: .3rem;
}

/* ── Middle content ── */
.tk-mid {
    flex: 1; min-width: 0;
    display: flex; align-items: center; gap: .45rem;
    overflow: hidden;
}
.tk-badges { display: flex; align-items: center; gap: .25rem; flex-shrink: 0; }
.tk-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .58rem; font-weight: 700;
    padding: .12rem .42rem; border-radius: 4px;
    letter-spacing: .05em; flex-shrink: 0;
    text-transform: uppercase; line-height: 1.5;
}

.tk-subject {
    font-size: .875rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #202124; flex-shrink: 0; max-width: 230px;
}
.tk-sep  { color: #dadce0; font-size: .75rem; flex-shrink: 0; }
.tk-snip {
    font-size: .835rem; color: #80868b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 400; flex: 1; min-width: 0;
}

/* ── Date ── */
.tk-date {
    font-size: .78rem; color: #64748b; font-weight: 600;
    white-space: nowrap; flex-shrink: 0;
    min-width: 55px; text-align: right;
    transition: opacity .12s;
}
.tk-row.task-acc .tk-date { color: #c5221f; }
.tk-row.task-dispo .tk-date { color: #9333ea; }

/* ── Hover quick-actions ── */
.tk-hover-actions {
    display: flex; align-items: center; gap: .35rem;
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    opacity: 0; pointer-events: none;
    transition: opacity .12s;
    z-index: 5;
}
.tk-act-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .85rem;
    border: none; background: #fff;
    border-radius: 100px; cursor: pointer;
    font-size: .75rem; font-weight: 700;
    transition: background .12s, color .12s, box-shadow .12s, transform .12s;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
    text-decoration: none; color: #5f6368;
}
.tk-act-btn:hover { background: #f1f3f4; color: #202124; transform: scale(1.03); }
.tk-act-btn.view { color: #5f6368; }
.tk-act-btn.view:hover { background: #f1f3f4; color: #202124; }

/* ══ EMPTY STATE ══ */
.tk-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center; padding: 4rem 2rem;
    height: 100%; gap: 1rem;
}
.tk-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #f1f3f4;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #80868b;
}
.tk-empty h3 { font-size: 1.05rem; font-weight: 700; color: #3c4043; margin: 0; }
.tk-empty p  { font-size: .855rem; color: #80868b; margin: 0; max-width: 280px; line-height: 1.6; }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .tk-row {
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
    .tk-chk-row  { display: none; }
    .tk-avatar   { grid-area: avatar; width: 36px; height: 36px; margin-top: 2px; }
    .tk-to       { grid-area: to; width: 100%; font-size: .9rem; }
    .tk-date     { grid-area: date; opacity: 1 !important; font-size: .72rem; margin-top: 2px; }
    .tk-mid      { grid-area: mid; flex-direction: column; align-items: flex-start; gap: 3px; }
    .tk-subject  { max-width: 100%; width: 100%; font-size: .835rem; }
    .tk-sep      { display: none; }
    .tk-snip     { display: block; width: 100%; font-size: .8rem; }
    .tk-hover-actions { display: none !important; }
    .tk-toolbar  { padding: .45rem .75rem; }
    .tk-infobar  { flex-wrap: wrap; font-size: .72rem; padding: .6rem .75rem; }
    .tk-row:hover .tk-date { opacity: 1; }
}
</style>
@endpush

<div class="tk-root">

    {{-- ══ TOOLBAR ══ --}}
    <div class="tk-toolbar">
        <div class="tk-toolbar-left">
            <input type="checkbox" class="tk-chk" id="checkAll" title="Pilih Semua">
            <div class="tk-tb-sep"></div>
            <button class="tk-tb-btn" title="Muat Ulang" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="tk-toolbar-right">
            <span class="tk-pg-info">
                @if($letters->total() > 0)
                    {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
                @else
                    Tidak ada tugas
                @endif
            </span>
            <a href="{{ $letters->previousPageUrl() ?? '#' }}"
               class="tk-pg-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ $letters->nextPageUrl() ?? '#' }}"
               class="tk-pg-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ══ CONTEXT INFOBAR ══ --}}
    <div class="tk-infobar">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat pekerjaan</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="tk-role-chip">
            <i class="bi bi-person-badge"></i>
            {{ ucwords(str_replace('_', ' ', $userRole)) }}
        </span>
        @if($letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#3730a3;">
                {{ $letters->total() }} Riwayat
            </span>
        @endif
    </div>

    {{-- ══ MAIL LIST ══ --}}
    <div class="tk-list">
        @forelse($letters as $letter)
            @php
                $isUnread   = $letter->is_unread;
                $isExternal = $letter->type === 'outbound_external';
                $showUrl    = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);

                // Determine display name
                if (in_array($letter->status, ['draft', 'pending_approval'])) {
                    $displayName = $isExternal ? ($letter->external_recipient_name ?: 'Tanpa Tujuan') : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                    $label = 'Ke:';
                } else {
                    if ($letter->type === 'external') {
                        $displayName = $letter->external_sender_name ?? 'Unknown';
                    } else {
                        $displayName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                    }
                    $label = 'Dari:';
                }
                $initial = mb_strtoupper(mb_substr($displayName, 0, 1));

                $isAcc = $letter->status === 'pending_approval';
                $rowClass = $isAcc ? 'task-acc' : 'task-dispo';
            @endphp

            <div class="tk-row {{ $isUnread ? '' : 'read' }} {{ $rowClass }}"
                 onclick="window.location='{{ $showUrl }}'"
                 data-id="{{ $letter->id }}">

                {{-- Checkbox --}}
                <input type="checkbox" class="tk-chk-row mail-check" onclick="event.stopPropagation()">

                {{-- Avatar --}}
                <div class="tk-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">{{ $initial }}</div>

                {{-- Sender / Recipient --}}
                <span class="tk-to">
                    <span class="tk-to-label">{{ $label }}</span>{{ $displayName }}
                </span>

                {{-- Middle: badges + subject + snippet --}}
                @php
                    $myHistory = $letter->histories->where('user_id', Auth::id())
                        ->whereIn('action', ['approved', 'disposed', 'forwarded', 'completed', 'agendakan'])
                        ->sortByDesc('created_at')->first();
                    $myAction = $myHistory ? $myHistory->action : 'unknown';
                @endphp

                <div class="tk-mid">
                    <div class="tk-badges">
                        @if($myAction === 'approved')
                            <span class="tk-badge" style="background:#e6f4ea; color:#137333; border:1px solid #a8d5b5;">
                                <i class="bi bi-check-circle-fill"></i> Telah di-ACC
                            </span>
                        @elseif($myAction === 'disposed')
                            <span class="tk-badge" style="background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;">
                                <i class="bi bi-arrow-return-right"></i> Didisposisikan
                            </span>
                        @elseif($myAction === 'forwarded')
                            <span class="tk-badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                                <i class="bi bi-forward-fill"></i> Diteruskan
                            </span>
                        @elseif($myAction === 'completed')
                            <span class="tk-badge" style="background:#f1f3f4; color:#5f6368; border:1px solid #dadce0;">
                                <i class="bi bi-archive-fill"></i> Diarsipkan Selesai
                            </span>
                        @elseif($myAction === 'agendakan')
                            <span class="tk-badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">
                                <i class="bi bi-journal-check"></i> Diagendakan
                            </span>
                        @else
                            <span class="tk-badge" style="background:#f1f3f4; color:#5f6368; border:1px solid #dadce0;">
                                <i class="bi bi-check2-all"></i> Selesai
                            </span>
                        @endif
                    </div>
                    <span class="tk-subject {{ !$letter->subject ? 'empty' : '' }}">
                        {{ $letter->subject ?: '(Tanpa Judul)' }}
                    </span>
                    <span class="tk-sep">–</span>
                    <span class="tk-snip">{!! Str::limit(strip_tags($letter->body), 90) !!}</span>
                </div>

                {{-- Date --}}
                <span class="tk-date">
                    {{ $letter->created_at->format('d/m/Y') }}
                </span>

                {{-- Hover quick-actions --}}
                <div class="tk-hover-actions" onclick="event.stopPropagation()">
                    <button class="tk-act-btn view" onclick="window.location='{{ $showUrl }}'">
                        <i class="bi bi-eye"></i> Lihat Surat
                    </button>
                </div>

            </div>
        @empty
            <div class="tk-empty">
                <div class="tk-empty-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3>Belum Ada Riwayat</h3>
                <p>Anda belum menyelesaikan tugas apa pun (ACC Surat atau Tindak Lanjut Disposisi).</p>
            </div>
        @endforelse
    </div>

</div>{{-- /.tk-root --}}

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
