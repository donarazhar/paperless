@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
    <h1 class="mb-4">Ganti Password</h1>

    <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Password Saat Ini</label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button class="btn btn-success">Ubah Password</button>
        <a href="{{ route('profile.edit') }}" class="btn btn-link">Batal</a>
    </form>
@endsection