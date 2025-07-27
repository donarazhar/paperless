@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="mb-4">Daftar Akun Baru</h2>
            <form method="POST" action="{{ route('register') }}">@csrf
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Register</button>
                <p class="mt-3 text-center">
                    Sudah punya akun? <a href="{{ route('login') }}">Login</a>
                </p>
            </form>
        </div>
    </div>
@endsection