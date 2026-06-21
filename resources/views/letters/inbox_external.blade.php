@extends('layouts.app')
@section('title', 'Surat Masuk Eksternal')

@push('styles')
<style>
    .ext-badge{display:inline-flex;align-items:center;gap:4px;background:var(--primary-soft);color:var(--primary-dark);font-size:.68rem;font-weight:700;padding:.2rem .6rem;border-radius:100px;letter-spacing:.04em}
    .filter-card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.15rem 1.35rem;margin-bottom:1.25rem;box-shadow:0 1px 8px rgba(15,23,42,0.04)}
    .f-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:.35rem;display:block}
    .inbox-summary{display:flex;align-items:center;justify-content:space-between;padding:.65rem 1.25rem;background:#f8fafc;border:1px solid #f1f5f9;border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem}
    .inbox-summary-text{font-size:.78rem;font-weight:600;color:#64748b}
    .inbox-summary-text strong{color:#0f172a}
    .table-container{background:#fff;border:1px solid #e2e8f0;border-radius:1.15rem;overflow:hidden;box-shadow:0 2px 12px rgba(15,23,42,0.05)}
    .inbox-table{width:100%;border-collapse:separate;border-spacing:0;table-layout:fixed}
    .inbox-table thead tr{background:#f8fafc}
    .inbox-table thead th{font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#64748b;padding:.65rem .75rem;border-bottom:1.5px solid #e2e8f0;white-space:nowrap}
    .inbox-table thead th:first-child{padding-left:1.25rem}
    .inbox-table thead th:last-child{padding-right:1.25rem}
    .inbox-table tbody tr{transition:background .15s;position:relative}
    .inbox-table tbody tr:hover td{background:#f8fafc}
    .inbox-table tbody td{padding:.65rem .75rem;border-bottom:1px solid #f1f5f9;vertical-align:top;font-size:.8rem;color:#334155}
    .inbox-table tbody td:first-child{padding-left:1.25rem}
    .inbox-table tbody td:last-child{padding-right:1.25rem}
    .inbox-table tbody tr:last-child td{border-bottom:none}
    .row-num{width:24px;height:24px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#64748b}
    .subject-cell .s-title{font-size:.78rem;font-weight:700;color:#0f172a;margin-bottom:2px;line-height:1.4;white-space:normal;word-break:break-word}
    .subject-cell .s-num{font-size:.65rem;font-weight:600;color:#64748b;letter-spacing:.02em;font-family:'SFMono-Regular',Consolas,monospace;white-space:normal;word-break:break-word}
    .agenda-pill{display:inline-flex;align-items:center;justify-content:center;padding:.2rem .4rem;border-radius:.3rem;background:#f1f5f9;color:#0f172a;font-size:.7rem;font-weight:700;font-family:'SFMono-Regular',Consolas,monospace;min-width:45px;white-space:nowrap}
    .date-cell .d-date{font-weight:600;font-size:.8rem;color:#334155;white-space:nowrap}
    .btn-open{display:inline-flex;align-items:center;justify-content:center;background:#f3e8ff;color:#9333ea;border:1px solid #e9d5ff;border-radius:.45rem;width:30px;height:30px;text-decoration:none;transition:all .15s;}
    .btn-open:hover{background:#9333ea;color:#fff;border-color:#9333ea;transform:translateY(-1px);box-shadow:0 2px 8px rgba(147,51,234,0.25)}
    .status-pill{display:inline-flex;align-items:center;gap:4px;font-size:.68rem;font-weight:700;padding:.25rem .6rem;border-radius:100px;white-space:nowrap}
    .sp-pending{background:#fef9c3;color:#92400e}
    .sp-review{background:#e0e7ff;color:#4f46e5}
    .sp-active{background:#f5f3ff;color:#8b5cf6}
    .sp-done{background:#d1fae5;color:#065f46}
    .sp-disp{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
    .disp-note{font-size:.7rem;color:#92400e;font-style:italic;line-height:1.3;margin-top:2px;max-width:180px;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .letter-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:1rem;padding:1rem 1.15rem;margin-bottom:.7rem;text-decoration:none;color:inherit;display:block;transition:all .18s;position:relative;overflow:hidden}
    .letter-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:#e2e8f0;border-radius:4px 0 0 4px;transition:background .18s}
    .letter-card:hover{border-color:#ede9fe;box-shadow:0 6px 20px rgba(124,58,237,0.10);transform:translateY(-2px);color:inherit}
    .letter-card:hover::before{background:#7c3aed}
    .letter-card.has-disp::before{background:#f59e0b}
    .letter-card.has-disp{border-color:#fde68a}
    .lc-no{font-size:.67rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#94a3b8;margin-bottom:.25rem;font-family:monospace}
    .lc-subject{font-size:.9rem;font-weight:700;color:#0f172a;margin-bottom:.4rem;line-height:1.35}
    .lc-meta{font-size:.75rem;color:#64748b;display:flex;flex-wrap:wrap;gap:.6rem;align-items:center}
    .lc-meta i{font-size:.7rem}
    .lc-open{font-size:.75rem;font-weight:700;color:#7c3aed;display:flex;align-items:center;gap:4px}
    .empty-state{text-align:center;padding:4rem 1rem}
    .empty-icon-wrap{width:80px;height:80px;background:#f5f3ff;border-radius:1.25rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:#7c3aed}
    .e-title{font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:.3rem}
    .e-sub{font-size:.84rem;color:#64748b}
    .table-wrap{display:block}.cards-wrap{display:none}
    @media(max-width:900px){.table-wrap{display:none}.cards-wrap{display:block}.inbox-hero{padding:1.35rem 1.25rem}.hero-title{font-size:1.15rem}.filter-card{padding:1rem}}
    @media(max-width:576px){.inbox-hero{padding:1.15rem 1rem;border-radius:1rem;margin-bottom:1rem}.hero-title{font-size:1.05rem}.hero-sub{font-size:.75rem}.stat-chip{font-size:.75rem;padding:.3rem .75rem}.filter-card{padding:.85rem;border-radius:.85rem}.letter-card{padding:.85rem 1rem;border-radius:.85rem}.inbox-summary{padding:.5rem .85rem;border-radius:.6rem}}
</style>
@endpush

@section('content')

<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="hero-icon"><i class="bi bi-building-fill"></i></div>
                <div class="hero-title">Surat Masuk Eksternal</div>
                <span class="ext-badge"><i class="bi bi-building"></i> Instansi Luar</span>
            </div>
            <div class="hero-sub">Surat dari instansi luar yang telah diinput &amp; diarsipkan</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="stat-chip"><i class="bi bi-envelope-exclamation-fill"></i> {{ $letters->total() }} surat</div>
            @if(in_array(Auth::user()->role, ['admin', 'admin_sekretariat', 'subag_persuratan', 'bagian_tu']))
            <a href="{{ route('letters.createExternal') }}" class="btn-custom success" style="width:auto;">
                <i class="bi bi-plus-lg"></i> Buat Surat Eksternal
            </a>
            @endif
        </div>
    </div>
</div>

<div class="filter-card">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-md-9">
            <label class="f-label">Cari Surat</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik nomor surat atau perihal..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
            <button type="submit" class="btn-filter flex-grow-1 justify-content-center"><i class="bi bi-search"></i> Cari</button>
            <a href="{{ request()->url() }}" class="btn-reset flex-grow-1 justify-content-center text-center"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>
</div>

@if($letters->total() > 0)
<div class="inbox-summary">
    <div class="inbox-summary-text">Menampilkan <strong>{{ $letters->firstItem() }}–{{ $letters->lastItem() }}</strong> dari <strong>{{ $letters->total() }}</strong> surat</div>
    @if($letters->hasPages())
    <div class="inbox-summary-text">Halaman <strong>{{ $letters->currentPage() }}</strong> dari <strong>{{ $letters->lastPage() }}</strong></div>
    @endif
</div>
@endif

<div class="table-wrap table-container">
    <table class="inbox-table">
        <thead><tr>
            <th style="width:105px;">No. Agenda</th>
            <th style="width:100px;">Tgl. Terima</th>
            <th style="width:180px;">Asal Surat</th>
            <th>No. Surat & Perihal</th>
            <th style="width:105px;">Tgl. Disposisi</th>
            <th style="width:160px;">Tujuan Disposisi</th>
            <th style="width:55px;text-align:center;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($letters as $letter)
                @php
                    $authUser = Auth::user();
                    $disp = $letter->dispositions->sortByDesc('created_at')->first(fn($d) =>
                        $d->to_user_id === $authUser->id || $d->to_unit_id === $authUser->unit_id
                    );
                    $status = $letter->status;
                    $pillClass = match($status) {
                        'pending_approval'      => 'sp-pending',
                        'pending_sending'       => 'sp-review',
                        'pending_agenda'        => 'sp-pending',
                        'in_review_subag'       => 'sp-review',
                        'in_review_bagian_tu'   => 'sp-review',
                        'in_consideration'      => 'sp-active',
                        'completed'             => 'sp-done',
                        default                 => 'sp-default',
                    };
                    $pillText = match($status) {
                        'pending_approval'      => 'Menunggu ACC Kepala',
                        'pending_sending'       => 'Menunggu Dikirim',
                        'pending_agenda'        => 'Antre Agenda',
                        'in_review_subag'       => 'Review Subag',
                        'in_review_bagian_tu'   => 'Review Bagian TU',
                        'in_consideration'      => 'Disposisi Aktif',
                        'completed'             => 'Selesai',
                        default                 => ucfirst(str_replace('_', ' ', $status)),
                    };
                    $pillIcon = match($status) {
                        'pending_approval'      => 'bi-clock',
                        'pending_sending'       => 'bi-send',
                        'pending_agenda'        => 'bi-hourglass-split',
                        'in_review_subag'       => 'bi-envelope-paper',
                        'in_review_bagian_tu'   => 'bi-eye-fill',
                        'in_consideration'      => 'bi-arrow-repeat',
                        'completed'             => 'bi-check-circle-fill',
                        default                 => 'bi-info-circle',
                    };
                    $showUrl = route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]);
                @endphp
                @php
                    $latestDisp = $letter->dispositions->sortByDesc('created_at')->first();
                @endphp
                <tr>
                    <td>
                        @if($letter->agenda_number)
                            <span class="agenda-pill">{{ $letter->agenda_number }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td><div class="date-cell"><div class="d-date">{{ $letter->created_at->format('d/m/Y') }}</div></div></td>
                    <td>
                        <div class="d-flex flex-column gap-1 align-items-start">
                            <div class="fw-bold" style="font-size:0.8rem;color:#0f172a;"><i class="bi bi-building-fill" style="color:#7e22ce;"></i> {{ $letter->external_sender_name }}</div>
                            <div>
                                @if($disp && $letter->status !== 'in_review_kasubag')
                                    <span class="status-pill sp-disp m-0" style="display:inline-flex;margin-bottom:0.15rem !important;">
                                        <i class="bi bi-exclamation-circle-fill"></i> Disposisi dr. {{ $disp->fromUser->unit->name ?? $disp->fromUser->name }}
                                    </span>
                                    <div class="disp-note" title="{{ $disp->note }}">"{{ \Illuminate\Support\Str::limit($disp->note, 40) }}"</div>
                                @else
                                    <span class="status-pill {{ $pillClass }} m-0"><i class="bi {{ $pillIcon }}"></i> {{ $pillText }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-num" style="margin-bottom:3px;color:#9333ea;"><i class="bi bi-hash"></i> {{ $letter->letter_number ?: 'Belum ada nomor' }}</div>
                            <div class="s-title" title="{{ $letter->subject }}">{{ $letter->subject }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="date-cell">
                            @if($latestDisp)
                                <div class="d-date">{{ $latestDisp->created_at->format('d/m/Y') }}</div>
                                <div style="font-size:0.65rem;color:#64748b;">{{ $latestDisp->created_at->format('H:i') }}</div>
                            @else
                                <span style="font-size:0.8rem;color:#cbd5e1;">—</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            @if($latestDisp)
                                <div class="fw-bold" style="font-size:0.75rem;color:#0f172a;"><i class="bi bi-geo-alt-fill" style="color:#ef4444;font-size:0.65rem;"></i> {{ $latestDisp->toUnit->name ?? $latestDisp->toUser->name ?? '—' }}</div>
                            @else
                                <span style="font-size:0.75rem;color:#64748b;">Belum didisposisi</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ $showUrl }}" class="btn-open" title="Buka Detail">
                            <i class="bi bi-chevron-right" style="font-size:0.9rem;margin:0;"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon-wrap"><i class="bi bi-envelope-exclamation"></i></div>
                        <div class="e-title">Belum ada surat masuk eksternal</div>
                        <div class="e-sub">Surat dari instansi luar akan muncul di sini setelah diinput.</div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="cards-wrap">
    @forelse($letters as $letter)
        @php
            $authUser = Auth::user();
            $disp = $letter->dispositions->first(fn($d) =>
                $d->to_user_id === $authUser->id || $d->to_unit_id === $authUser->unit_id
            );
            $status = $letter->status;
                    $pillClass = match($status) {
                        'pending_approval'      => 'sp-pending',
                        'pending_sending'       => 'sp-review',
                        'pending_agenda'        => 'sp-pending',
                        'in_review_subag'       => 'sp-review',
                        'in_review_bagian_tu'   => 'sp-review',
                        'in_consideration'      => 'sp-active',
                        'completed'             => 'sp-done',
                        default                 => 'sp-default',
                    };
                    $pillText = match($status) {
                        'pending_approval'      => 'Menunggu ACC Kepala',
                        'pending_sending'       => 'Menunggu Dikirim',
                        'pending_agenda'        => 'Antre Agenda',
                        'in_review_subag'       => 'Review Subag',
                        'in_review_bagian_tu'   => 'Review Bagian TU',
                        'in_consideration'      => 'Disposisi Aktif',
                        'completed'             => 'Selesai',
                        default                 => ucfirst(str_replace('_', ' ', $status)),
                    };
                    $pillIcon = match($status) {
                        'pending_approval'      => 'bi-clock',
                        'pending_sending'       => 'bi-send',
                        'pending_agenda'        => 'bi-hourglass-split',
                        'in_review_subag'       => 'bi-envelope-paper',
                        'in_review_bagian_tu'   => 'bi-eye-fill',
                        'in_consideration'      => 'bi-arrow-repeat',
                        'completed'             => 'bi-check-circle-fill',
                        default                 => 'bi-info-circle',
                    };
        @endphp
        <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
           class="letter-card {{ $disp ? 'has-disp' : '' }}">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="lc-no flex-grow-1">{{ $letter->letter_number ?: 'No. belum ada' }}</div>
                <span class="ext-badge" style="background:#fdf4ff;color:#7e22ce;"><i class="bi bi-building"></i> Eksternal</span>
            </div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta mb-2">
                <span><i class="bi bi-building-fill" style="color:#7e22ce;"></i> {{ $letter->external_sender_name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                @if($disp)
                    <span class="status-pill sp-disp"><i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi</span>
                @else
                    <span class="status-pill {{ $pillClass }}"><i class="bi {{ $pillIcon }}"></i> {{ $pillText }}</span>
                @endif
                <span class="lc-open">Buka <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i></span>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <div class="empty-icon-wrap"><i class="bi bi-envelope-exclamation"></i></div>
            <div class="e-title">Belum ada surat masuk eksternal</div>
            <div class="e-sub">Surat dari instansi luar akan muncul di sini setelah diinput.</div>
        </div>
    @endforelse
</div>

@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">{{ $letters->links() }}</div>
@endif

@endsection
