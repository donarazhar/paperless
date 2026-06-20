@extends('layouts.app')
@section('title', 'Surat Keluar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Daftar Surat Keluar</h1>
        @if(Auth::user()->role === 'staf_unit')
            <a href="{{ route('letters.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Buat Surat Baru
            </a>
        @endif
    </div>

    {{-- Form Filter --}}
    <div class="card p-4 mb-4">
        <form class="row gy-3 gx-3 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Cari nomor atau perihal" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                    <option value="pending_agenda" @selected(request('status') == 'pending_agenda')>Antre Agenda</option>
                    <option value="in_review_kasubag" @selected(request('status') == 'in_review_kasubag')>Direview Kasubag</option>
                    <option value="in_consideration" @selected(request('status') == 'in_consideration')>Disposisi Aktif</option>
                    <option value="completed" @selected(request('status') == 'completed')>Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan Filter</button>
                <a href="{{ request()->url() }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-borderless-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Kirim</th>
                        <th>No Surat Internal</th>
                        <th>Perihal</th>
                        <th>Untuk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                        <tr>
                            <td>{{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}</td>
                            <td>
                                <span class="d-block fw-semibold">{{ $letter->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $letter->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                @if($letter->letter_number !== '-')
                                    <span class="fw-bold">{{ $letter->letter_number }}</span>
                                @else
                                    <em class="text-muted">Draft</em>
                                @endif
                            </td>
                            <td class="fw-medium text-dark">{{ $letter->subject }}</td>
                            <td>
                                @if($letter->recipientUser)
                                    <i class="bi bi-person-fill text-muted me-1"></i> {{ $letter->recipientUser->name }}
                                @elseif($letter->recipientUnit)
                                    <i class="bi bi-diagram-3-fill text-muted me-1"></i> Unit {{ $letter->recipientUnit->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = $letter->status;
                                    $badgeInfo = match($status) {
                                        'draft' => ['bg' => 'bg-secondary', 'text' => 'Draft', 'icon' => 'bi-save'],
                                        'pending_agenda' => ['bg' => 'bg-warning text-dark', 'text' => 'Menunggu Agenda', 'icon' => 'bi-hourglass-split'],
                                        'in_review_kasubag' => ['bg' => 'bg-info', 'text' => 'Review Kasubag', 'icon' => 'bi-search'],
                                        'in_consideration' => ['bg' => 'bg-primary', 'text' => 'Disposisi Aktif', 'icon' => 'bi-arrow-repeat'],
                                        'completed' => ['bg' => 'bg-success', 'text' => 'Selesai', 'icon' => 'bi-check-circle'],
                                        default => ['bg' => 'bg-light text-dark', 'text' => ucfirst($status), 'icon' => 'bi-info-circle'],
                                    };
                                @endphp
                                <span class="badge {{ $badgeInfo['bg'] }} px-2 py-1">
                                    <i class="bi {{ $badgeInfo['icon'] }} me-1"></i> {{ $badgeInfo['text'] }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($letter->id)]) }}"
                                    class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 fw-bold">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada data surat keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->hasPages())
            <div class="mt-4">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
@endsection