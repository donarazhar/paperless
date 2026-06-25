@extends('layouts.mailbox')
@section('title', 'Draft Surat')

@section('content')
<style>
    /* ══ DRAFTS GMAIL-STYLE ══ */
    .draft-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }

    /* ── Toolbar ── */
    .draft-toolbar {
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
        font-size: .85rem; transition: all .15s;
        text-decoration: none;
    }
    .tb-page-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tb-page-btn.disabled { opacity: .35; pointer-events: none; }

    /* ── Filter tabs ── */
    .draft-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        flex-shrink: 0;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .draft-tabs::-webkit-scrollbar { display: none; }
    .draft-tab {
        display: flex; align-items: center; gap: .4rem;
        padding: .65rem 1.25rem;
        font-size: .8rem; font-weight: 600;
        color: #64748b;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
        text-decoration: none;
        transition: all .15s;
        cursor: pointer;
    }
    .draft-tab:hover { color: #f59e0b; background: #fffbeb; }
    .draft-tab.active { color: #d97706; border-bottom-color: #f59e0b; background: #fff; }
    .tab-badge {
        font-size: .62rem; font-weight: 700;
        padding: .1rem .38rem; border-radius: 10px;
        min-width: 18px; text-align: center;
    }
    .tab-draft    { background: #fef3c7; color: #92400e; }
    .tab-pending  { background: #fecaca; color: #991b1b; }
    .tab-total    { background: #e2e8f0; color: #64748b; }

    /* ── Compose shortcut ── */
    .btn-compose-sm {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #4f46e5; color: #fff;
        border: none; border-radius: 100px;
        padding: .38rem .95rem; font-size: .78rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        text-decoration: none; flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(79,70,229,.25);
    }
    .btn-compose-sm:hover { background: #4338ca; color: #fff; transform: translateY(-1px); }

    /* ── Mail list ── */
    .mail-list { flex: 1; overflow-y: auto; }

    /* ── Mail row ── */
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
    /* Draft = amber left border */
    .m-row.is-draft::before   { background: #f59e0b; }
    /* Pending approval = red left border */
    .m-row.is-pending::before { background: #dc2626; }
    .m-row { background: #fff; }
    .m-row:hover { background: #fafaf5; }
    .m-row:hover .m-actions { opacity: 1; }

    /* Avatar */
    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0;
    }
    .av-int  { background: #e0e7ff; color: #4338ca; }
    .av-ext  { background: #fce7f3; color: #be185d; }
    .av-draf { background: #fef3c7; color: #92400e; }

    /* "Ke:" recipient label */
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
        font-size: .875rem; font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: #0f172a; flex-shrink: 0; max-width: 240px;
    }
    .m-subject.no-subject { color: #94a3b8; font-style: italic; font-weight: 400; }
    .m-sep { color: #cbd5e1; font-size: .8rem; flex-shrink: 0; }
    .m-snippet {
        font-size: .82rem; color: #94a3b8;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        font-weight: 400; flex: 1; min-width: 0;
    }

    /* Status badges */
    .m-badge {
        display: inline-flex; align-items: center; gap: .2rem;
        font-size: .6rem; font-weight: 700;
        padding: .15rem .45rem; border-radius: 4px;
        letter-spacing: .04em; flex-shrink: 0;
        text-transform: uppercase;
    }
    .mb-draft   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .mb-pending { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .mb-ext     { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }

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
        background: #fafaf5;
        padding-left: .75rem;
        opacity: 0; transition: opacity .15s; z-index: 5;
    }
    .m-act-btn {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .6rem; border: none;
        background: #fff; border-radius: 6px; cursor: pointer;
        font-size: .72rem; font-weight: 600; color: #4f46e5;
        transition: all .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        text-decoration: none;
    }
    .m-act-btn:hover { background: #eef2ff; color: #3730a3; }
    .m-act-btn.danger { color: #dc2626; }
    .m-act-btn.danger:hover { background: #fef2f2; }

    /* Checkbox */
    .m-check {
        width: 15px; height: 15px; accent-color: #4f46e5;
        cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s;
    }
    .m-row:hover .m-check,
    .m-check:checked { opacity: 1; }

    /* Empty state */
    .empty-draft {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 4rem 2rem;
        height: 100%; gap: .75rem;
    }
    .empty-draft i { font-size: 3.5rem; color: #e2e8f0; }
    .empty-draft h3 { font-size: 1.1rem; font-weight: 700; color: #94a3b8; margin: 0; }
    .empty-draft p  { font-size: .85rem; color: #cbd5e1; margin: 0; max-width: 300px; }

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
            font-size: .95rem;
            max-width: 100%;
            white-space: normal;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .m-snippet {
            display: none;
        }
        .m-actions {
            display: none !important;
        }
        .draft-tabs .draft-tab { padding: .55rem .85rem; font-size: .75rem; }
    }
</style>

@php
    $filterStatus = request('status', 'all');
    $draftCount   = $letters->where('status', 'draft')->count();
    $pendingCount = $letters->where('status', 'pending_approval')->count();
@endphp

<div class="draft-wrap">

    {{-- ── Toolbar ── --}}
    <div class="draft-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <a href="{{ route('letters.create') }}" class="btn-compose-sm" title="Tulis Surat Baru">
            <i class="bi bi-pencil-square"></i>
            <span class="d-none d-md-inline">Tulis Baru</span>
        </a>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if($letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 draft
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
    <div class="draft-tabs">
        <a href="{{ route('letters.drafts', ['status'=>'all']) }}"
           class="draft-tab {{ $filterStatus === 'all' ? 'active' : '' }}">
            <i class="bi bi-files"></i> Semua
            @if($letters->total() > 0)
                <span class="tab-badge tab-total">{{ $letters->total() }}</span>
            @endif
        </a>
        <a href="{{ route('letters.drafts', ['status'=>'draft']) }}"
           class="draft-tab {{ $filterStatus === 'draft' ? 'active' : '' }}">
            <i class="bi bi-pencil"></i> Draft
            @if($draftCount > 0)
                <span class="tab-badge tab-draft">{{ $draftCount }}</span>
            @endif
        </a>
        <a href="{{ route('letters.drafts', ['status'=>'pending_approval']) }}"
           class="draft-tab {{ $filterStatus === 'pending_approval' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Menunggu ACC
            @if($pendingCount > 0)
                <span class="tab-badge tab-pending">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>

    {{-- ── Mail list ── --}}
    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isExternal   = $letter->type === 'outbound_external';
                $isDraft      = $letter->status === 'draft';
                $isPending    = $letter->status === 'pending_approval';
                $showUrl      = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Tanpa Tujuan')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial = mb_strtoupper(mb_substr($recipientName, 0, 1));
                $rowClass = $isDraft ? 'is-draft' : ($isPending ? 'is-pending' : '');
            @endphp

            <div onclick="window.location='{{ $showUrl }}'"
               class="m-row {{ $rowClass }}">

                <input type="checkbox" class="m-check mail-check" onclick="event.stopPropagation()">

                <div class="m-avatar {{ $isExternal ? 'av-ext' : 'av-draf' }}">
                    {{ $initial }}
                </div>

                <span class="m-to">
                    <span class="to-label">Ke:</span>{{ $recipientName }}
                </span>

                <div class="m-content">
                    {{-- Status + type badges --}}
                    <div class="d-flex align-items-center gap-1 flex-shrink-0 me-2">
                        @if($isExternal)
                            <span class="m-badge mb-ext">EXT</span>
                        @endif
                        @if($isDraft)
                            <span class="m-badge mb-draft"><i class="bi bi-pencil"></i> Draft</span>
                        @elseif($isPending)
                            <span class="m-badge mb-pending"><i class="bi bi-hourglass-split"></i> Menunggu ACC</span>
                        @endif
                    </div>

                    <span class="m-subject {{ !$letter->subject ? 'no-subject' : '' }}">
                        {{ $letter->subject ?: '(Tanpa Judul)' }}
                    </span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                {{-- Hover quick-actions --}}
                @if($isDraft)
                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ route('letters.edit', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}'" class="m-act-btn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('letters.submitDraft', $letter->id) }}" onsubmit="return confirm('Ajukan draft ini untuk proses ACC Pimpinan?');" style="display:inline;">
                        @csrf
                        <button type="submit" class="m-act-btn" style="color: #16a34a; background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <i class="bi bi-send-fill"></i> Ajukan
                        </button>
                    </form>
                </div>
                @elseif($isPending)
                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ route('letters.edit', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}'" class="m-act-btn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn">
                        <i class="bi bi-eye"></i> Lihat
                    </button>
                </div>
                @endif

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
            <div class="empty-draft">
                <i class="bi bi-file-earmark-text"></i>
                <h3>
                    @if($filterStatus === 'draft') Tidak Ada Draft
                    @elseif($filterStatus === 'pending_approval') Tidak Ada Surat Menunggu ACC
                    @else Folder Draft Kosong
                    @endif
                </h3>
                <p>
                    @if($filterStatus === 'pending_approval')
                        Belum ada surat yang menunggu persetujuan ACC.
                    @else
                        Semua surat sudah terkirim. Klik <strong>Tulis Baru</strong> untuk membuat surat baru.
                    @endif
                </p>
                <a href="{{ route('letters.create') }}" class="btn-compose-sm mt-2">
                    <i class="bi bi-pencil-square"></i> Tulis Surat Baru
                </a>
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