@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')
    <h1 class="mb-4">Edit Profil</h1>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <button class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('profile.password') }}" class="btn btn-link">Ganti Password</a>
    </form>
@endsection