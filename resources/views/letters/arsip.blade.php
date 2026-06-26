@extends('layouts.mailbox')
@section('title', 'Arsip Surat')

@section('content')
<style>
    /* ══ ARSIP GMAIL-STYLE ══ */
    .arsip-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }

    /* ── Toolbar ── */
    .arsip-toolbar {
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
    .arsip-tabs {
        display: flex;
        gap: 0;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        flex-shrink: 0;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .arsip-tabs::-webkit-scrollbar { display: none; }
    .arsip-tab {
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
    .arsip-tab:hover { color: #16a34a; background: #f0fdf4; }
    .arsip-tab.active { color: #15803d; border-bottom-color: #16a34a; background: #fff; }
    .tab-badge {
        font-size: .62rem; font-weight: 700;
        padding: .1rem .38rem; border-radius: 10px;
        min-width: 18px; text-align: center;
    }
    .tab-badge.muted { background: #e2e8f0; color: #64748b; }

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
    .m-row.unread           { background: #fff; }
    .m-row.unread::before   { background: #16a34a; }
    .m-row.read             { background: #f8fafc; }
    .m-row:hover            { background: #f1f5f9; z-index: 2; }
    .m-row:hover .m-actions { opacity: 1; }

    /* Avatar */
    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800;
        flex-shrink: 0;
    }
    .av-int { background: #e0e7ff; color: #4338ca; }
    .av-ext { background: #fce7f3; color: #be185d; }

    /* Sender */
    .m-sender {
        width: 180px; flex-shrink: 0;
        font-size: .875rem; font-weight: 500;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        color: #374151;
    }
    .m-row.unread .m-sender { font-weight: 700; color: #0f172a; }

    /* Content */
    .m-content {
        flex: 1; min-width: 0;
        display: flex; align-items: center; gap: .5rem;
    }
    .m-subject {
        font-size: .875rem; font-weight: 500;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: #374151; flex-shrink: 0; max-width: 260px;
    }
    .m-row.unread .m-subject { font-weight: 700; color: #0f172a; }
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
    .mb-done     { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .mb-default  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Date */
    .m-date {
        font-size: .78rem; color: #94a3b8; font-weight: 500;
        white-space: nowrap; flex-shrink: 0; text-align: right;
        margin-left: auto !important;
        width: 65px;
        display: block;
    }
    .m-row.unread .m-date { color: #0f172a; font-weight: 700; }

    /* Hover actions (Gmail style) */
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
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .m-act-btn:hover { background: #eef2ff; color: #4f46e5; }

    /* Checkbox */
    .m-check {
        width: 15px; height: 15px; accent-color: #16a34a;
        cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s;
    }
    .m-row:hover .m-check,
    .m-check:checked { opacity: 1; }

    /* Empty state */
    .empty-arsip {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 4rem 2rem;
        height: 100%; gap: .75rem;
    }
    .empty-arsip i { font-size: 3.5rem; color: #e2e8f0; }
    .empty-arsip h3 { font-size: 1.1rem; font-weight: 700; color: #94a3b8; margin: 0; }
    .empty-arsip p  { font-size: .85rem; color: #cbd5e1; margin: 0; max-width: 300px; }

    /* Responsive */
    @media (max-width: 768px) {
        .m-row { height: auto; padding: .75rem 1rem; flex-wrap: wrap !important; gap: .5rem; position: relative; }
        .m-sender { width: calc(100% - 100px); }
        .m-content { width: 100%; }
        .m-subject { max-width: 100%; }
        .m-snippet { display: none; }
        .m-actions { display: none !important; }
        .m-date { position: absolute; right: 1rem; top: 1rem; margin-left: 0 !important; width: auto; }
        .m-check { opacity: 0 !important; display: none; }
        .arsip-tabs .arsip-tab { padding: .55rem .85rem; font-size: .75rem; }
    }
    @media (max-width: 480px) {
        .m-avatar { display: none; }
        .m-sender { width: calc(100% - 60px); }
    }
</style>

@php
    $filterType = request('type', 'all');
    // Calculate counts for badges
    // Note: Assuming $letters is paginated. You might need real counts here if passed from controller.
@endphp

<div class="arsip-wrap">

    {{-- ── Toolbar ── --}}
    <div class="arsip-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <button class="tb-btn" title="Hapus Permanen">
            <i class="bi bi-trash3"></i>
        </button>
        <button class="tb-btn" title="Opsi">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if(isset($letters) && $letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 arsip
            @endif
        </span>
        @if(isset($letters))
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
        @endif
    </div>

    {{-- ── Filter Tabs ── --}}
    <div class="arsip-tabs">
        <a href="{{ route('letters.arsip') }}" class="arsip-tab active">
            <i class="bi bi-archive-fill"></i> Semua Arsip
            @if(isset($letters) && $letters->total() > 0)
                <span class="tab-badge muted">{{ $letters->total() }}</span>
            @endif
        </a>
    </div>

    {{-- ── Mail list ── --}}
    <div class="mail-list" id="mailList">
        @forelse($letters as $letter)
            @php
                $isUnread   = $letter->is_unread;
                $isExternal = $letter->type === 'outbound_external' || $letter->type === 'external';
                $showUrl    = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                
                // Determine sender name for display
                if ($letter->type === 'outbound_external') {
                    $senderName = $letter->external_recipient_name ?? 'Pihak Luar';
                } else {
                    $senderName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Unknown');
                }
                
                $initial    = mb_strtoupper(mb_substr($senderName, 0, 1));

                /* status badge */
                $sbClass = match($letter->status) {
                    'completed' => 'mb-done',
                    default     => 'mb-default',
                };
            @endphp

            <div onclick="window.location='{{ $showUrl }}'"
               class="m-row {{ $isUnread ? 'unread' : 'read' }}"
               data-id="{{ $letter->id }}">

                <input type="checkbox" class="m-check mail-check" onclick="event.stopPropagation()">

                <div class="m-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">
                    {{ $initial }}
                </div>

                <span class="m-sender">{{ $senderName }}</span>

                <div class="m-content">
                    {{-- badges --}}
                    <div class="d-flex align-items-center gap-1 flex-shrink-0 me-2">
                        @if($isExternal)
                            <span class="m-badge mb-ext">EXT</span>
                        @else
                            <span class="m-badge mb-default" style="background:#e0e7ff; color:#4338ca; border-color:#c7d2fe;">INT</span>
                        @endif
                        @if($letter->agenda_number)
                            <span class="m-badge mb-agenda">{{ $letter->agenda_number }}</span>
                        @endif
                        <span class="m-badge {{ $sbClass }}">Selesai</span>
                    </div>

                    <span class="m-subject">{{ $letter->subject }}</span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                {{-- Hover quick-actions --}}
                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn" title="Lihat Arsip">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <span class="m-date">
                    {{ $letter->created_at->format('d/m/Y') }}
                </span>
            </div>
        @empty
            <div class="empty-arsip">
                <i class="bi bi-archive"></i>
                <h3>Tidak ada arsip</h3>
                <p>Anda belum memiliki surat yang diarsipkan.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Select-all checkbox */
    const checkAll  = document.getElementById('checkAll');
    const checks    = document.querySelectorAll('.mail-check');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checks.forEach(c => c.checked = this.checked);
        });
    }

    /* Highlight row on check */
    checks.forEach(c => {
        c.addEventListener('change', function() {
            const row = this.closest('.m-row');
            if (this.checked) {
                row.style.background = '#fef8e7'; // light yellow highlight
            } else {
                row.style.background = '';
            }
        });
    });
});
</script>
@endpush
@endsection