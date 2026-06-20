@extends('layouts.app')
@section('title','Unit')
@section('content')
<h1>Manajemen Unit</h1>

<form action="{{ route('units.store') }}" method="POST" class="mb-4">
  @csrf
  <div class="input-group">
    <select name="branch_id" class="form-select" required>
      <option value="">-- Pilih Cabang --</option>
      @foreach($branches as $b)
        <option value="{{ $b->id }}">{{ $b->name }}</option>
      @endforeach
    </select>
    <input type="text" name="name" class="form-control w-50" placeholder="Nama Unit" required>
    <div class="input-group-text">
        <input class="form-check-input mt-0 me-2" type="checkbox" name="is_sekretariat" value="1" aria-label="Checkbox for following text input"> Sekretariat
    </div>
    <button class="btn btn-primary">Tambah Unit</button>
  </div>
</form>

<table class="table">
  <thead>
    <tr><th>#</th><th>Cabang</th><th>Nama Unit</th><th>Tipe</th><th>Aksi</th></tr>
  </thead>
  <tbody>
    @foreach($units as $unit)
    @if ($unit->name == 'Administrator')
      @continue
    @endif
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $unit->branch->name ?? '-' }}</td>
      <td>{{ $unit->name }}</td>
      <td>
        @if($unit->is_sekretariat)
            <span class="badge bg-primary">Sekretariat</span>
        @else
            <span class="badge bg-secondary">Unit Biasa</span>
        @endif
      </td>
      <td>
        <form action="{{ route('units.update', $unit) }}" method="POST" class="d-inline">
          @csrf @method('PUT')
          <select name="branch_id" class="form-select form-select-sm d-inline w-auto" required>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
          <input type="text" name="name" value="{{ $unit->name }}" class="form-control form-control-sm d-inline w-auto" required>
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
