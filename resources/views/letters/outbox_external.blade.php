@extends('layouts.app')
@section('title', 'Surat Keluar Eksternal')

@section('content')


{{-- Page Header --}}
<div class="inbox-hero" style="background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 45%, #2563eb 100%);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="hero-title mb-0">Surat Keluar Eksternal</div>
                <span class="ext-badge" style="background: rgba(255,255,255,0.2); color: #fff;"><i class="bi bi-building"></i> Instansi Luar</span>
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

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;">
    <table class="inbox-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th style="width:110px;">Tanggal</th>
                <th>Perihal Surat</th>
                <th style="width:200px;">Instansi Tujuan</th>
                <th style="width:120px;">Status</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $letter)
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">
                        {{ $loop->iteration + ($letters->currentPage()-1)*$letters->perPage() }}
                    </td>
                    <td>
                        <div class="date-cell">
                            <div class="d-date">{{ $letter->created_at->format('d/m/Y') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title">{{ $letter->subject }}</div>
                            <div class="s-num">{{ $letter->letter_number ?: '—' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="recip-cell">
                            <div class="r-name">{{ $letter->external_recipient_name }}</div>
                            <div class="r-type"><i class="bi bi-building me-1"></i>Instansi Luar</div>
                        </div>
                    </td>
                    <td>
                        <span class="status-pill">
                            <i class="bi bi-check-circle-fill"></i> Tercatat
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('letters.show', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}" class="btn-open">
                            Detail <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-send-arrow-up"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat keluar eksternal</p>
                        <span style="font-size:0.8rem;">Surat ke instansi luar akan tercatat di sini.</span>
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
                <span class="ext-badge"><i class="bi bi-building"></i> Eksternal</span>
            </div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta">
                <span><i class="bi bi-building-fill" style="color:#7e22ce;"></i> {{ $letter->external_recipient_name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="mt-2">
                <span class="status-pill"><i class="bi bi-check-circle-fill"></i> Tercatat</span>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <i class="bi bi-send-arrow-up"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat keluar eksternal</p>
        </div>
    @endforelse
</div>

@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">{{ $letters->links() }}</div>
@endif
@endsection
