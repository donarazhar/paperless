@extends('layouts.app')
@section('title', 'Surat Masuk')

@section('content')
    <h1 class="mb-4">Surat Masuk</h1>

    {{-- Form Filter & Pencarian --}}
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
                <th>Tanggal Dikirim</th>
                <th>No Surat</th>
                <th>Perihal</th>
                <th>Dari</th>
                <th>Untuk</th>
                <th>Via</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
                @php
                    $user = Auth::user();
                    // Cari disposisi yang relevan (ke user atau unit)
                    $disp = $letter->dispositions->first(function ($d) use ($user) {
                        return $d->to_user_id === $user->id
                            || $d->to_unit_id === $user->unit_id;
                    });
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($letters->currentPage() - 1) * $letters->perPage() }}</td>
                    <td>
                        {{ $letter->created_at
                ->locale('id')
                ->isoFormat('dddd, D MMMM YYYY HH:mm') }}
                    </td>
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
                    <td>
                        @if($disp)
                            <span class="badge bg-warning text-dark">Disposisi</span><br>
                            <small class="d-block">{{ \Illuminate\Support\Str::limit($disp->note, 30) }}</small>
                            <small class="d-block text-muted">
                                <i class="bi bi-person-circle"></i>
                                Oleh: {{ $disp->fromUser->name }}
                            </small>
                        @else
                            <span class="badge bg-primary">Langsung</span>
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
                        @if(Auth::user()->role === 'staff' && $letter->status === 'sent')
                            <form action="{{ route('letters.markRead', $letter) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Tandai Dibaca</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $letters->links() }}
@endsection