@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')
<style>
    /* Add form panel */
    .add-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.35rem 1.5rem;margin-bottom:1.25rem; }
    .add-panel .ap-title { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#64748b;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem; }
    .add-panel .form-control { height:44px;border-radius:0.65rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.9rem;font-weight:500;color:#0f172a;transition:all .2s; }
    .add-panel .form-control:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,0.08);outline:none; }
    .add-panel .form-control::placeholder { color:#94a3b8;font-weight:400; }

    /* Table */
    .data-table { width:100%;border-collapse:separate;border-spacing:0; }
    .data-table thead th { font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;padding:0.6rem 0.85rem;border-bottom:1px solid #e8edf4;white-space:nowrap;background:#fafbfd; }
    .data-table thead th:first-child { border-radius:0.75rem 0 0 0;padding-left:1.25rem; }
    .data-table thead th:last-child  { border-radius:0 0.75rem 0 0;padding-right:1.25rem; }
    .data-table tbody tr:hover td { background:#f8faff; }
    .data-table tbody td { padding:0.85rem 0.85rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:0.865rem;color:#334155; }
    .data-table tbody td:first-child { padding-left:1.25rem; }
    .data-table tbody td:last-child  { padding-right:1.25rem; }
    .data-table tbody tr:last-child td { border-bottom:none; }

    /* Inline edit input */
    .edit-input { height:38px;border-radius:0.55rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.875rem;padding:0 0.75rem;min-width:160px;transition:all .2s; }
    .edit-input:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.09);outline:none; }

    /* Buttons */
    .btn-add  { display:inline-flex;align-items:center;gap:0.45rem;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border:none;border-radius:0.65rem;font-size:0.875rem;font-weight:700;height:44px;padding:0 1.25rem;cursor:pointer;transition:all .15s;text-decoration:none; }
    .btn-add:hover { opacity:0.88;transform:translateY(-1px);color:#fff; }
    .btn-save { display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#166534;border:none;border-radius:0.5rem;font-size:0.76rem;font-weight:700;padding:0.35rem 0.75rem;cursor:pointer;transition:background .15s; }
    .btn-save:hover { background:#bbf7d0; }
    .btn-del  { display:inline-flex;align-items:center;gap:4px;background:#fef2f2;color:#dc2626;border:none;border-radius:0.5rem;font-size:0.76rem;font-weight:700;padding:0.35rem 0.75rem;cursor:pointer;transition:background .15s; }
    .btn-del:hover { background:#fee2e2; }

    /* Unit count badge */
    .unit-badge { display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#2563eb;font-size:0.72rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:100px; }

    /* Mobile cards */
    .br-card { background:#fff;border:1px solid #e8edf4;border-radius:0.9rem;padding:1rem 1.1rem;margin-bottom:0.65rem; }
    .br-name { font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:0.35rem; }
    .br-meta { font-size:0.78rem;color:#64748b;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem; }

    .empty-state { text-align:center;padding:4rem 1rem;color:#94a3b8; }
    .empty-state i { font-size:3rem;display:block;margin-bottom:0.75rem;color:#cbd5e1; }

    .table-wrap { display:block; }
    .cards-wrap { display:none; }
    @media(max-width:768px) { .table-wrap{display:none;} .cards-wrap{display:block;} }
</style>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0" style="letter-spacing:-0.03em;">Manajemen Cabang</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Kelola data cabang dan unit di bawahnya</p>
    </div>
    <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.78rem;padding:0.45rem 0.9rem;border-radius:100px;">
        <i class="bi bi-building me-1"></i>{{ $branches->count() }} cabang
    </span>
</div>

{{-- Add Form --}}
<div class="add-panel shadow-sm">
    <div class="ap-title"><i class="bi bi-plus-circle-fill text-primary"></i> Tambah Cabang Baru</div>
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <div class="d-flex gap-2 flex-wrap">
            <input type="text" name="name" class="form-control" placeholder="Nama cabang baru…" required style="max-width:320px;">
            <button class="btn-add" type="submit">
                <i class="bi bi-plus-lg"></i> Tambah Cabang
            </button>
        </div>
    </form>
</div>

{{-- DESKTOP TABLE --}}
<div class="table-wrap" style="background:#fff;border:1px solid #e8edf4;border-radius:1rem;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Nama Cabang</th>
                <th style="width:120px;">Jumlah Unit</th>
                <th style="width:280px;">Edit Nama</th>
                <th style="width:80px;">Hapus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($branches as $branch)
                <tr>
                    <td style="color:#94a3b8;font-size:0.78rem;font-weight:600;">{{ $branches->firstItem() + $loop->index }}</td>
                    <td style="font-weight:700;color:#0f172a;">{{ $branch->name }}</td>
                    <td>
                        <span class="unit-badge">
                            <i class="bi bi-diagram-3-fill"></i> {{ $branch->units_count }} Unit
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('branches.update', $branch) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $branch->name }}" class="edit-input" required>
                            <button class="btn-save" type="submit">
                                <i class="bi bi-check2"></i> Simpan
                            </button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('branches.destroy', $branch) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn-del" onclick="return confirm('Hapus cabang ini? Seluruh unit di dalamnya juga akan terhapus.')">
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
                        <span style="font-size:0.8rem;">Tambahkan cabang menggunakan form di atas.</span>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MOBILE CARDS --}}
<div class="cards-wrap">
    @forelse($branches as $branch)
        <div class="br-card">
            <div class="br-name">{{ $branch->name }}</div>
            <div class="br-meta">
                <span class="unit-badge"><i class="bi bi-diagram-3-fill"></i> {{ $branch->units_count }} Unit</span>
            </div>
            <form action="{{ route('branches.update', $branch) }}" method="POST" class="mb-2">
                @csrf @method('PUT')
                <div class="d-flex gap-2">
                    <input type="text" name="name" value="{{ $branch->name }}" class="edit-input flex-grow-1" required>
                    <button class="btn-save" type="submit"><i class="bi bi-check2"></i> Simpan</button>
                </div>
            </form>
            <form action="{{ route('branches.destroy', $branch) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn-del w-100 justify-content-center" onclick="return confirm('Hapus cabang ini?')">
                    <i class="bi bi-trash3-fill"></i> Hapus Cabang
                </button>
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
