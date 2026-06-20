@extends('layouts.app')
@section('title', 'Surat Keluar Internal')

@section('content')


{{-- Page Header --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="hero-title">Surat Keluar Internal</div>
            <div class="hero-sub">Daftar surat yang telah dikirim ke unit lain</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-send-fill"></i> {{ $letters->total() }} surat
            </div>
            @if(in_array(Auth::user()->role, ['staf_unit','staf_tu']))
                <a href="{{ route('letters.create') }}" class="btn-custom success" style="width: auto;">
                    <i class="bi bi-plus-lg"></i> Buat Surat Baru
                </a>
            @endif
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
                <th style="width:200px;">No. Surat / Agenda</th>
                <th>Perihal Surat</th>
                <th style="width:220px;">Tujuan & Status</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $letter)
                @php
                    $status = $letter->status;
                    $pillClass = match($status) {
                        'draft'             => 'sp-draft',
                        'pending_agenda'    => 'sp-pending',
                        'in_review_kasubag' => 'sp-review',
                        'in_consideration'  => 'sp-active',
                        'completed'         => 'sp-done',
                        default             => 'sp-default',
                    };
                    $pillText = match($status) {
                        'draft'             => 'Draft',
                        'pending_agenda'    => 'Antre Agenda',
                        'in_review_kasubag' => 'Review Kasubag',
                        'in_consideration'  => 'Disposisi Aktif',
                        'completed'         => 'Selesai',
                        default             => ucfirst($status),
                    };
                    $pillIcon = match($status) {
                        'draft'             => 'bi-pencil',
                        'pending_agenda'    => 'bi-hourglass-split',
                        'in_review_kasubag' => 'bi-eye-fill',
                        'in_consideration'  => 'bi-arrow-repeat',
                        'completed'         => 'bi-check-circle-fill',
                        default             => 'bi-info-circle',
                    };
                @endphp
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
                        <div class="d-flex flex-column gap-1">
                            <div class="s-num" style="font-family:monospace; font-size:0.8rem; color:#475569;">{{ $letter->letter_number !== '-' ? $letter->letter_number : 'Draft — belum bernomor' }}</div>
                            @if($letter->agenda_number)
                                <div><span class="agenda-pill" style="display:inline-flex; align-items:center; gap:0.25rem; padding:0.2rem 0.5rem; border-radius:0.4rem; background:rgba(219,234,254,0.5); color:#1e40af; font-size:0.7rem; font-weight:600;"><i class="bi bi-hash"></i>Agenda: {{ $letter->agenda_number }}</span></div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title">{{ $letter->subject }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2 align-items-start">
                            <div class="recip-cell p-0 m-0 border-0 bg-transparent">
                                @if($letter->recipientUser)
                                    <div class="r-name fw-bold text-dark">{{ $letter->recipientUser->name }}</div>
                                @elseif($letter->recipientUnit)
                                    <div class="r-name fw-bold text-dark">{{ $letter->recipientUnit->name }}</div>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                            </div>
                            <span class="status-pill {{ $pillClass }} m-0">
                                <i class="bi {{ $pillIcon }}"></i> {{ $pillText }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('letters.show', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" class="btn-open">
                            Detail <i class="bi bi-arrow-right-short" style="font-size:1rem;"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-send"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat keluar</p>
                        <span style="font-size:0.8rem;">Surat yang Anda kirim akan muncul di sini.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($letters as $letter)
        @php
            $status = $letter->status;
            $pillClass = match($status) { 'draft'=>'sp-draft','pending_agenda'=>'sp-pending','in_review_kasubag'=>'sp-review','in_consideration'=>'sp-active','completed'=>'sp-done',default=>'sp-default' };
            $pillText  = match($status) { 'draft'=>'Draft','pending_agenda'=>'Antre Agenda','in_review_kasubag'=>'Review','in_consideration'=>'Disposisi Aktif','completed'=>'Selesai',default=>ucfirst($status) };
        @endphp
        <a href="{{ route('letters.show', ['letter'=>\Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}" class="letter-card">
            <div class="lc-no">{{ $letter->letter_number !== '-' ? $letter->letter_number : 'Draft' }}</div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta">
                @if($letter->recipientUser)
                    <span><i class="bi bi-person-fill"></i> {{ $letter->recipientUser->name }}</span>
                @elseif($letter->recipientUnit)
                    <span><i class="bi bi-diagram-3-fill"></i> {{ $letter->recipientUnit->name }}</span>
                @endif
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="mt-2"><span class="status-pill {{ $pillClass }}">{{ $pillText }}</span></div>
        </a>
    @empty
        <div class="empty-state">
            <i class="bi bi-send"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat keluar</p>
        </div>
    @endforelse
</div>

@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">{{ $letters->links() }}</div>
@endif
@endsection