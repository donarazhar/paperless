@extends('layouts.app')
@section('title','Unit')
@section('content')
<h1>Manajemen Unit</h1>

<form action="{{ route('units.store') }}" method="POST" class="mb-4">
  @csrf
  <div class="input-group">
    <input type="text" name="name" class="form-control" placeholder="Nama Unit" required>
    <button class="btn btn-primary">Tambah Unit</button>
  </div>
</form>

<table class="table">
  <thead>
    <tr><th>#</th><th>Nama Unit</th><th>Aksi</th></tr>
  </thead>
  <tbody>
    @foreach($units as $unit)
    @if ($unit->name == 'Administrator')
      @continue
    @endif
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $unit->name }}</td>
      <td>
        <form action="{{ route('units.update', $unit) }}" method="POST" class="d-inline">
          @csrf @method('PUT')
          <input type="text" name="name" value="{{ $unit->name }}" class="form-control d-inline w-auto" required>
          <button class="btn btn-sm btn-success">Simpan</button>
        </form>
        <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
