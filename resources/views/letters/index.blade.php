@extends('layouts.app')
@section('title', 'Semua Surat')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Daftar Surat</h1>
    </div>

    {{-- Form Pencarian & Filter --}}
    <div class="card p-4 mb-4">
        <form class="row gy-3 gx-3 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Cari (Perihal/No.Surat)</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ketik kata kunci...">
            </div>
        <div class="col-auto">
            <label class="form-label">Jenis</label>
            <select name="type" class="form-select">
                <option value="">Semua</option>
                <option value="inbound" @selected(request('type') == 'inbound')>Inbound</option>
                <option value="outbound" @selected(request('type') == 'outbound')>Outbound</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="sent" @selected(request('status') == 'sent')>Sent</option>
                <option value="read" @selected(request('status') == 'read')>Read</option>
                <option value="disposed" @selected(request('status') == 'disposed')>Disposed</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-auto">
            <label class="form-label">Sampai</label>
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
        <table class="table table-borderless-custom align-middle">
            <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Type</th>
                <th>No Surat</th>
                <th>Perihal</th>
                <th>Dari</th>
                <th>Tujuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
                <tr>
                    <td>{{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}</td>
                    <td>{{ $letter->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                    <td class="text-capitalize">{{ $letter->type }}</td>
                    <td>{{ $letter->letter_number }}</td>
                    <td>{{ $letter->subject }}</td>
                    <td>{{ $letter->sender->name }}</td>
                    <td>
                        @if($letter->recipientUser)
                            {{ $letter->recipientUser->name }}
                        @elseif($letter->recipientUnit)
                            Unit {{ $letter->recipientUnit->name }}
                        @else
                            --
                        @endif
                    </td>
                    <td class="text-capitalize">{{ $letter->status }}</td>
                    <td>
                        <a href="{{ route('letters.show', ['letter' => Hashids::encode($letter->id)]) }}"
                            class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="mt-4">
        {{ $letters->links() }}
    </div>
</div>
@endsection