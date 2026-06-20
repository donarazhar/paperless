@extends('layouts.app')
@section('title', 'Surat Masuk Internal')

@section('content')
<style>
/* ── CSS Variables ── */
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-soft: #eff6ff;
    --primary-mid: #dbeafe;
    --accent: #7c3aed;
    --accent-soft: #f5f3ff;
    --amber: #f59e0b;
    --amber-soft: #fef3c7;
    --green: #10b981;
    --green-soft: #d1fae5;
    --red: #ef4444;
    --red-soft: #fee2e2;
    --surface: #ffffff;
    --surface-2: #f8fafc;
    --border: #e2e8f0;
    --border-light: #f1f5f9;
    --text: #0f172a;
    --text-2: #334155;
    --muted: #64748b;
    --muted-light: #94a3b8;
}

/* ── Page Hero ── */
.inbox-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 45%, #7c3aed 100%);
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(37,99,235,0.22);
}

.inbox-hero::before {
    content:'';
    position:absolute;
    top:-40px; right:-40px;
    width:220px; height:220px;
    border-radius:50%;
    background: rgba(255,255,255,0.06);
}
.inbox-hero::after {
    content:'';
    position:absolute;
    bottom:-60px; left:30%;
    width:300px; height:300px;
    border-radius:50%;
    background: rgba(255,255,255,0.04);
}

.inbox-hero .hero-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.04em;
    margin-bottom: 0.25rem;
}
.inbox-hero .hero-sub {
    font-size: 0.83rem;
    color: rgba(255,255,255,0.72);
    font-weight: 500;
}

.stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 100px;
    padding: 0.4rem 1rem;
    font-size: 0.82rem;
    font-weight: 700;
    color: #fff;
}

/* ── Filter Card ── */
.filter-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 8px rgba(15,23,42,0.04);
}

.filter-card .f-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--muted-light);
    margin-bottom: 0.35rem;
    display: block;
}

.filter-card .form-control,
.filter-card .form-select {
    height: 40px;
    font-size: 0.855rem;
    border-radius: 0.65rem;
    border: 1.5px solid var(--border);
    background: var(--surface-2);
    padding: 0 0.9rem;
    transition: all .18s;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3.5px rgba(37,99,235,0.1) !important;
}

.btn-filter {
    height: 40px;
    background: var(--primary);
    border: none;
    color: #fff;
    border-radius: 0.65rem;
    font-size: 0.855rem;
    font-weight: 700;
    padding: 0 1.15rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .18s;
    white-space: nowrap;
}
.btn-filter:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37,99,235,0.28);
}

.btn-reset {
    height: 40px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    color: var(--muted);
    border-radius: 0.65rem;
    font-size: 0.855rem;
    font-weight: 600;
    padding: 0 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .18s;
    text-decoration: none;
}
.btn-reset:hover {
    background: var(--primary-soft);
    border-color: var(--primary-mid);
    color: var(--primary);
}

/* ── Table Container ── */
.table-container {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.15rem;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(15,23,42,0.05);
}

/* ── Table ── */
.inbox-table { width: 100%; border-collapse: separate; border-spacing: 0; }

.inbox-table thead tr {
    background: var(--surface-2);
}
.inbox-table thead th {
    font-size: 0.685rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted-light);
    padding: 0.85rem 1rem;
    border-bottom: 1.5px solid var(--border-light);
    white-space: nowrap;
}
.inbox-table thead th:first-child { padding-left: 1.5rem; }
.inbox-table thead th:last-child  { padding-right: 1.5rem; }

.inbox-table tbody tr {
    transition: background .14s, transform .1s;
    position: relative;
}
.inbox-table tbody tr:hover td {
    background: #f0f7ff;
}
.inbox-table tbody td {
    padding: 1rem 1rem;
    border-bottom: 1px solid var(--border-light);
    vertical-align: middle;
    font-size: 0.87rem;
    color: var(--text-2);
}
.inbox-table tbody td:first-child { padding-left: 1.5rem; }
.inbox-table tbody td:last-child  { padding-right: 1.5rem; }
.inbox-table tbody tr:last-child td { border-bottom: none; }

/* Row number badge */
.row-num {
    width: 28px; height: 28px;
    background: var(--border-light);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--muted);
}

/* Subject cell */
.subject-cell .s-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 3px;
    line-height: 1.35;
}
.subject-cell .s-num {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--muted-light);
    letter-spacing: 0.03em;
    font-family: 'SFMono-Regular', Consolas, monospace;
}

/* Sender cell */
.sender-cell {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.sender-avatar {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: #fff;
    font-size: 0.78rem;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sender-cell .s-name { font-weight: 700; color: var(--text); font-size: 0.84rem; }
.sender-cell .s-unit { font-size: 0.7rem; color: var(--muted-light); margin-top: 1px; }

/* Date cell */
.date-cell .d-date { font-weight: 700; font-size: 0.845rem; color: var(--text-2); }
.date-cell .d-time { font-size: 0.71rem; color: var(--muted-light); margin-top: 2px; }

/* ── Status Pills ── */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.3rem 0.7rem;
    border-radius: 100px;
    white-space: nowrap;
}

.sp-pending   { background: #fef9c3; color: #92400e; }
.sp-review    { background: var(--primary-mid); color: var(--primary-dark); }
.sp-active    { background: var(--accent-soft); color: var(--accent); }
.sp-done      { background: var(--green-soft); color: #065f46; }
.sp-disp      { background: var(--amber-soft); color: #92400e; border: 1px solid #fde68a; }
.sp-default   { background: var(--border-light); color: var(--muted); }

.disp-note {
    font-size: 0.7rem;
    color: #92400e;
    font-style: italic;
    line-height: 1.4;
    margin-top: 4px;
    max-width: 180px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Action Button ── */
.btn-open {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1.5px solid var(--primary-mid);
    border-radius: 0.6rem;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.42rem 0.9rem;
    text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
}
.btn-open:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.25);
}

/* ── Empty State ── */
.empty-state {
    text-align: center;
    padding: 5rem 1rem;
}
.empty-icon-wrap {
    width: 88px; height: 88px;
    background: var(--primary-soft);
    border-radius: 1.5rem;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    font-size: 2.5rem;
    color: var(--primary);
}
.empty-state .e-title { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 0.4rem; }
.empty-state .e-sub { font-size: 0.84rem; color: var(--muted); }

/* ── Pagination ── */
.pagination .page-link {
    border-radius: 0.55rem !important;
    font-size: 0.84rem;
    font-weight: 600;
    border-color: var(--border);
    color: var(--muted);
    margin: 0 2px;
    padding: 0.45rem 0.75rem;
    transition: all .15s;
}
.pagination .page-link:hover { background: var(--primary-soft); color: var(--primary); border-color: var(--primary-mid); }
.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(37,99,235,0.3);
}

/* ── Mobile Cards ── */
.letter-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 1rem;
    padding: 1rem 1.1rem;
    margin-bottom: 0.7rem;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: all .18s;
    position: relative;
    overflow: hidden;
}
.letter-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--border);
    border-radius: 4px 0 0 4px;
    transition: background .18s;
}
.letter-card:hover {
    border-color: var(--primary-mid);
    box-shadow: 0 6px 20px rgba(37,99,235,0.10);
    transform: translateY(-2px);
    color: inherit;
}
.letter-card:hover::before { background: var(--primary); }
.letter-card.has-disp::before { background: var(--amber); }
.letter-card.has-disp { border-color: #fde68a; }

.lc-no {
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--muted-light);
    margin-bottom: 0.25rem;
    font-family: monospace;
}
.lc-subject {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.4rem;
    line-height: 1.35;
}
.lc-meta {
    font-size: 0.75rem;
    color: var(--muted);
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-items: center;
}
.lc-meta i { font-size: 0.7rem; }

/* ── Responsive ── */
.table-wrap  { display: block; }
.cards-wrap  { display: none; }

@media (max-width: 900px) {
    .table-wrap { display: none; }
    .cards-wrap { display: block; }
    .inbox-hero { padding: 1.35rem 1.25rem; }
    .inbox-hero .hero-title { font-size: 1.2rem; }
    .filter-card { padding: 1rem; }
}
@media (max-width: 576px) {
    .inbox-hero { border-radius: 1rem; }
}
</style>

{{-- ── HERO HEADER ── --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:#fff;backdrop-filter:blur(4px);">
                    <i class="bi bi-envelope-arrow-down-fill"></i>
                </div>
                <div class="hero-title">Surat Masuk Internal</div>
            </div>
            <div class="hero-sub">Daftar surat masuk &amp; disposisi untuk unit Anda</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="stat-chip">
                <i class="bi bi-envelope-fill"></i>
                {{ $letters->total() }} Surat
            </div>
            @if(request('search') || request('status') || request('date_from'))
            <div class="stat-chip" style="background:rgba(245,158,11,0.25);border-color:rgba(245,158,11,0.4);">
                <i class="bi bi-funnel-fill"></i> Difilter
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── FILTER CARD ── --}}
<div class="filter-card">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-sm-6 col-md-3">
            <label class="f-label">Cari Surat</label>
            <input type="text" name="search" class="form-control" placeholder="Nomor atau perihal…" value="{{ request('search') }}">
        </div>
        <div class="col-6 col-sm-4 col-md-2">
            <label class="f-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending_agenda"    @selected(request('status')=='pending_agenda')>Antre Agenda</option>
                <option value="in_review_kasubag" @selected(request('status')=='in_review_kasubag')>Review</option>
                <option value="in_consideration"  @selected(request('status')=='in_consideration')>Disposisi Aktif</option>
                <option value="completed"         @selected(request('status')=='completed')>Selesai</option>
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
            <button type="submit" class="btn-filter">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <a href="{{ request()->url() }}" class="btn-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- ── DESKTOP TABLE ── --}}
<div class="table-wrap table-container">
    <table class="inbox-table">
        <thead>
            <tr>
                <th style="width:50px;">#</th>
                <th style="width:110px;">Tanggal</th>
                <th>Perihal Surat</th>
                <th style="width:180px;">Pengirim</th>
                <th style="width:190px;">Status / Disposisi</th>
                <th style="width:110px;">Aksi</th>
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
                    $senderInitial = strtoupper(substr($letter->sender->name ?? 'A', 0, 1));
                @endphp
                <tr>
                    <td>
                        <div class="row-num">
                            {{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}
                        </div>
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="d-date">{{ $letter->created_at->format('d M Y') }}</div>
                            <div class="d-time"><i class="bi bi-clock me-1"></i>{{ $letter->created_at->format('H:i') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title">{{ $letter->subject }}</div>
                            <div class="s-num">{{ $letter->letter_number ?: '— Belum ada nomor' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="sender-cell">
                            <div class="sender-avatar">{{ $senderInitial }}</div>
                            <div>
                                <div class="s-name">{{ $letter->sender->name }}</div>
                                <div class="s-unit">{{ $letter->sender->unit->name ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($disp)
                            <span class="status-pill sp-disp">
                                <i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi
                            </span>
                            <div class="disp-note">"{{ \Illuminate\Support\Str::limit($disp->note, 50) }}"</div>
                        @else
                            <span class="status-pill {{ $pillClass }}">
                                <i class="bi {{ $pillIcon }}"></i> {{ $pillText }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $showUrl }}" class="btn-open">
                            <i class="bi bi-eye-fill"></i> Buka
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon-wrap"><i class="bi bi-inbox"></i></div>
                            <div class="e-title">Tidak ada surat masuk</div>
                            <div class="e-sub">Semua antrean surat telah diproses atau belum ada surat masuk.</div>
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
            $pillIcon = match($status) {
                'pending_agenda'    => 'bi-hourglass-split',
                'in_review_kasubag' => 'bi-eye-fill',
                'in_consideration'  => 'bi-arrow-repeat',
                'completed'         => 'bi-check-circle-fill',
                default             => 'bi-info-circle',
            };
        @endphp
        <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
           class="letter-card {{ $disp ? 'has-disp' : '' }}">
            <div class="lc-no">{{ $letter->letter_number ?: 'No. belum ada' }}</div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta mb-2">
                <span><i class="bi bi-person-fill"></i> {{ $letter->sender->name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                @if($disp)
                    <span class="status-pill sp-disp"><i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi</span>
                @else
                    <span class="status-pill {{ $pillClass }}"><i class="bi {{ $pillIcon }}"></i> {{ $pillText }}</span>
                @endif
                <span style="font-size:0.75rem;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:4px;">
                    Buka <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i>
                </span>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <div class="empty-icon-wrap"><i class="bi bi-inbox"></i></div>
            <div class="e-title">Tidak ada surat masuk</div>
            <div class="e-sub">Semua antrean surat telah diproses.</div>
        </div>
    @endforelse
</div>

{{-- ── Pagination ── --}}
@if($letters->hasPages())
    <div class="mt-4 d-flex justify-content-end">
        {{ $letters->links() }}
    </div>
@endif

@endsection