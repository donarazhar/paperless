@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')

{{-- Hero Header --}}
<div class="inbox-hero" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 45%, #b45309 100%);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="hero-title mb-0">Manajemen Cabang</div>
            <div class="hero-sub">Kelola cabang-cabang yang dimiliki YPI Al Azhar</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="stat-chip">
                <i class="bi bi-building-fill"></i> {{ $branches->total() }} cabang
            </div>
        </div>
    </div>
</div>

{{-- Add Form --}}
<div class="filter-card">
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <label class="f-label"><i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Cabang Baru</label>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
            <input type="text" name="name" class="form-control" placeholder="Nama cabang baru (misal: Cabang Kebayoran Baru)..." style="max-width:380px;" required>
            <button class="btn-filter" type="submit" style="background:var(--primary);color:#fff;border-color:var(--primary);">
                <i class="bi bi-plus-lg"></i> Tambah Cabang
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
                <th>Nama Cabang</th>
                <th style="width:100px;">Jml Unit</th>
                <th>Edit</th>
                <th style="width:80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($branches as $branch)
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $branches->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:9px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-building-fill" style="color:#d97706;font-size:0.9rem;"></i>
                            </div>
                            <div style="font-weight:700;font-size:0.875rem;color:#0f172a;">{{ $branch->name }}</div>
                        </div>
                    </td>
                    <td>
                        <span class="role-pill rp-stafunit" style="font-size:0.72rem;">
                            <i class="bi bi-diagram-3"></i> {{ $branch->units_count }} unit
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('branches.update', $branch) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $branch->name }}" class="edit-input" style="max-width:340px;" required>
                            <button class="btn-edit" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn-del" onclick="return confirm('Hapus cabang ini? Pastikan tidak ada unit yang terkait.')">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-building"></i>
                        <p class="fw-semibold mb-1" style="color:#475569;">Belum ada cabang</p>
                        <span style="font-size:0.8rem;">Tambahkan cabang baru menggunakan form di atas.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($branches as $branch)
        <div class="user-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:38px;height:38px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-building-fill" style="color:#d97706;font-size:1rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="uc-name">{{ $branch->name }}</div>
                    <div class="uc-email">{{ $branch->units_count }} unit terdaftar</div>
                </div>
            </div>
            <form action="{{ route('branches.update', $branch) }}" method="POST" class="mt-2">
                @csrf @method('PUT')
                <input type="text" name="name" value="{{ $branch->name }}" class="edit-input w-100 mb-2" required>
                <div class="d-flex gap-2">
                    <button class="btn-edit flex-grow-1 justify-content-center" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                    <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn-del" onclick="return confirm('Hapus cabang ini?')"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </form>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-building"></i>
            <p class="fw-semibold mb-1" style="color:#475569;">Belum ada cabang</p>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $branches->links() }}
</div>

@endsection
