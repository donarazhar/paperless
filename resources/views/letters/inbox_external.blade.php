@extends('layouts.app')
@section('title', 'Surat Masuk Eksternal')

@section('content')


{{-- Page Header --}}
<div class="inbox-hero" style="background: linear-gradient(135deg, #1e3a8a 0%, #4c1d95 45%, #7e22ce 100%);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="hero-title mb-0">Surat Masuk Eksternal</div>
                <span class="ext-badge" style="background: rgba(255,255,255,0.2); color: #fff;"><i class="bi bi-building"></i> Instansi Luar</span>
            </div>
            <div class="hero-sub">Surat dari instansi luar yang telah diinput &amp; diarsipkan</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-envelope-exclamation-fill"></i> {{ $letters->total() }} surat
            </div>
            <a href="{{ route('letters.createExternal') }}" class="btn-custom success" style="width: auto;">
                <i class="bi bi-plus-lg"></i> Buat Surat Eksternal
            </a>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
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
                <th style="width:180px;">Instansi Pengirim</th>
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
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">
                        {{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}
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
                        <div class="sender-cell">
                            <div class="s-name">{{ $letter->external_sender_name }}</div>
                            <div class="s-input"><i class="bi bi-building me-1"></i>Instansi Luar</div>
                        </div>
                    </td>
                    <td>
                        @if($disp)
                            <div class="status-pill sp-disp mb-1">
                                <i class="bi bi-exclamation-circle-fill"></i> Ada Disposisi
                            </div>
                            <div style="font-size:0.72rem;color:#92400e;font-style:italic;line-height:1.3;">
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
                            <i class="bi bi-envelope-exclamation"></i>
                            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat masuk eksternal</p>
                            <span style="font-size:0.8rem;">Surat dari instansi luar akan muncul di sini setelah diinput.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
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
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="lc-no flex-grow-1">{{ $letter->letter_number ?: 'No. belum ada' }}</div>
                <span class="ext-badge"><i class="bi bi-building"></i> Eksternal</span>
            </div>
            <div class="lc-subject">{{ $letter->subject }}</div>
            <div class="lc-meta">
                <span><i class="bi bi-building-fill" style="color:#7e22ce;"></i> {{ $letter->external_sender_name }}</span>
                <span><i class="bi bi-clock"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
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
            <i class="bi bi-envelope-exclamation"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada surat masuk eksternal</p>
            <span style="font-size:0.8rem;">Surat dari instansi luar akan muncul di sini setelah diinput.</span>
        </div>
    @endforelse
</div>

@if($letters->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $letters->links() }}
    </div>
@endif

@endsection
