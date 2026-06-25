@extends('layouts.mailbox')
@section('title', 'Tugas Disposisi Surat')

@section('content')
<style>
    /* ══ DISPOSISI SURAT GMAIL-STYLE ══ */
    .acc-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }

    /* ── Toolbar ── */
    .acc-toolbar {
        display: flex; align-items: center; gap: .5rem; padding: .6rem 1rem;
        border-bottom: 1px solid #f1f5f9; background: #fff; flex-shrink: 0;
        position: sticky; top: 0; z-index: 10;
    }
    .tb-check { width: 16px; height: 16px; accent-color: #9333ea; cursor: pointer; flex-shrink: 0; }
    .tb-btn {
        width: 34px; height: 34px; border: none; background: none; color: #64748b; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: .95rem; transition: all .15s;
    }
    .tb-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tb-divider { width: 1px; height: 20px; background: #e2e8f0; margin: 0 .25rem; flex-shrink: 0; }
    .tb-spacer  { flex: 1; }
    .tb-page-info { font-size: .8rem; font-weight: 600; color: #94a3b8; white-space: nowrap; }
    .tb-page-btn {
        width: 30px; height: 30px; border: none; background: none; color: #64748b; border-radius: 6px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: .85rem; transition: all .15s; text-decoration: none;
    }
    .tb-page-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tb-page-btn.disabled { opacity: .35; pointer-events: none; }

    /* ── Context info bar ── */
    .acc-infobar {
        display: flex; align-items: center; gap: .75rem; padding: .55rem 1rem;
        background: #faf5ff; border-bottom: 1px solid #e9d5ff; font-size: .78rem; color: #7e22ce; font-weight: 600; flex-shrink: 0;
    }
    .acc-infobar i { font-size: .85rem; }
    .acc-role-chip {
        display: inline-flex; align-items: center; gap: .3rem; background: #f3e8ff; color: #9333ea;
        border: 1px solid #d8b4fe; border-radius: 100px; padding: .2rem .65rem; font-size: .7rem; font-weight: 700;
    }

    /* ── Mail list ── */
    .mail-list { flex: 1; overflow-y: auto; }

    /* ── Mail row ── */
    .m-row {
        display: flex; flex-wrap: nowrap !important; align-items: center; padding: 0 1rem; height: 52px;
        border-bottom: 1px solid #f8fafc; transition: background .12s; text-decoration: none; color: inherit;
        position: relative; cursor: pointer; gap: .65rem; background: #fff; overflow: hidden;
    }
    .m-row::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: #9333ea; transition: background .15s;
    }
    .m-row.read { background: #fafafa; }
    .m-row.read::before { background: transparent; }
    .m-row.read .m-to { font-weight: 500; color: #374151; }
    .m-row.read .m-subject { font-weight: 500; color: #374151; }
    .m-row.read .m-date { font-weight: 500; }
    .m-row:hover { background: #faf5ff; box-shadow: 0 1px 4px rgba(0,0,0,.04); z-index: 2; }
    .m-row:hover .m-actions { opacity: 1; }

    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0; background: #f3e8ff; color: #9333ea;
    }

    .m-to {
        width: 170px; flex-shrink: 0; font-size: .875rem; font-weight: 700;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #0f172a;
    }
    .m-to .to-label {
        font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-right: .25rem; letter-spacing: .04em;
    }

    .m-content { flex: 1; min-width: 0; display: flex; align-items: center; gap: .5rem; }
    .m-subject { font-size: .875rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #0f172a; flex-shrink: 0; max-width: 240px; }
    .m-sep { color: #cbd5e1; font-size: .8rem; flex-shrink: 0; }
    .m-snippet { font-size: .82rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 400; flex: 1; min-width: 0; }

    .m-badge {
        display: inline-flex; align-items: center; gap: .2rem; font-size: .6rem; font-weight: 700;
        padding: .15rem .45rem; border-radius: 4px; letter-spacing: .04em; flex-shrink: 0; text-transform: uppercase;
        background: #fdf4ff; color: #c026d3; border: 1px solid #fbcfe8;
    }

    .m-date { font-size: .78rem; font-weight: 700; color: #9333ea; white-space: nowrap; flex-shrink: 0; text-align: right; margin-left: auto; min-width: 65px; }

    .m-actions {
        display: flex; align-items: center; gap: .25rem; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        background: #faf5ff; padding-left: .75rem; opacity: 0; transition: opacity .15s; z-index: 5;
    }
    .m-act-btn {
        display: inline-flex; align-items: center; gap: .25rem; padding: .35rem .75rem; border: none;
        background: #fff; border-radius: 100px; cursor: pointer; font-size: .75rem; font-weight: 700;
        transition: all .15s; box-shadow: 0 1px 3px rgba(0,0,0,.08); text-decoration: none;
    }
    .m-act-btn.view { color: #64748b; }
    .m-act-btn.view:hover { background: #f1f5f9; }

    .m-check { width: 15px; height: 15px; accent-color: #9333ea; cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s; }
    .m-row:hover .m-check, .m-check:checked { opacity: 1; }

    .empty-acc { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 4rem 2rem; height: 100%; gap: .75rem; }
    .empty-acc i { font-size: 3.5rem; color: #e9d5ff; }
    .empty-acc h3 { font-size: 1.1rem; font-weight: 700; color: #7e22ce; margin: 0; }
    .empty-acc p  { font-size: .85rem; color: #94a3b8; margin: 0; max-width: 300px; }

    /* Responsive */
    @media (max-width: 768px) {
        .m-row {
            height: auto;
            padding: .85rem 1rem;
            display: grid;
            grid-template-columns: 40px 1fr auto;
            grid-template-areas:
                "avatar to date"
                "avatar content content";
            gap: 2px 12px;
            align-items: start;
        }
        .m-check { display: none; }
        .m-avatar {
            grid-area: avatar;
            width: 40px; height: 40px;
            margin-top: 2px;
        }
        .m-to {
            grid-area: to;
            width: 100%;
            font-size: .95rem;
            color: #0f172a;
        }
        .m-date {
            grid-area: date;
            position: static;
            margin: 0;
            margin-top: 3px;
        }
        .m-content {
            grid-area: content;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            gap: 3px;
            min-width: 0;
        }
        .m-content > .d-flex {
            margin-bottom: 2px;
            flex-wrap: wrap;
        }
        .m-subject {
            max-width: 100%;
            font-size: .85rem;
            width: 100%;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            min-width: 0;
        }
        .m-sep { display: none; }
        .m-snippet {
            flex: none;
            display: block;
            width: 100%;
            font-size: .85rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            min-width: 0;
        }
        .m-actions { display: none !important; }
        .acc-infobar { font-size: .72rem; flex-wrap: wrap; }
    }
</style>

<div class="acc-wrap">

    {{-- ── Toolbar ── --}}
    <div class="acc-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i></button>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if(isset($letters) && $letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 surat
            @endif
        </span>
        @if(isset($letters))
        <a href="{{ $letters->previousPageUrl() }}" class="tb-page-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}" style="color:inherit;"><i class="bi bi-chevron-left"></i></a>
        <a href="{{ $letters->nextPageUrl() }}" class="tb-page-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}" style="color:inherit;"><i class="bi bi-chevron-right"></i></a>
        @endif
    </div>

    {{-- ── Context info bar ── --}}
    <div class="acc-infobar">
        <i class="bi bi-arrow-return-right"></i>
        <span>Surat masuk berikut memerlukan tindak lanjut atau disposisi dari Anda.</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="acc-role-chip"><i class="bi bi-person-badge"></i> {{ ucwords(str_replace('_', ' ', $userRole)) }}</span>
        @if(isset($letters) && $letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#7e22ce;">{{ $letters->total() }} tugas</span>
        @endif
    </div>

    {{-- ── Mail list ── --}}
    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isUnread = $letter->is_unread;
                $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Unknown');
                $initial = mb_strtoupper(mb_substr($senderName, 0, 1));
            @endphp

            <div onclick="window.location='{{ $showUrl }}'" class="m-row {{ $isUnread ? '' : 'read' }}" data-id="{{ $letter->id }}">
                <input type="checkbox" class="m-check mail-check" onclick="event.stopPropagation()">

                <div class="m-avatar">
                    {{ $initial }}
                </div>

                <span class="m-to">
                    <span class="to-label">Dari:</span>{{ $senderName }}
                </span>

                <div class="m-content">
                    <div class="d-flex align-items-center gap-1 flex-shrink-0 me-2">
                        <span class="m-badge"><i class="bi bi-arrow-return-right"></i> Disposisi</span>
                    </div>

                    <span class="m-subject">{{ $letter->subject }}</span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn view" title="Buka Disposisi">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <span class="m-date">
                    @if($letter->created_at->isToday())
                        {{ $letter->created_at->format('H:i') }}
                    @elseif($letter->created_at->isCurrentYear())
                        {{ $letter->created_at->format('d M') }}
                    @else
                        {{ $letter->created_at->format('d/m/y') }}
                    @endif
                </span>
            </div>
        @empty
            <div class="empty-acc">
                <i class="bi bi-journal-check"></i>
                <h3>Bebas Tugas!</h3>
                <p>Anda tidak memiliki surat yang perlu didisposisikan saat ini.</p>
            </div>
        @endforelse
    </div>
</div>

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