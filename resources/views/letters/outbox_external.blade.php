@extends('layouts.app')
@section('title', 'Surat Keluar Eksternal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Riwayat Surat Keluar Eksternal</h1>
        <a href="{{ route('letters.createOutboundExternal') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Catat Surat Keluar Eksternal
        </a>
    </div>

    {{-- Form Filter --}}
    <div class="card p-4 mb-4">
        <form class="row gy-3 gx-3 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Cari perihal, nomor, atau instansi tujuan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold"><i class="bi bi-search me-1"></i> Terapkan</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('letters.outboundExternal') }}" class="btn btn-outline-secondary w-100 fw-bold">Reset</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 text-uppercase text-muted small" width="20%">No Surat / Tgl</th>
                        <th class="py-3 px-4 text-uppercase text-muted small" width="30%">Tujuan Instansi</th>
                        <th class="py-3 px-4 text-uppercase text-muted small" width="30%">Perihal</th>
                        <th class="py-3 px-4 text-uppercase text-muted small" width="10%">Status</th>
                        <th class="py-3 px-4 text-uppercase text-muted small text-end" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                        <tr>
                            <td class="px-4">
                                <div class="fw-bold text-dark">{{ $letter->letter_number }}</div>
                                <div class="small text-muted">{{ $letter->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $letter->external_recipient_name }}</div>
                                        <div class="small text-muted"><i class="bi bi-person-fill"></i> Dicatat oleh: Anda</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $letter->subject }}">
                                    {{ $letter->subject }}
                                </div>
                            </td>
                            <td class="px-4">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-check-circle-fill me-1"></i> Tercatat
                                </span>
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('letters.show', \Vinkla\Hashids\Facades\Hashids::encode($letter->id)) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    Lihat <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" alt="Empty" width="80" class="mb-3 opacity-50">
                                <p class="text-muted mb-0">Belum ada data surat keluar eksternal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($letters->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
@endsection
