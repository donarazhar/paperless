@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Manajemen Cabang</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('branches.store') }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Nama Cabang Baru" required>
                <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Tambah Cabang</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
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
