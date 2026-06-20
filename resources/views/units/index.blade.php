@extends('layouts.app')
@section('title', 'Manajemen Unit')

@section('content')


{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">Manajemen Unit</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Kelola unit kerja dan pengelompokan cabangnya</p>
    </div>
    <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.78rem;padding:0.45rem 0.9rem;border-radius:100px;">
        <i class="bi bi-diagram-3-fill me-1"></i>{{ $units->where('name','!=','Administrator')->count() }} unit
    </span>
</div>

{{-- Add Form --}}
<div class="add-panel shadow-sm">
    <div class="ap-title"><i class="bi bi-plus-circle-fill text-primary"></i> Tambah Unit Baru</div>
    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <select name="branch_id" class="form-select" style="max-width:180px;" required>
                <option value="">— Pilih Cabang —</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            <input type="text" name="name" class="form-control" placeholder="Nama unit baru…" style="max-width:240px;" required>
            <div class="d-flex align-items-center gap-2 px-2">
                <input class="form-check-input m-0" type="checkbox" name="is_sekretariat" value="1" id="is_sekretariat">
                <label class="form-check-label" for="is_sekretariat" style="font-size:0.82rem;font-weight:600;color:#475569;cursor:pointer;">Sekretariat</label>
            </div>
            <button class="btn-add" type="submit">
                <i class="bi bi-plus-lg"></i> Tambah Unit
            </button>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;">
    <table class="inbox-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Unit & Cabang</th>
                <th style="width:110px;">Tipe</th>
                <th>Edit</th>
                <th style="width:80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 0; @endphp
            @foreach($units as $unit)
                @if($unit->name === 'Administrator') @continue @endif
                @php $i++; @endphp
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $i }}</td>
                    <td>
                        <div style="font-weight:700;color:#0f172a;">{{ $unit->name }}</div>
                        <div style="font-size:0.75rem;color:#64748b;margin-top:2px;"><i class="bi bi-building"></i> {{ $unit->branch->name ?? '—' }}</div>
                    </td>
                    <td>
                        @if($unit->is_sekretariat)
                            <span class="sek-badge"><i class="bi bi-star-fill"></i> Sekretariat</span>
                        @else
                            <span class="unit-badge"><i class="bi bi-diagram-3"></i> Unit Biasa</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('units.update', $unit) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf @method('PUT')
                            <select name="branch_id" class="edit-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $unit->branch_id==$b->id?'selected':'' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="name" value="{{ $unit->name }}" class="edit-input" required>
                            <button class="btn-save" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                        </form>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <form action="{{ route('units.destroy', $unit) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn-del" onclick="return confirm('Hapus unit ini?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @php $i = 0; @endphp
    @foreach($units as $unit)
        @if($unit->name === 'Administrator') @continue @endif
        @php $i++; @endphp
        <div class="unit-card">
            <div class="uc-name">{{ $unit->name }}</div>
            <div class="uc-branch"><i class="bi bi-building"></i> {{ $unit->branch->name ?? '—' }}</div>
            @if($unit->is_sekretariat)
                <span class="sek-badge"><i class="bi bi-star-fill"></i> Sekretariat</span>
            @else
                <span class="unit-badge"><i class="bi bi-diagram-3"></i> Unit Biasa</span>
            @endif

            <form action="{{ route('units.update', $unit) }}" method="POST" class="mt-2">
                @csrf @method('PUT')
                <div class="d-flex gap-2 mb-2 flex-wrap">
                    <select name="branch_id" class="edit-select flex-grow-1" required>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $unit->branch_id==$b->id?'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="name" value="{{ $unit->name }}" class="edit-input flex-grow-1" required>
                </div>
                <button class="btn-save w-100 justify-content-center"><i class="bi bi-check2"></i> Simpan Perubahan</button>
            </form>

            <div class="uc-actions">
                <form action="{{ route('units.destroy', $unit) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn-del" onclick="return confirm('Hapus unit ini?')">
                        <i class="bi bi-trash3-fill"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    @if($i === 0)
        <div class="empty-state">
            <i class="bi bi-diagram-3"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada unit</p>
        </div>
    @endif
</div>

@endsection
