@extends('layouts.app')
@section('title', 'Tambah Unit')
@section('content')
    <h1>Tambah Unit Baru</h1>

    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Unit</label>
            <input type="text" name="name" class="form-control" placeholder="Masukkan nama unit" required>
        </div>
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection