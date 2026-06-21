@extends('layouts.app')
@section('title', 'Surat Keluar Eksternal')

@push('styles')
<style>
    .inbox-hero{
        background:linear-gradient(135deg,#7e22ce 0%,#a855f7 45%,#c084fc 100%);
        border-radius:1.25rem;margin-bottom:1.25rem;padding:1.75rem 2rem;
        position:relative;overflow:hidden;
        box-shadow:0 8px 32px rgba(168,85,247,0.22);
    }
    .inbox-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.06)}
    .inbox-hero::after{content:'';position:absolute;bottom:-60px;left:30%;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.04)}
    .hero-icon{width:42px;height:42px;border-radius:12px;flex-shrink:0;background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:#fff}
    .hero-title{font-size:1.35rem;font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.2}
    .hero-sub{font-size:.82rem;color:rgba(255,255,255,0.7);font-weight:500;margin-top:.25rem}
    .stat-chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.25);border-radius:100px;padding:.4rem 1rem;font-size:.82rem;font-weight:700;color:#fff}
    .ext-badge{display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.2);color:#fff;font-size:.68rem;font-weight:700;padding:.2rem .6rem;border-radius:100px;letter-spacing:.04em}

    .filter-card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:1.15rem 1.35rem;margin-bottom:1.25rem;box-shadow:0 1px 8px rgba(15,23,42,0.04)}
    .f-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:.35rem;display:block}
    .inbox-summary{display:flex;align-items:center;justify-content:space-between;padding:.65rem 1.25rem;background:#f8fafc;border:1px solid #f1f5f9;border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem}
    .inbox-summary-text{font-size:.78rem;font-weight:600;color:#64748b}
    .inbox-summary-text strong{color:#0f172a}
    
    /* Compact Table Styling */
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
    .sp-done{background:#d1fae5;color:#065f46}

    .letter-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:1rem;padding:1rem 1.15rem;margin-bottom:.7rem;text-decoration:none;color:inherit;display:block;transition:all .18s;position:relative;overflow:hidden}
    .letter-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:#e2e8f0;border-radius:4px 0 0 4px;transition:background .18s}
    .letter-card:hover{border-color:#e9d5ff;box-shadow:0 6px 20px rgba(168,85,247,0.10);transform:translateY(-2px);color:inherit}
    .letter-card:hover::before{background:#a855f7}
    .lc-no{font-size:.67rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#94a3b8;margin-bottom:.25rem;font-family:monospace}
    .lc-subject{font-size:.9rem;font-weight:700;color:#0f172a;margin-bottom:.4rem;line-height:1.35}
    .lc-meta{font-size:.75rem;color:#64748b;display:flex;flex-wrap:wrap;gap:.6rem;align-items:center}
    .lc-meta i{font-size:.7rem}
    .lc-open{font-size:.75rem;font-weight:700;color:#9333ea;display:flex;align-items:center;gap:4px}
    
    .empty-state{text-align:center;padding:4rem 1rem}
    .empty-icon-wrap{width:80px;height:80px;background:#f3e8ff;border-radius:1.25rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:#a855f7}
    .e-title{font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:.3rem}
    .e-sub{font-size:.84rem;color:#64748b}

    .table-wrap{display:block}.cards-wrap{display:none}
    @media(max-width:900px){.table-wrap{display:none}.cards-wrap{display:block}.inbox-hero{padding:1.35rem 1.25rem}.hero-title{font-size:1.15rem}.filter-card{padding:1rem}}
    @media(max-width:576px){.inbox-hero{padding:1.15rem 1rem;border-radius:1rem;margin-bottom:1rem}.hero-title{font-size:1.05rem}.hero-sub{font-size:.75rem}.stat-chip{font-size:.75rem;padding:.3rem .75rem}.filter-card{padding:.85rem;border-radius:.85rem}.letter-card{padding:.85rem 1rem;border-radius:.85rem}.inbox-summary{padding:.5rem .85rem;border-radius:.6rem}}
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="hero-icon"><i class="bi bi-building-fill-up"></i></div>
                <div class="hero-title mb-0">Surat Keluar Eksternal</div>
                <span class="ext-badge"><i class="bi bi-building"></i> Instansi Luar</span>
            </div>
            <div class="hero-sub">Riwayat surat yang dikirim ke instansi luar</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-send-arrow-up-fill"></i> {{ $letters->total() }} surat
            </div>
            <a href="{{ route('letters.createOutboundExternal') }}" class="btn-custom success" style="width: auto;">
                <i class="bi bi-plus-lg"></i> Catat Surat Keluar
            </a>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-card">
    <form class="row gy-2 gx-2 align-items-end" method="GET">
        <div class="col-12 col-md-9">
            <label class="f-label">Cari Surat</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik nomor surat atau perihal..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
            <button type="submit" class="btn-filter flex-grow-1 justify-content-center">
                <i class="bi bi-search"></i> Cari
            </button>
            <a href="{{ request()->url() }}" class="btn-reset flex-grow-1 justify-content-center text-center">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
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

{{-- DESKTOP TABLE --}}
<div class="table-wrap table-container">
    <table class="inbox-table">
        <thead>
            <tr>
                <th style="width:105px;">No. Agenda</th>
                <th style="width:100px;">Tgl. Kirim</th>
                <th>No. Surat & Perihal</th>
                <th style="width:185px;">Tujuan & Status</th>
                <th style="width:55px;text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $letter)
                <tr>
                    <td>
                        @if($letter->agenda_number)
                            <span class="agenda-pill">{{ $letter->agenda_number }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:0.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="d-date">{{ $letter->created_at->format('d/m/Y') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-num" style="margin-bottom:3px;color:#a855f7;"><i class="bi bi-hash"></i> {{ $letter->letter_number ?: 'Belum ada nomor' }}</div>
                            <div class="s-title" title="{{ $letter->subject }}">{{ $letter->subject }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1 align-items-start">
                            <div class="fw-bold" style="font-size:0.8rem;color:#0f172a;">
                                <i class="bi bi-building-fill" style="color:#a855f7;"></i> {{ $letter->external_recipient_name }}
                            </div>
                            <div>
                                <span class="status-pill sp-done m-0">
                                    <i class="bi bi-check-circle-fill"></i> Tercatat
                                </span>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('letters.show', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}" class="btn-open" title="Buka Detail">
                            <i class="bi bi-chevron-right" style="font-size:0.9rem;margin:0;"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <div class="empty-icon-wrap"><i class="bi bi-send-arrow-up"></i></div>
                        <div class="e-title">Belum ada surat keluar eksternal</div>
                        <div class="e-sub">Surat ke instansi luar akan tercatat di sini.</div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($letters as $letter)
        <a href="{{ route('letters.show', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}" class="letter-card">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="lc-no flex-grow-1">{{ $letter->letter_number ?: 'No. belum ada' }}</div>
                <span class="ext-badge" style="background:#f3e8ff;color:#9333ea;"><i class="bi bi-building"></i> Eksternal</span>
            </div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta mb-2">
                <span><i class="bi bi-building-fill" style="color:#a855f7;"></i> {{ $letter->external_recipient_name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="status-pill sp-done"><i class="bi bi-check-circle-fill"></i> Tercatat</span>
                <span class="lc-open">Buka <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i></span>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <div class="empty-icon-wrap"><i class="bi bi-send-arrow-up"></i></div>
            <div class="e-title">Belum ada surat keluar eksternal</div>
            <div class="e-sub">Surat ke instansi luar akan tercatat di sini.</div>
        </div>
    @endforelse
</div>

@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">{{ $letters->links() }}</div>
@endif
@endsection
