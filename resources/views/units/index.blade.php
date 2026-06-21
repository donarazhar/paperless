@extends('layouts.app')
@section('title', 'Manajemen Unit')

@section('content')

{{-- Hero Header --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="hero-title mb-0">Manajemen Unit</div>
            <div class="hero-sub">Kelola unit kerja di bawah setiap cabang YPI Al Azhar</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-diagram-3-fill"></i> {{ $units->total() }} unit
            </div>
        </div>
    </div>
</div>

{{-- Add Form --}}
<div class="filter-card">
    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <label class="f-label"><i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Unit Baru</label>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
            <select name="branch_id" class="form-select" style="max-width:220px;" required>
                <option value="">— Pilih Cabang —</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
            <input type="text" name="name" class="form-control" placeholder="Nama unit baru (misal: SDIA 1 Pusat)..." style="max-width:300px;" required>
            <div class="d-flex align-items-center gap-2 px-1">
                <input class="form-check-input m-0" type="checkbox" name="is_sekretariat" value="1" id="is_sekretariat">
                <label class="form-check-label" for="is_sekretariat" style="font-size:0.82rem;font-weight:600;color:#475569;cursor:pointer;">Sekretariat</label>
            </div>
            <button class="btn-filter" type="submit" style="background:var(--primary);color:#fff;border-color:var(--primary);">
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
            @forelse($units as $unit)
                @if($unit->name === 'Administrator') @continue @endif
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $units->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:9px;background:{{ $unit->is_sekretariat ? '#ede9fe' : '#e0e7ff' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi {{ $unit->is_sekretariat ? 'bi-star-fill' : 'bi-diagram-3-fill' }}" style="color:{{ $unit->is_sekretariat ? '#7c3aed' : '#6366f1' }};font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:0.875rem;color:#0f172a;">{{ $unit->name }}</div>
                                <div style="font-size:0.72rem;color:#94a3b8;"><i class="bi bi-building"></i> {{ $unit->branch->name ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($unit->is_sekretariat)
                            <span class="role-pill rp-kepala" style="font-size:0.72rem;"><i class="bi bi-star-fill"></i> Sekretariat</span>
                        @else
                            <span class="role-pill rp-default" style="font-size:0.72rem;"><i class="bi bi-diagram-3"></i> Unit Biasa</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('units.update', $unit) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf @method('PUT')
                            <select name="branch_id" class="edit-select" style="max-width:180px;" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="name" value="{{ $unit->name }}" class="edit-input" required>
                            <button class="btn-edit" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn-del" onclick="return confirm('Hapus unit ini?')">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-diagram-3"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada unit</p>
                        <span style="font-size:0.8rem;">Tambahkan unit baru menggunakan form di atas.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($units as $unit)
        @if($unit->name === 'Administrator') @continue @endif
        <div class="user-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $unit->is_sekretariat ? '#ede9fe' : '#e0e7ff' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $unit->is_sekretariat ? 'bi-star-fill' : 'bi-diagram-3-fill' }}" style="color:{{ $unit->is_sekretariat ? '#7c3aed' : '#6366f1' }};font-size:1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="uc-name">{{ $unit->name }}</div>
                    <div class="uc-email"><i class="bi bi-building" style="font-size:0.7rem;"></i> {{ $unit->branch->name ?? '—' }}</div>
                </div>
                @if($unit->is_sekretariat)
                    <span class="role-pill rp-kepala" style="font-size:0.7rem;"><i class="bi bi-star-fill"></i> Sek</span>
                @endif
            </div>
            <form action="{{ route('units.update', $unit) }}" method="POST" class="mt-1">
                @csrf @method('PUT')
                <select name="branch_id" class="edit-select w-100 mb-2" required>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" value="{{ $unit->name }}" class="edit-input w-100 mb-2" required>
                <div class="d-flex gap-2">
                    <button class="btn-edit flex-grow-1 justify-content-center" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                    <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn-del" onclick="return confirm('Hapus unit ini?')"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </form>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-diagram-3"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada unit</p>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $units->links() }}
</div>

@endsection
