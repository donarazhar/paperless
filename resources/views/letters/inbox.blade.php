@extends('layouts.app')
@section('title', 'Surat Masuk Internal')

@section('content')
<style>
    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1rem;
        padding: 1.1rem 1.35rem;
        margin-bottom: 1.25rem;
    }

    .filter-bar .f-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 0.35rem;
        display: block;
    }

    .filter-bar .form-control,
    .filter-bar .form-select {
        height: 40px;
        font-size: 0.865rem;
        border-radius: 0.6rem;
        border: 1.5px solid #e8edf4;
        background: #fafbfd;
        padding: 0 0.9rem;
    }

    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.09) !important;
    }

    /* ── Letter Card (Mobile) ── */
    .letter-card {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 0.9rem;
        padding: 1rem 1.1rem;
        margin-bottom: 0.65rem;
        text-decoration: none;
        color: inherit;
        display: block;
        transition: border-color .15s, box-shadow .15s, transform .12s;
        position: relative;
    }

    .letter-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 4px 16px rgba(37,99,235,0.08);
        transform: translateY(-1px);
        color: inherit;
    }

    .letter-card.has-disp {
        border-left: 3px solid #f59e0b;
    }

    .letter-card .lc-no {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }

    .letter-card .lc-subject {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
        line-height: 1.35;
    }

    .letter-card .lc-meta {
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        align-items: center;
    }

    .lc-meta i { font-size: 0.7rem; }

    /* ── Status Badge ── */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 100px;
    }

    .sp-pending   { background:#fef9c3; color:#92400e; }
    .sp-review    { background:#dbeafe; color:#1d4ed8; }
    .sp-active    { background:#ede9fe; color:#7c3aed; }
    .sp-done      { background:#dcfce7; color:#166534; }
    .sp-disp      { background:#fef3c7; color:#b45309; }
    .sp-default   { background:#f1f5f9; color:#475569; }

    /* ── Desktop Table ── */
    .letters-table { width: 100%; border-collapse: separate; border-spacing: 0; }

    .letters-table thead th {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #94a3b8;
        padding: 0.6rem 0.85rem;
        border-bottom: 1px solid #e8edf4;
        white-space: nowrap;
        background: #fafbfd;
    }

    .letters-table thead th:first-child { border-radius: 0.75rem 0 0 0; padding-left: 1.25rem; }
    .letters-table thead th:last-child  { border-radius: 0 0.75rem 0 0; padding-right: 1.25rem; }

    .letters-table tbody tr {
        transition: background .12s;
    }

    .letters-table tbody tr:hover td { background: #f8faff; }

    .letters-table tbody td {
        padding: 0.85rem 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.865rem;
        color: #334155;
    }

    .letters-table tbody td:first-child { padding-left: 1.25rem; }
    .letters-table tbody td:last-child  { padding-right: 1.25rem; }

    .letters-table tbody tr:last-child td { border-bottom: none; }

    .subject-cell .s-title { font-size: 0.875rem; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
    .subject-cell .s-num   { font-size: 0.7rem; font-weight: 600; color: #94a3b8; letter-spacing: 0.03em; }

    .sender-cell .s-name { font-weight: 600; color: #0f172a; font-size: 0.85rem; }
    .sender-cell .s-unit { font-size: 0.72rem; color: #94a3b8; margin-top: 1px; }

    .date-cell .d-date { font-weight: 600; font-size: 0.835rem; color: #334155; }
    .date-cell .d-time { font-size: 0.72rem; color: #94a3b8; margin-top: 1px; }

    /* ── Action Button ── */
    .btn-open {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        color: #2563eb;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.4rem 0.85rem;
        text-decoration: none;
        transition: background .15s, color .15s;
        white-space: nowrap;
    }

    .btn-open:hover { background: #dbeafe; color: #1d4ed8; }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #94a3b8;
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 0.75rem; color: #cbd5e1; }
    .empty-state p { font-size: 0.9rem; }

    /* ── Pagination ── */
    .pagination .page-link { border-radius: 0.5rem !important; font-size: 0.85rem; border-color: #e8edf4; color: #475569; margin: 0 2px; }
    .pagination .page-item.active .page-link { background: #2563eb; border-color: #2563eb; }

    /* ── Responsive ── */
    .table-wrap  { display: block; }
    .cards-wrap  { display: none; }

    @media (max-width: 900px) {
        .table-wrap { display: none; }
        .cards-wrap { display: block; }
    }

    @media (max-width: 600px) {
        .filter-bar { padding: 0.9rem 1rem; }
    }
</style>

{{-- ── Page Header ── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">Surat Masuk Internal</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Antrean surat masuk &amp; disposisi untuk unit Anda</p>
    </div>
    <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:0.78rem; padding:0.45rem 0.9rem; border-radius:100px;">
        <i class="bi bi-envelope-fill me-1"></i>{{ $letters->total() }} surat
    </span>
</div>

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="f-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nomor atau perihal…" value="{{ request('search') }}">
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <label class="f-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="pending_agenda"      @selected(request('status')=='pending_agenda')>Antre Agenda</option>
                <option value="in_review_kasubag"   @selected(request('status')=='in_review_kasubag')>Review</option>
                <option value="in_consideration"    @selected(request('status')=='in_consideration')>Disposisi Aktif</option>
                <option value="completed"           @selected(request('status')=='completed')>Selesai</option>
            </select>
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <label class="f-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <label class="f-label">Sampai</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-6 col-sm-auto d-flex gap-2 align-items-end">
            <button class="btn btn-primary" style="height:40px; border-radius:0.6rem; font-size:0.85rem; padding:0 1rem;">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <a href="{{ request()->url() }}" class="btn btn-light border" style="height:40px; border-radius:0.6rem; font-size:0.85rem; padding:0 0.9rem;">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

{{-- ── DESKTOP TABLE ── --}}
<div class="table-wrap" style="background:#fff; border:1px solid #e8edf4; border-radius:1rem; overflow:hidden;">
    <table class="letters-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th style="width:100px;">Tanggal</th>
                <th>Perihal Surat</th>
                <th style="width:160px;">Pengirim</th>
                <th style="width:180px;">Status / Disposisi</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $letter)
                @php
                    $authUser = Auth::user();
                    $disp = $letter->dispositions->first(fn($d) =>
                        $d->to_user_id === $authUser->id || $d->to_unit_id === $authUser->unit_id
                    );
                    $status = $letter->status;
                    $pillClass = match($status) {
                        'pending_agenda'    => 'sp-pending',
                        'in_review_kasubag' => 'sp-review',
                        'in_consideration'  => 'sp-active',
                        'completed'         => 'sp-done',
                        default             => 'sp-default',
                    };
                    $pillText = match($status) {
                        'pending_agenda'    => 'Antre Agenda',
                        'in_review_kasubag' => 'Review Kasubag',
                        'in_consideration'  => 'Disposisi Aktif',
                        'completed'         => 'Selesai',
                        default             => ucfirst($status),
                    };
                    $pillIcon = match($status) {
                        'pending_agenda'    => 'bi-hourglass-split',
                        'in_review_kasubag' => 'bi-eye-fill',
                        'in_consideration'  => 'bi-arrow-repeat',
                        'completed'         => 'bi-check-circle-fill',
                        default             => 'bi-info-circle',
                    };
                    $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                @endphp
                <tr>
                    <td style="color:#94a3b8; font-size:0.78rem; font-weight:600;">
                        {{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="d-date">{{ $letter->created_at->format('d M Y') }}</div>
                            <div class="d-time">{{ $letter->created_at->format('H:i') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title">{{ $letter->subject }}</div>
                            <div class="s-num">{{ $letter->letter_number ?: '—' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="sender-cell">
                            <div class="s-name">{{ $letter->sender->name }}</div>
                            <div class="s-unit">{{ $letter->sender->unit->name ?? '' }}</div>
                        </div>
                    </td>
                    <td>
                        @if($disp)
                            <div class="status-pill sp-disp mb-1">
                                <i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi
                            </div>
                            <div style="font-size:0.72rem; color:#92400e; font-style:italic; line-height:1.3;">
                                "{{ \Illuminate\Support\Str::limit($disp->note, 40) }}"
                            </div>
                        @else
                            <span class="status-pill {{ $pillClass }}">
                                <i class="bi {{ $pillIcon }}"></i> {{ $pillText }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $showUrl }}" class="btn-open">
                            Buka <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p class="fw-semibold mb-1" style="color:#475569;">Tidak ada surat masuk</p>
                            <span style="font-size:0.8rem;">Semua antrean surat telah diproses.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── MOBILE CARDS ── --}}
<div class="cards-wrap">
    @forelse($letters as $letter)
        @php
            $authUser = Auth::user();
            $disp = $letter->dispositions->first(fn($d) =>
                $d->to_user_id === $authUser->id || $d->to_unit_id === $authUser->unit_id
            );
            $status = $letter->status;
            $pillClass = match($status) {
                'pending_agenda'    => 'sp-pending',
                'in_review_kasubag' => 'sp-review',
                'in_consideration'  => 'sp-active',
                'completed'         => 'sp-done',
                default             => 'sp-default',
            };
            $pillText = match($status) {
                'pending_agenda'    => 'Antre Agenda',
                'in_review_kasubag' => 'Review Kasubag',
                'in_consideration'  => 'Disposisi Aktif',
                'completed'         => 'Selesai',
                default             => ucfirst($status),
            };
        @endphp
        <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
           class="letter-card {{ $disp ? 'has-disp' : '' }}">
            <div class="lc-no">{{ $letter->letter_number ?: 'No. belum ada' }}</div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta">
                <span><i class="bi bi-person-fill"></i> {{ $letter->sender->name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="mt-2">
                @if($disp)
                    <span class="status-pill sp-disp"><i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi</span>
                @else
                    <span class="status-pill {{ $pillClass }}">{{ $pillText }}</span>
                @endif
            </div>
        </a>
    @empty
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Tidak ada surat masuk</p>
            <span style="font-size:0.8rem;">Semua antrean surat telah diproses.</span>
        </div>
    @endforelse
</div>

{{-- ── Pagination ── --}}
@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $letters->links() }}
    </div>
@endif

@endsection