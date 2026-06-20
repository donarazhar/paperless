@extends('layouts.app')
@section('title', 'Surat Masuk Internal')

@section('content')


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

{{-- ── DESKTOP TABLE ── --}}
<div class="table-wrap table-container">
    <table class="inbox-table">
        <thead>
            <tr>
                <th style="width:50px;">#</th>
                <th style="width:110px;">Tanggal</th>
                <th>Perihal Surat</th>
                <th style="width:280px;">Pengirim & Status</th>
                <th style="width:110px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $letter)
                @php
                    $authUser = Auth::user();
                    $disp = $letter->dispositions->sortByDesc('created_at')->first(fn($d) =>
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
                            <div class="d-date">{{ $letter->created_at->format('d/m/Y') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="subject-cell">
                            <div class="s-title">{{ $letter->subject }}</div>
                            <div class="s-num" style="margin-bottom:0.25rem;">{{ $letter->letter_number ?: '— Belum ada nomor' }}</div>
                            @if($letter->agenda_number)
                                <div><span class="agenda-pill" style="display:inline-flex; align-items:center; gap:0.25rem; padding:0.2rem 0.5rem; border-radius:0.4rem; background:rgba(219,234,254,0.5); color:#1e40af; font-size:0.7rem; font-weight:600;"><i class="bi bi-hash"></i>Agenda: {{ $letter->agenda_number }}</span></div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2 align-items-start">
                            <div class="s-name fw-bold text-dark">{{ $letter->sender->unit->name ?? '—' }}</div>
                            <div>
                                @if($disp && $letter->status !== 'in_review_kasubag')
                                    <span class="status-pill sp-disp m-0" style="display:inline-flex;margin-bottom:0.25rem !important;">
                                        <i class="bi bi-exclamation-circle-fill"></i> Disposisi dr. {{ $disp->fromUser->unit->name ?? $disp->fromUser->name }}
                                    </span>
                                    <div class="disp-note mt-1" style="font-size:0.75rem;">"{{ \Illuminate\Support\Str::limit($disp->note, 50) }}"</div>
                                @else
                                    <span class="status-pill {{ $pillClass }} m-0">
                                        <i class="bi {{ $pillIcon }}"></i> {{ $pillText }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ $showUrl }}" class="btn-open">
                            <i class="bi bi-eye-fill"></i> Buka
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
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
                <span><i class="bi bi-calendar"></i> {{ $letter->created_at->format('d/m/Y') }}</span>
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