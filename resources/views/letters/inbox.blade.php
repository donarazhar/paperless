@extends('layouts.app')
@section('title', 'Surat Masuk & Disposisi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Antrean Surat Masuk & Disposisi</h1>
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
                    <option value="pending_agenda" @selected(request('status') == 'pending_agenda')>Antre Agenda</option>
                    <option value="in_review_kasubag" @selected(request('status') == 'in_review_kasubag')>Review Kasubag</option>
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
                        <th>Tanggal</th>
                        <th>Informasi Surat</th>
                        <th>Dari</th>
                        <th>Status / Arahan Disposisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                        @php
                            $user = Auth::user();
                            $disp = $letter->dispositions->first(function ($d) use ($user) {
                                return $d->to_user_id === $user->id || $d->to_unit_id === $user->unit_id;
                            });
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}</td>
                            <td>
                                <span class="d-block fw-semibold">{{ $letter->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $letter->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $letter->subject }}</div>
                                <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.7rem;">
                                    {{ $letter->letter_number }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $letter->sender->name }}</div>
                                <small class="text-muted">{{ $letter->sender->unit->name }}</small>
                            </td>
                            <td>
                                @if($disp)
                                    <div class="p-2 bg-warning bg-opacity-10 border border-warning rounded">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>
                                            <strong class="small text-dark">Ada Disposisi</strong>
                                        </div>
                                        <div class="small text-muted fst-italic">"{{ \Illuminate\Support\Str::limit($disp->note, 45) }}"</div>
                                    </div>
                                @else
                                    @php
                                        $status = $letter->status;
                                        $badgeInfo = match($status) {
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
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('letters.show', ['letter' => Hashids::encode($letter->id)]) }}"
                                    class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 fw-bold">
                                    Buka Surat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-check-all fs-1 d-block mb-3 text-success"></i>
                                Hore! Tidak ada tumpukan surat masuk atau disposisi baru.
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