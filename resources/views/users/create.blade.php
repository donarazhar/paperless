@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
    <h1>Tambah Pengguna Baru</h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="manager">Manager</option>
                <option value="staff" selected>Staff</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Unit</label>
            <select name="unit_id" class="form-select" required>
                @foreach($units as $unit)
                    @if ($unit->name == 'Administrator')
                        @continue
                    @endif
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection