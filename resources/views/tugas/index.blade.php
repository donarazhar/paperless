@extends('layouts.mailbox')
@section('title', 'Task Log — Riwayat Pekerjaan')

@section('content')
<style>
    /* ══ TASK VIEW GMAIL-STYLE ══ */
    .acc-wrap { display: flex; flex-direction: column; height: 100%; overflow: hidden; background: #fff; }
    .acc-toolbar {
        display: flex; align-items: center; gap: .5rem; padding: .6rem 1rem;
        border-bottom: 1px solid #f1f5f9; background: #fff; flex-shrink: 0; position: sticky; top: 0; z-index: 10;
    }
    .tb-check { width: 16px; height: 16px; accent-color: #6366f1; cursor: pointer; flex-shrink: 0; }
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

    .acc-infobar {
        display: flex; align-items: center; gap: .75rem; padding: .55rem 1rem;
        background: #eef2ff; border-bottom: 1px solid #c7d2fe; font-size: .78rem; color: #3730a3; font-weight: 600; flex-shrink: 0;
    }
    .acc-infobar i { font-size: .85rem; }
    .acc-role-chip {
        display: inline-flex; align-items: center; gap: .3rem; background: #e0e7ff; color: #4338ca;
        border: 1px solid #c7d2fe; border-radius: 100px; padding: .2rem .65rem; font-size: .7rem; font-weight: 700;
    }

    .mail-list { flex: 1; overflow-y: auto; }
    .m-row {
        display: flex; flex-wrap: nowrap !important; align-items: center; padding: 0 1rem; height: 52px;
        border-bottom: 1px solid #f8fafc; transition: background .12s; text-decoration: none; color: inherit;
        position: relative; cursor: pointer; gap: .65rem; background: #fff; overflow: hidden;
    }
    .m-row::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: #6366f1; transition: background .15s;
    }
    .m-row.task-acc::before { background: #dc2626; }
    .m-row.task-dispo::before { background: #9333ea; }

    .m-row.read { background: #fafafa; }
    .m-row:hover { background: #f8fafc; }
    .m-row:hover .m-actions { opacity: 1; }

    .m-avatar {
        width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0;
    }
    .av-int { background: #e0e7ff; color: #3730a3; }
    .av-ext { background: #fce7f3; color: #be185d; }

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
    }
    .mb-acc    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .mb-dispo  { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }
    .mb-ext    { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
    .mb-int    { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }

    .m-date { font-size: .78rem; font-weight: 700; color: #64748b; white-space: nowrap; flex-shrink: 0; text-align: right; margin-left: auto; min-width: 65px; }
    .m-row.task-acc .m-date { color: #dc2626; }
    .m-row.task-dispo .m-date { color: #9333ea; }

    .m-actions {
        display: flex; align-items: center; gap: .25rem; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        background: #f8fafc; padding-left: .75rem; opacity: 0; transition: opacity .15s; z-index: 5;
    }
    .m-act-btn {
        display: inline-flex; align-items: center; gap: .25rem; padding: .35rem .75rem; border: none;
        background: #fff; border-radius: 100px; cursor: pointer; font-size: .75rem; font-weight: 700;
        transition: all .15s; box-shadow: 0 1px 3px rgba(0,0,0,.08); text-decoration: none;
    }
    .m-act-btn.view { color: #64748b; }
    .m-act-btn.view:hover { background: #e2e8f0; }

    .m-check { width: 15px; height: 15px; cursor: pointer; flex-shrink: 0; opacity: 0; transition: opacity .15s; }
    .m-row:hover .m-check, .m-check:checked { opacity: 1; }

    .empty-acc { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 4rem 2rem; height: 100%; gap: .75rem; }
    .empty-acc i { font-size: 3.5rem; color: #cbd5e1; }
    .empty-acc h3 { font-size: 1.1rem; font-weight: 700; color: #64748b; margin: 0; }
    .empty-acc p  { font-size: .85rem; color: #94a3b8; margin: 0; max-width: 300px; }

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
    <div class="acc-toolbar">
        <input type="checkbox" class="tb-check" id="checkAll" title="Pilih Semua">
        <div class="tb-divider"></div>
        <button class="tb-btn" title="Muat Ulang" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i></button>
        <div class="tb-spacer"></div>
        <span class="tb-page-info">
            @if($letters->total() > 0)
                {{ $letters->firstItem() }}–{{ $letters->lastItem() }} dari {{ $letters->total() }}
            @else
                0 tugas
            @endif
        </span>
        <a href="{{ $letters->previousPageUrl() }}" class="tb-page-btn {{ $letters->onFirstPage() ? 'disabled' : '' }}"><i class="bi bi-chevron-left"></i></a>
        <a href="{{ $letters->nextPageUrl() }}" class="tb-page-btn {{ !$letters->hasMorePages() ? 'disabled' : '' }}"><i class="bi bi-chevron-right"></i></a>
    </div>

    <div class="acc-infobar">
        <i class="bi bi-clock-history"></i>
        <span>Riwayat Pekerjaan (Task Log): Surat-surat yang telah selesai Anda kerjakan.</span>
        @php $userRole = Auth::user()->role; @endphp
        <span class="acc-role-chip"><i class="bi bi-person-badge"></i> {{ ucwords(str_replace('_', ' ', $userRole)) }}</span>
        @if($letters->total() > 0)
            <span class="ms-auto" style="font-weight:700; color:#3730a3;">{{ $letters->total() }} riwayat</span>
        @endif
    </div>

    <div class="mail-list">
        @forelse($letters as $letter)
            @php
                $isUnread   = $letter->is_unread;
                $isExternal = $letter->type === 'outbound_external';
                $showUrl    = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                
                // Determine display name
                if (in_array($letter->status, ['draft', 'pending_approval'])) {
                    // Outbound scenario
                    $displayName = $isExternal ? ($letter->external_recipient_name ?: 'Tanpa Tujuan') : ($letter->recipientUnit->name ?? ($letter->recipientUser->name ?? 'Internal'));
                    $label = 'Ke:';
                } else {
                    // Inbound / Disposisi scenario
                    $displayName = $letter->sender->unit->name ?? ($letter->sender->name ?? 'Sistem');
                    $label = 'Dari:';
                }
                $initial = mb_strtoupper(mb_substr($displayName, 0, 1));
                
                $isAcc = $letter->status === 'pending_approval';
                $rowClass = $isAcc ? 'task-acc' : 'task-dispo';
            @endphp

            <div onclick="window.location='{{ $showUrl }}'" class="m-row {{ $isUnread ? '' : 'read' }} {{ $rowClass }}">
                <input type="checkbox" class="m-check mail-check" onclick="event.stopPropagation()">
                <div class="m-avatar {{ $isExternal ? 'av-ext' : 'av-int' }}">{{ $initial }}</div>

                <span class="m-to">
                    <span class="to-label">{{ $label }}</span>{{ $displayName }}
                </span>

                @php
                    $myHistory = $letter->histories->where('user_id', Auth::id())->whereIn('action', ['approved', 'disposed', 'forwarded', 'completed', 'agendakan'])->sortByDesc('created_at')->first();
                    $myAction = $myHistory ? $myHistory->action : 'unknown';
                @endphp
                <div class="m-content">
                    <div class="d-flex align-items-center gap-1 flex-shrink-0 me-2">
                        @if($myAction === 'approved')
                            <span class="m-badge" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;"><i class="bi bi-check-circle-fill"></i> Telah di-ACC</span>
                        @elseif($myAction === 'disposed')
                            <span class="m-badge" style="background:#faf5ff; color:#7e22ce; border:1px solid #e9d5ff;"><i class="bi bi-arrow-return-right"></i> Didisposisikan</span>
                        @elseif($myAction === 'forwarded')
                            <span class="m-badge" style="background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;"><i class="bi bi-forward-fill"></i> Diteruskan</span>
                        @elseif($myAction === 'completed')
                            <span class="m-badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;"><i class="bi bi-archive-fill"></i> Diarsipkan Selesai</span>
                        @elseif($myAction === 'agendakan')
                            <span class="m-badge" style="background:#f0fdfa; color:#0f766e; border:1px solid #ccfbf1;"><i class="bi bi-journal-check"></i> Diagendakan</span>
                        @else
                            <span class="m-badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;"><i class="bi bi-check2-all"></i> Selesai</span>
                        @endif
                    </div>
                    <span class="m-subject">{{ $letter->subject }}</span>
                    <span class="m-sep">—</span>
                    <span class="m-snippet">{!! Str::limit(strip_tags($letter->body), 80) !!}</span>
                </div>

                <div class="m-actions" onclick="event.stopPropagation()">
                    <button onclick="window.location='{{ $showUrl }}'" class="m-act-btn view" title="Lihat Riwayat">
                        <i class="bi bi-eye"></i> Lihat Surat
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
                <i class="bi bi-clock-history"></i>
                <h3>Belum Ada Riwayat</h3>
                <p>Anda belum menyelesaikan tugas apa pun (ACC Surat atau Tindak Lanjut Disposisi).</p>
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
