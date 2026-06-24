@extends('layouts.mailbox')
@section('title', 'Draft — Menunggu ACC')

@section('content')
<style>
    /* ══ ACC SURAT GMAIL-STYLE ══ */
    .acc-wrap {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }

    /* ── Toolbar ── */
    .acc-toolbar {
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
        accent-color: #dc2626; cursor: pointer; flex-shrink: 0;
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

    /* ── Context info bar ── */
    .acc-infobar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .55rem 1rem;
        background: #fef2f2;
        border-bottom: 1px solid #fecaca;
        font-size: .78rem;
        color: #991b1b;
        font-weight: 600;
        flex-shrink: 0;
    }
    .acc-infobar i { font-size: .85rem; }
    .acc-role-chip {
        display: inline-flex; align-items: center; gap: .3rem;
        background: #fee2e2; color: #b91c1c;
        border: 1px solid #fca5a5;
        border-radius: 100px;
        padding: .2rem .65rem;
        font-size: .7rem; font-weight: 700;
    }

    /* ── Mail list ── */
    .mail-list { flex: 1; overflow-y: auto; }

    /* ── Mail row ── (identik dengan inbox) */
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
        background: #fff;
        overflow: hidden;
    }
    .m-row::before {
        content: '';
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px;
        background: #dc2626; /* selalu merah — pending approval */
        transition: background .15s;
    }
    .m-row.read { background: #fafafa; }
    .m-row:hover { background: #fef2f2; }
    .m-row:hover .m-actions { opacity: 1; }

    /* Avatar */
    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0;
    }
    .av-int { background: #fee2e2; color: #b91c1c; }
    .av-ext { background: #fce7f3; color: #be185d; }

    /* Recipient name */
    .m-to {
        width: 170px; flex-shrink: 0;
        font-size: .875rem; font-weight: 700;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        color: #0f172a;
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
        font-size: .875rem; font-weight: 700;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        color: #0f172a; flex-shrink: 0; max-width: 240px;
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
    .mb-acc    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .mb-ext    { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
    .mb-int    { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }

    /* Date */
    .m-date {
        font-size: .78rem; font-weight: 700;
        color: #dc2626; /* merah karena urgent */
        white-space: nowrap; flex-shrink: 0; text-align: right;
        margin-left: auto;
        min-width: 65px;
    }

    /* Hover actions */
    .m-actions {
        display: flex; align-items: center; gap: .25rem;
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        background: #fdf2f8; /* pinkish hover context for ACC */
        padding-left: .75rem;
        opacity: 0; transition: opacity .15s; z-index: 5;
    }
    .m-act-btn {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .35rem .75rem; border: none;
        background: #fff; border-radius: 100px; cursor: pointer;
        font-size: .75rem; font-weight: 700;
        transition: all .15s; box-shadow: 0 1px 3px rgba(0,0,0,.08);
        text-decoration: none;
    }
    .m-act-btn.acc {
        color: #dc2626; border: 1px solid #fecaca;
    }
    .m-act-btn.acc:hover { background: #fef2f2; border-color: #dc2626; }
    .m-act-btn.view { color: #64748b; }
    .m-act-btn.view:hover { background: #f1f5f9; }

    /* Checkbox */
    .m-check {
        width: 15px; height: 15px; accent-color: #dc2626;
        cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s;
    }
    .m-row:hover .m-check,
    .m-check:checked { opacity: 1; }

    /* Empty state */
    .empty-acc {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 4rem 2rem;
        height: 100%; gap: .75rem;
    }
    .empty-acc i { font-size: 3.5rem; color: #bbf7d0; }
    .empty-acc h3 { font-size: 1.1rem; font-weight: 700; color: #15803d; margin: 0; }
    .empty-acc p  { font-size: .85rem; color: #94a3b8; margin: 0; max-width: 300px; }

    /* Responsive */
    @media (max-width: 768px) {
        .m-row     { height: auto; padding: .75rem 1rem; flex-wrap: wrap; gap: .5rem; position: relative; }
        .m-to      { width: calc(100% - 70px); }
        .m-content { width: 100%; }
        .m-subject { max-width: 100%; }
        .m-snippet { display: none; }
        .m-actions { display: none !important; }
        .m-date    { position: absolute; right: 1rem; top: 1rem; margin-left: 0; }
        .m-check   { opacity: 0 !important; display: none; }
        .acc-infobar { font-size: .72rem; flex-wrap: wrap; }
    }
    @media (max-width: 480px) {
        .m-avatar { display: none; }
        .m-to     { width: calc(100% - 60px); }
    }
</style>

<div class="acc-wrap">

    {{-- ── Toolbar ── --}}
    <div class="acc-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if($letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 surat
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

    {{-- ── Context info bar ── --}}
    <div class="acc-infobar">
        <i class="bi bi-shield-exclamation"></i>
        <span>Surat berikut memerlukan persetujuan (ACC) Anda sebelum dikirim.</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="acc-role-chip">
            <i class="bi bi-person-badge"></i>
            {{ $userRole === 'kepala_unit' ? 'Kepala Unit' : 'Subag Persuratan' }}
        </span>
        @if($letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#b91c1c;">
                {{ $letters->total() }} menunggu
            </span>
        @endif
    </div>

    {{-- ── Mail list ── --}}
    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isUnread     = $letter->is_unread;
                $isExternal   = $letter->type === 'outbound_external';
                $showUrl      = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                $recipientName = $isExternal
                    ? ($letter->external_recipient_name ?: 'Tanpa Tujuan Eksternal')
                    : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                $initial      = mb_strtoupper(mb_substr($recipientName, 0, 1));
            @endphp

            <div onclick="window.location='{{ $showUrl }}'"
               class="m-row {{ $isUnread ? '' : 'read' }}"
               data-id="{{ $letter->id }}">

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
                            <span class="m-badge mb-ext">Eksternal</span>
                        @else
                            <span class="m-badge mb-int">Internal</span>
                        @endif
                        <span class="m-badge mb-acc"><i class="bi bi-hourglass-split"></i> Perlu ACC</span>
                    </div>

                    <span class="m-subject">{{ $letter->subject }}</span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                {{-- Hover: ACC langsung atau lihat detail --}}
                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn view" title="Lihat Detail">
                        <i class="bi bi-eye"></i>
                    </button>
                    <form method="POST"
                          action="{{ route('letters.approve', $letter->id) }}"
                          onsubmit="return confirm('ACC surat ini?')">
                        @csrf
                        <button type="submit" class="m-act-btn acc" title="ACC Sekarang">
                            <i class="bi bi-check-lg"></i> ACC
                        </button>
                    </form>
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
                <i class="bi bi-check-circle-fill"></i>
                <h3>Semua Beres!</h3>
                <p>Tidak ada surat yang menunggu persetujuan (ACC) dari Anda saat ini.</p>
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