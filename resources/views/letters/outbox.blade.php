@extends('layouts.mailbox')
@section('title', 'Kotak Keluar')

@section('content')
<style>
    /* ══ OUTBOX GMAIL-STYLE (konsisten dengan inbox) ══ */
    .outbox-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }

    /* ── Toolbar ── */
    .outbox-toolbar {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .6rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        flex-shrink: 0;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .tb-check {
        width: 16px; height: 16px;
        accent-color: #4f46e5; cursor: pointer; flex-shrink: 0;
    }
    .tb-btn {
        width: 34px; height: 34px; border: none; background: none;
        color: #64748b; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; transition: all .15s;
    }
    .tb-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tb-divider { width: 1px; height: 20px; background: #e2e8f0; margin: 0 .25rem; flex-shrink: 0; }
    .tb-spacer  { flex: 1; }
    .tb-page-info { font-size: .8rem; font-weight: 600; color: #94a3b8; white-space: nowrap; }
    .tb-page-btn {
        width: 30px; height: 30px; border: none; background: none;
        color: #64748b; border-radius: 6px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; transition: all .15s; text-decoration: none;
    }
    .tb-page-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tb-page-btn.disabled { opacity: .35; pointer-events: none; }

    /* ── Filter tabs ── */
    .outbox-tabs {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        flex-shrink: 0;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .outbox-tabs::-webkit-scrollbar { display: none; }
    .outbox-tab {
        display: flex; align-items: center; gap: .4rem;
        padding: .65rem 1.25rem;
        font-size: .8rem; font-weight: 600;
        color: #64748b;
        border-bottom: 2px solid transparent;
        white-space: nowrap; text-decoration: none;
        transition: all .15s; cursor: pointer;
    }
    .outbox-tab:hover { color: #10b981; background: #f0fdf4; }
    .outbox-tab.active { color: #059669; border-bottom-color: #10b981; background: #fff; }
    .tab-badge {
        font-size: .62rem; font-weight: 700;
        padding: .1rem .38rem; border-radius: 10px;
        min-width: 18px; text-align: center;
    }
    .tab-total   { background: #e2e8f0; color: #64748b; }
    .tab-wait    { background: #fef3c7; color: #92400e; }
    .tab-sent    { background: #d1fae5; color: #065f46; }

    /* ── Mail list ── */
    .mail-list { flex: 1; overflow-y: auto; }

    /* ── Mail row — sama persis dengan inbox ── */
    .m-row {
        display: flex;
        flex-wrap: nowrap !important;
        align-items: center;
        padding: 0 1rem;
        height: 52px;
        border-bottom: 1px solid #f8fafc;
        transition: background .12s;
        text-decoration: none;
        color: inherit;
        position: relative;
        cursor: pointer;
        gap: .65rem;
        overflow: hidden;
    }
    .m-row::before {
        content: '';
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px; background: transparent; transition: background .15s;
    }
    /* Status-based left border */
    .m-row.st-pending_approval::before  { background: #f59e0b; }
    .m-row.st-pending_sending::before   { background: #3b82f6; }
    .m-row.st-pending_agenda::before    { background: #8b5cf6; }
    .m-row.st-completed::before         { background: #10b981; }
    .m-row.st-in_consideration::before  { background: #6366f1; }
    .m-row { background: #fff; }
    .m-row:hover { background: #f1f5f9; }
    .m-row:hover .m-actions { opacity: 1; }

    /* Avatar */
    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0;
    }
    .av-int  { background: #d1fae5; color: #065f46; }
    .av-ext  { background: #fce7f3; color: #be185d; }

    /* "Ke:" recipient */
    .m-to {
        width: 170px; flex-shrink: 0;
        font-size: .875rem; font-weight: 500;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        color: #374151;
    }
    .m-to .to-label {
        font-size: .68rem; font-weight: 700;
        color: #94a3b8; text-transform: uppercase;
        margin-right: .25rem; letter-spacing: .04em;
    }

    /* Content */
    .m-content {
        flex: 1; min-width: 0;
        display: flex; align-items: center; gap: .5rem;
    }
    .m-subject {
        font-size: .875rem; font-weight: 500;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: #374151; flex-shrink: 0; max-width: 240px;
    }
    .m-sep { color: #cbd5e1; font-size: .8rem; flex-shrink: 0; }
    .m-snippet {
        font-size: .82rem; color: #94a3b8;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        font-weight: 400; flex: 1; min-width: 0;
    }

    /* Badges */
    .m-badge {
        display: inline-flex; align-items: center; gap: .2rem;
        font-size: .6rem; font-weight: 700;
        padding: .15rem .45rem; border-radius: 4px;
        letter-spacing: .04em; flex-shrink: 0; text-transform: uppercase;
    }
    .mb-ext      { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
    .mb-agenda   { background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; }
    .mb-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .mb-sending  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .mb-done     { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .mb-consider { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
    .mb-review   { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .mb-default  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Date */
    .m-date {
        font-size: .78rem; color: #94a3b8; font-weight: 500;
        white-space: nowrap; flex-shrink: 0; text-align: right;
        margin-left: auto !important;
        width: 65px;
        display: block;
    }

    /* Hover actions */
    .m-actions {
        display: flex; align-items: center; gap: .25rem;
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        background: #f1f5f9;
        padding-left: .75rem;
        opacity: 0; transition: opacity .15s; z-index: 5;
    }
    .m-act-btn {
        width: 32px; height: 32px; border: none; background: #fff;
        border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; color: #64748b; transition: all .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.08); text-decoration: none;
    }
    .m-act-btn:hover { background: #d1fae5; color: #065f46; }

    /* Checkbox */
    .m-check {
        width: 15px; height: 15px; accent-color: #4f46e5;
        cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s;
    }
    .m-row:hover .m-check,
    .m-check:checked { opacity: 1; }

    /* Empty state */
    .empty-outbox {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 4rem 2rem;
        height: 100%; gap: .75rem;
    }
    .empty-outbox i { font-size: 3.5rem; color: #e2e8f0; }
    .empty-outbox h3 { font-size: 1.1rem; font-weight: 700; color: #94a3b8; margin: 0; }
    .empty-outbox p  { font-size: .85rem; color: #cbd5e1; margin: 0; max-width: 300px; }

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
        .m-actions {
            display: none !important;
        }
        .outbox-tabs .outbox-tab { padding: .55rem .85rem; font-size: .75rem; }
    }
</style>

@php
    $filterStatus = request('status', 'all');

    /* count per status untuk tab badge */
    $waitCount = $letters->whereIn('status', ['pending_approval','pending_sending','pending_agenda','in_consideration'])->count();
    $sentCount  = $letters->whereIn('status', ['completed'])->count();
@endphp

<div class="outbox-wrap">

    {{-- ── Toolbar ── --}}
    <div class="outbox-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <button class="tb-btn" title="Opsi">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if($letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 pesan
            @endif
        </span>
        <a href="{{ $letters->previousPageUrl() }}"
           class="tb-page-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}"
           style="color:inherit;">
            <i class="bi bi-chevron-left"></i>
        </a>
        <a href="{{ $letters->nextPageUrl() }}"
           class="tb-page-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}"
           style="color:inherit;">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    {{-- ── Filter tabs ── --}}
    <div class="outbox-tabs">
        <a href="{{ route('letters.outbound', ['status'=>'all']) }}"
           class="outbox-tab {{ $filterStatus === 'all' ? 'active' : '' }}">
            <i class="bi bi-send"></i> Semua
            @if($letters->total() > 0)
                <span class="tab-badge tab-total">{{ $letters->total() }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['status'=>'waiting']) }}"
           class="outbox-tab {{ $filterStatus === 'waiting' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Menunggu
            @if($waitCount > 0)
                <span class="tab-badge tab-wait">{{ $waitCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['status'=>'completed']) }}"
           class="outbox-tab {{ $filterStatus === 'completed' ? 'active' : '' }}">
            <i class="bi bi-check-circle-fill"></i> Terkirim
            @if($sentCount > 0)
                <span class="tab-badge tab-sent">{{ $sentCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.outbound', ['type'=>'outbound_external']) }}"
           class="outbox-tab {{ request('type') === 'outbound_external' ? 'active' : '' }}">
            <i class="bi bi-globe"></i> Eksternal
        </a>
    </div>

    {{-- ── Mail list ── --}}
    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isExternal   = $letter->type === 'outbound_external';
                $showUrl      = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Pihak Luar')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial = mb_strtoupper(mb_substr($recipientName, 0, 1));

                /* status badge class & label */
                $sbClass = match($letter->status) {
                    'pending_approval'  => 'mb-pending',
                    'pending_sending'   => 'mb-sending',
                    'pending_agenda'    => 'mb-review',
                    'in_consideration'  => 'mb-consider',
                    'completed'         => 'mb-done',
                    default             => 'mb-default',
                };
                $rowClass = 'st-' . $letter->status;
            @endphp

            <div onclick="window.location='{{ $showUrl }}'"
               class="m-row {{ $rowClass }}">

                <input type="checkbox" class="m-check mail-check" onclick="event.stopPropagation()">

                <div class="m-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">
                    {{ $initial }}
                </div>

                <span class="m-to">
                    <span class="to-label">Ke:</span>{{ $recipientName }}
                </span>

                <div class="m-content">
                    {{-- Badges --}}
                    <div class="d-flex align-items-center gap-1 flex-shrink-0 me-2">
                        @if($isExternal)
                            <span class="m-badge mb-ext">EXT</span>
                        @endif
                        @if($letter->agenda_number)
                            <span class="m-badge mb-agenda">{{ $letter->agenda_number }}</span>
                        @endif
                        <span class="m-badge {{ $sbClass }}">{{ $letter->status_label }}</span>
                    </div>

                    <span class="m-subject">{{ $letter->subject }}</span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                {{-- Hover quick-action --}}
                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn" title="Lihat Detail">
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
            <div class="empty-outbox">
                <i class="bi bi-send"></i>
                <h3>
                    @if($filterStatus === 'waiting') Tidak Ada Surat Menunggu
                    @elseif($filterStatus === 'completed') Belum Ada Surat Terkirim
                    @else Kotak Keluar Kosong
                    @endif
                </h3>
                <p>
                    @if($filterStatus === 'waiting')
                        Tidak ada surat yang sedang menunggu proses persetujuan atau pengiriman.
                    @else
                        Belum ada surat keluar dari unit Anda.
                    @endif
                </p>
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