@extends('layouts.app')
@section('title', 'Manajemen Organ')

@section('content')

{{-- Hero Header --}}
<div class="inbox-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="hero-title mb-0">Manajemen Organ</div>
            <div class="hero-sub">Kelola organ / jabatan di dalam setiap unit kerja</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-layers-fill"></i> {{ $organs->total() }} organ
            </div>
        </div>
    </div>
</div>

{{-- Add Form --}}
<div class="filter-card">
    <form action="{{ route('organs.store') }}" method="POST">
        @csrf
        <label class="f-label"><i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Organ Baru</label>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
            <select name="unit_id" class="form-select" style="max-width:260px;" required>
                <option value="">— Pilih Unit —</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->branch->name ?? '-' }})</option>
                @endforeach
            </select>
            <input type="text" name="name" class="form-control" placeholder="Nama organ / jabatan (misal: Subag Keuangan)..." style="max-width:300px;" required>
            <button class="btn-filter" type="submit" style="background:var(--primary);color:#fff;border-color:var(--primary);">
                <i class="bi bi-plus-lg"></i> Tambah Organ
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
                <th>Organ / Jabatan</th>
                <th style="width:220px;">Unit</th>
                <th>Edit</th>
                <th style="width:80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($organs as $organ)
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $organs->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:9px;background:#fce7f3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-layers-fill" style="color:#db2777;font-size:0.85rem;"></i>
                            </div>
                            <div style="font-weight:700;font-size:0.875rem;color:#0f172a;">{{ $organ->name }}</div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.845rem;color:#374151;font-weight:600;">{{ $organ->unit->name ?? '—' }}</div>
                        <div style="font-size:0.72rem;color:#94a3b8;"><i class="bi bi-building"></i> {{ $organ->unit->branch->name ?? '—' }}</div>
                    </td>
                    <td>
                        <form action="{{ route('organs.update', $organ) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf @method('PUT')
                            <select name="unit_id" class="edit-select" style="max-width:190px;" required>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ $organ->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="name" value="{{ $organ->name }}" class="edit-input" required>
                            <button class="btn-edit" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('organs.destroy', $organ) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn-del" onclick="return confirm('Hapus organ ini? Pastikan tidak ada pengguna yang terkait.')">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-layers"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada organ</p>
                        <span style="font-size:0.8rem;">Tambahkan organ baru menggunakan form di atas.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($organs as $organ)
        <div class="user-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:38px;height:38px;border-radius:10px;background:#fce7f3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-layers-fill" style="color:#db2777;font-size:1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="uc-name">{{ $organ->name }}</div>
                    <div class="uc-email"><i class="bi bi-diagram-3-fill" style="font-size:0.7rem;"></i> {{ $organ->unit->name ?? '—' }}</div>
                </div>
            </div>
            <form action="{{ route('organs.update', $organ) }}" method="POST" class="mt-1">
                @csrf @method('PUT')
                <select name="unit_id" class="edit-select w-100 mb-2" required>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ $organ->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" value="{{ $organ->name }}" class="edit-input w-100 mb-2" required>
                <div class="d-flex gap-2">
                    <button class="btn-edit flex-grow-1 justify-content-center" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                    <form action="{{ route('organs.destroy', $organ) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn-del" onclick="return confirm('Hapus organ ini?')"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </form>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-layers"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada organ</p>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $organs->links() }}
</div>

@endsection
