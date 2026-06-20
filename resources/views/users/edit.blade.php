@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Edit Pengguna: {{ $user->name }}</h1>
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
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Nama Lengkap</label>
            <input type="text" name="name" class="form-control bg-light" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Alamat Email</label>
            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control bg-light" placeholder="Kosongkan jika tidak ingin diubah">
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-control bg-light">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Role / Hak Akses</label>
            <select name="role" class="form-select bg-light" required>
                <option value="staf_unit" {{ $user->role == 'staf_unit' || $user->role == 'staff' ? 'selected' : '' }}>Staf Unit (Pembuat Surat)</option>
                <option value="staf_tu" {{ $user->role == 'staf_tu' ? 'selected' : '' }}>Staf TU Sekretariat</option>
                <option value="kasubag_tu" {{ $user->role == 'kasubag_tu' ? 'selected' : '' }}>Kasubag TU Sekretariat</option>
                <option value="kepala_sekretariat" {{ $user->role == 'kepala_sekretariat' ? 'selected' : '' }}>Kepala Sekretariat</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small text-uppercase">Penempatan Unit</label>
            <select name="unit_id" class="form-select bg-light" required>
                @foreach($units as $unit)
                    @if ($unit->name == 'Administrator' && $user->unit_id != $unit->id)
                        @continue
                    @endif
                    <option value="{{ $unit->id }}" {{ $user->unit_id == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }} (Cab. {{ $unit->branch->name ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-end pt-3 border-top">
            <button class="btn btn-primary px-4 fw-bold">
                <i class="bi bi-save-fill me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection