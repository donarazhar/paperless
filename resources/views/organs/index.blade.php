@extends('layouts.mailbox')
@section('title', 'Manajemen Organ')

@section('content')
<style>
    /* Modern Dashboard Styling */
    .page-container { padding: 2rem; max-width: 1400px; margin: 0 auto; width: 100%; }
    
    /* Header Section */
    .hero-card { 
        background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); 
        border-radius: 1.5rem; 
        padding: 2.5rem; 
        color: white; 
        position: relative; 
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(236,72,153,0.2);
    }
    .hero-card::after {
        content: ''; position: absolute; right: 0; top: 0; width: 50%; height: 100%;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        background-size: 100px; opacity: 0.5; pointer-events: none;
    }
    .hero-title { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em; }
    .hero-sub { font-size: 1rem; color: rgba(255,255,255,0.8); }

    /* Action/Filter Card (Add Organ Form) */
    .filter-card {
        background: #ffffff; border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);
    }
    .add-input, .add-select {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; 
        padding: 0.75rem 1rem; width: 100%; font-weight: 500; transition: all 0.2s;
    }
    .add-input:focus, .add-select:focus { border-color: #ec4899; box-shadow: 0 0 0 4px rgba(236,72,153,0.1); outline: none; background: #ffffff; }
    .btn-add { background: #0f172a; color: #fff; border: none; border-radius: 0.75rem; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 100%; }
    .btn-add:hover { background: #1e293b; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* Table Styling */
    .table-container {
        background: #ffffff; border-radius: 1.5rem; overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th {
        background: #f8fafc; padding: 1.25rem 1.5rem; font-weight: 700; color: #64748b; 
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;
    }
    .modern-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .modern-table tbody tr { transition: all 0.2s; }
    .modern-table tbody tr:hover { background: #f8fafc; }
    .modern-table tbody tr:last-child td { border-bottom: none; }

    /* Avatar & Info */
    .organ-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; background: #fce7f3; color: #db2777; }
    .organ-name { font-weight: 700; font-size: 0.95rem; color: #0f172a; margin-bottom: 0.15rem; }

    /* Inline Edit Form */
    .edit-input, .edit-select { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; width: 100%; }
    .edit-input:focus, .edit-select:focus { border-color: #ec4899; outline: none; box-shadow: 0 0 0 3px rgba(236,72,153,0.1); }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; border: none; transition: all 0.2s; text-decoration: none; font-weight: 600; font-size: 0.85rem; padding: 0.5rem 1rem; gap: 0.4rem; }
    .btn-save { background: #e0f2fe; color: #0284c7; }
    .btn-save:hover { background: #0284c7; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(2,132,199,0.2); }
    .btn-del { background: #fef2f2; color: #dc2626; width: 36px; height: 36px; padding: 0; border-radius: 10px; }
    .btn-del:hover { background: #dc2626; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(220,38,38,0.2); }
    
    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
    .empty-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
    .empty-desc { color: #64748b; font-size: 0.95rem; }

    /* Mobile Cards */
    .mobile-cards { display: none; }
    .m-card { background: #fff; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.04); }

    @media (max-width: 992px) {
        .table-container { display: none; }
        .mobile-cards { display: block; }
    }
</style>

<div class="mail-scroll">
    <div class="page-container">
        
        {{-- Hero Header --}}
        <div class="hero-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4" style="position:relative; z-index:2;">
                <div>
                    <h1 class="hero-title">Manajemen Organ</h1>
                    <p class="hero-sub mb-0">Kelola organ (jabatan / divisi) di dalam setiap unit kerja</p>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 0.5rem 1.25rem; border-radius: 100px; font-weight: 600; font-size: 1rem; backdrop-filter: blur(4px);">
                    <i class="bi bi-layers-fill me-2"></i> {{ $organs->total() }} Organ Terdaftar
                </div>
            </div>
        </div>

        {{-- Add Organ Form Card --}}
        <div class="filter-card">
            <h5 class="fw-bold mb-3" style="color: #0f172a; font-size: 1.1rem;"><i class="bi bi-plus-circle-fill text-pink-500 me-2" style="color:#ec4899;"></i>Tambah Organ Baru</h5>
            <form action="{{ route('organs.store') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <select name="unit_id" class="add-select" required>
                            <option value="">— Pilih Unit Induk —</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->branch->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="name" class="add-input" placeholder="Ketik nama organ (misal: Subag Keuangan)..." required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-add w-100"><i class="bi bi-plus-lg"></i> Simpan Organ</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Profil Organ</th>
                        <th style="width: 250px;">Unit Induk</th>
                        <th>Edit Data Organ</th>
                        <th style="text-align: right; width: 80px; padding-right: 2rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organs as $organ)
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: 0.85rem;">
                                {{ $organs->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="organ-avatar">
                                        <i class="bi bi-layers-fill"></i>
                                    </div>
                                    <div class="organ-name">{{ $organ->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #334155; font-size: 0.9rem;">{{ $organ->unit->name ?? '—' }}</div>
                                <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.2rem;"><i class="bi bi-building me-1"></i> {{ $organ->unit->branch->name ?? '—' }}</div>
                            </td>
                            <td>
                                <form action="{{ route('organs.update', $organ) }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf @method('PUT')
                                    <div style="flex: 1; min-width: 150px; max-width: 200px;">
                                        <select name="unit_id" class="edit-select" required>
                                            @foreach($units as $u)
                                                <option value="{{ $u->id }}" {{ $organ->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="flex: 2; min-width: 200px;">
                                        <input type="text" name="name" value="{{ $organ->name }}" class="edit-input" required>
                                    </div>
                                    <button class="action-btn btn-save" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
                                </form>
                            </td>
                            <td style="text-align: right; padding-right: 2rem;">
                                <form action="{{ route('organs.destroy', $organ) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="action-btn btn-del" onclick="return confirm('Yakin ingin menghapus organ ini? Pastikan tidak ada pengguna yang terkait.')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-layers"></i>
                                    <div class="empty-title">Tidak Ada Organ</div>
                                    <div class="empty-desc">Tambahkan organ baru melalui form di atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="mobile-cards">
            @forelse($organs as $organ)
                <div class="m-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="organ-avatar">
                            <i class="bi bi-layers-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="organ-name">{{ $organ->name }}</div>
                            <div style="font-size: 0.8rem; color: #475569; margin-top: 0.2rem;"><i class="bi bi-diagram-3-fill me-1"></i> {{ $organ->unit->name ?? '—' }}</div>
                        </div>
                    </div>
                    
                    <form action="{{ route('organs.update', $organ) }}" method="POST" class="pt-3 border-top mt-2">
                        @csrf @method('PUT')
                        <div class="mb-2">
                            <label class="text-muted" style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block;">Ubah Unit</label>
                            <select name="unit_id" class="edit-select" required>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ $organ->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted" style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block;">Ubah Nama Organ</label>
                            <input type="text" name="name" value="{{ $organ->name }}" class="edit-input" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="action-btn btn-save flex-grow-1" type="submit" style="height:42px;"><i class="bi bi-check2-circle"></i> Simpan</button>
                    </form>
                            <form action="{{ route('organs.destroy', $organ) }}" method="POST" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-del" style="height:42px; width:42px; border-radius: 0.5rem;" onclick="return confirm('Hapus organ ini?')"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-layers"></i>
                    <div class="empty-title">Tidak Ada Organ</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $organs->links() }}
        </div>

    </div>
</div>
@endsection
