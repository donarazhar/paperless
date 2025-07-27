@extends('layouts.app')
@section('title', 'Surat Keluar')

@section('content')
    <h1 class="mb-4">Surat Keluar</h1>

    @if(Auth::user()->role === 'staff')
        <div class="mb-3">
            <a href="{{ route('letters.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Surat Baru
            </a>
        </div>
    @endif

    {{-- Form Filter --}}
    <form class="row g-2 mb-4" method="GET">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Cari nomor atau perihal"
                value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Status --</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="sent" @selected(request('status') == 'sent')>Sent</option>
                <option value="read" @selected(request('status') == 'read')>Read</option>
                <option value="disposed" @selected(request('status') == 'disposed')>Disposed</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>No Surat</th>
                <th>Perihal</th>
                <th>Untuk</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
                <tr>
                    <td>{{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}</td>
                    <td>
                        {{ $letter->created_at->locale('id')->isoFormat('dddd, D MMM YYYY HH:mm') }}
                    </td>
                    <td>{{ $letter->letter_number }}</td>
                    <td>{{ $letter->subject }}</td>
                    <td>
                        @if($letter->recipientUser)
                            {{ $letter->recipientUser->name }}
                        @elseif($letter->recipientUnit)
                            Unit {{ $letter->recipientUnit->name }}
                        @else
                            —
                        @endif
                    </td>
                    @php
                        $status = $letter->status;
                        $badge = match($status) {
                            'draft'    => 'secondary',
                            'sent'     => 'primary',
                            'read'     => 'success',
                            'disposed' => 'warning',
                            default    => 'light',
                        };
                        $icons = [
                            'draft'    => 'bi-save-fill',
                            'sent'     => 'bi-send-fill',
                            'read'     => 'bi-eye-fill',
                            'disposed' => 'bi-arrow-repeat',
                        ];
                        $icon = $icons[$status] ?? 'bi-info-circle';
                    @endphp
                    <td>
                        <span class="badge bg-{{ $badge }}{{ $badge==='warning' ? ' text-dark' : '' }}">
                            <i class="bi {{ $icon }} me-1"></i> {{ ucfirst($status) }}
                        </span>
                    </td>
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