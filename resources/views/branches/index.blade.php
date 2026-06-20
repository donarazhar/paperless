@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Manajemen Cabang</h1>
</div>

<div class="card p-4">
    <div class="mb-4">
        <form action="{{ route('branches.store') }}" method="POST">
            @csrf
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Nama Cabang Baru" required>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Tambah Cabang</button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Cabang</th>
                    <th>Jumlah Unit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $branch->name }}</td>
                        <td><span class="badge bg-secondary">{{ $branch->units_count }} Unit</span></td>
                        <td>
                            <form action="{{ route('branches.update', $branch) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <div class="input-group input-group-sm w-auto d-inline-flex">
                                    <input type="text" name="name" value="{{ $branch->name }}" class="form-control" required style="max-width: 150px;">
                                    <button class="btn btn-success"><i class="bi bi-check2"></i> Simpan</button>
                                </div>
                            </form>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline ms-1">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus cabang ini?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada cabang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
