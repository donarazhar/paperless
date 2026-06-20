@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Tambah Pengguna Baru</h1>
    <a href="{{ route('users.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

{{-- Alert Error Validasi --}}
@if($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 0.75rem;">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <strong class="mb-0">Terdapat kesalahan pada input Anda:</strong>
        </div>
        <ul class="mb-0 text-sm">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card p-4 col-lg-8">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Nama Lengkap</label>
            <input type="text" name="name" class="form-control bg-light" value="{{ old('name') }}" required>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Alamat Email</label>
            <input type="email" name="email" class="form-control bg-light" value="{{ old('email') }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase">Kata Sandi</label>
                <input type="password" name="password" class="form-control bg-light" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" class="form-control bg-light" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Role / Hak Akses</label>
            <select name="role" class="form-select bg-light" required>
                <option value="staf_unit" selected>Staf Unit (Pembuat Surat)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Penempatan Unit</label>
            <select name="unit_id" class="form-select bg-light" required>
                <option value="">— Pilih Unit —</option>
                @foreach($units as $unit)
                    @if ($unit->name == 'Administrator')
                        @continue
                    @endif
                    <option value="{{ $unit->id }}">{{ $unit->name }} (Cab. {{ $unit->branch->name ?? '-' }})</option>
                @endforeach
            </select>
            @if($units->count() <= 1)
                <div class="form-text text-danger mt-2"><i class="bi bi-info-circle"></i> Semua unit telah terisi oleh pengguna. Buat unit baru terlebih dahulu.</div>
            @endif
        </div>

        <div class="d-flex justify-content-end pt-3 border-top">
            <button class="btn btn-primary px-4 fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Buat Akun
            </button>
        </div>
    </form>
</div>
@endsection