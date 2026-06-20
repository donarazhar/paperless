@extends('layouts.app')
@section('title','Unit')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Manajemen Unit</h1>
</div>

<div class="card p-4 mb-4">
    <form action="{{ route('units.store') }}" method="POST">
      @csrf
      <div class="row g-2 align-items-center">
        <div class="col-md-3">
            <select name="branch_id" class="form-select" required>
              <option value="">-- Pilih Cabang --</option>
              @foreach($branches as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
              @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Nama Unit" required>
        </div>
        <div class="col-auto">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_sekretariat" value="1" id="is_sekretariat"> 
                <label class="form-check-label text-muted fw-semibold" for="is_sekretariat">Sekretariat</label>
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Tambah Unit</button>
        </div>
      </div>
    </form>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-borderless-custom">
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
    </div>
</div>
@endsection
