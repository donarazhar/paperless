@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <h1>Edit Pengguna</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Unit</label>
            <select name="unit_id" class="form-select" required>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ $user->unit_id == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection