@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manajemen Pengguna</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
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
@endsection