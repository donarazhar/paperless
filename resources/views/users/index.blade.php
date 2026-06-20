@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Manajemen Pengguna</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus-fill"></i> Tambah User</a>
    </div>

<div class="card p-4 mb-4">
    <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <select name="branch_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Cabang --</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select name="unit_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Unit --</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-borderless-custom">
            <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Cabang</th>
                <th>Unit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->unit->branch->name ?? '-' }}</td>
                    <td>{{ $user->unit->name }}</td>
                    @if ($user->role == 'admin')
                        <td>
                            <i class="text-sm">User ini dikunci</i>
                        </td>
                    @else
                        <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection