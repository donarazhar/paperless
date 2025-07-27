@extends('layouts.app')
@section('title', 'Semua Surat')

@section('content')
    <h1 class="mb-4">Semua Surat</h1>

    {{-- Form Pencarian & Filter --}}
    <form class="row gy-2 gx-3 align-items-end mb-4" method="GET">
        <div class="col-auto">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}">
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
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-hover align-middle">
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
        </tbody>
    </table>

    {{ $letters->links() }}
@endsection