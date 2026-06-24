@extends('layouts.mailbox')
@section('title', 'Manajemen Unit')

@section('content')
<style>
    /* Modern Dashboard Styling */
    .page-container { padding: 2rem; max-width: 1400px; margin: 0 auto; width: 100%; }
    
    /* Header Section */
    .hero-card { 
        background: linear-gradient(135deg, #10b981 0%, #047857 100%); 
        border-radius: 1.5rem; 
        padding: 2.5rem; 
        color: white; 
        position: relative; 
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(16,185,129,0.2);
    }
    .hero-card::after {
        content: ''; position: absolute; right: 0; top: 0; width: 50%; height: 100%;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        background-size: 100px; opacity: 0.5; pointer-events: none;
    }
    .hero-title { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em; }
    .hero-sub { font-size: 1rem; color: rgba(255,255,255,0.8); }

    /* Action/Filter Card (Add Unit Form) */
    .filter-card {
        background: #ffffff; border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);
    }
    .add-input, .add-select {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; 
        padding: 0.75rem 1rem; width: 100%; font-weight: 500; transition: all 0.2s;
    }
    .add-input:focus, .add-select:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); outline: none; background: #ffffff; }
    .btn-add { background: #0f172a; color: #fff; border: none; border-radius: 0.75rem; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 100%; }
    .btn-add:hover { background: #1e293b; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* Custom Checkbox */
    .modern-checkbox-wrap { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.5rem; transition: background 0.2s; }
    .modern-checkbox-wrap:hover { background: #f1f5f9; }
    .modern-checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: #10b981; cursor: pointer; }
    .modern-checkbox-wrap span { font-weight: 600; font-size: 0.9rem; color: #475569; user-select: none; }

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
    .unit-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .ua-sekretariat { background: #fce7f3; color: #be185d; }
    .ua-unit { background: #dcfce7; color: #15803d; }
    
    .unit-name { font-weight: 700; font-size: 0.95rem; color: #0f172a; margin-bottom: 0.15rem; }
    .unit-branch { font-size: 0.8rem; color: #64748b; }

    /* Inline Edit Form */
    .edit-input, .edit-select { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; width: 100%; }
    .edit-input:focus, .edit-select:focus { border-color: #10b981; outline: none; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; border: none; transition: all 0.2s; text-decoration: none; font-weight: 600; font-size: 0.85rem; padding: 0.5rem 1rem; gap: 0.4rem; }
    .btn-save { background: #e0f2fe; color: #0284c7; }
    .btn-save:hover { background: #0284c7; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(2,132,199,0.2); }
    .btn-del { background: #fef2f2; color: #dc2626; width: 36px; height: 36px; padding: 0; border-radius: 10px; }
    .btn-del:hover { background: #dc2626; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(220,38,38,0.2); }
    
    /* Role Pills */
    .role-pill { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.8rem; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .rp-sek { background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8; }
    .rp-biasa { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

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
                    <h1 class="hero-title">Manajemen Unit</h1>
                    <p class="hero-sub mb-0">Kelola unit kerja dan struktur cabang YPI Al Azhar</p>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 0.5rem 1.25rem; border-radius: 100px; font-weight: 600; font-size: 1rem; backdrop-filter: blur(4px);">
                    <i class="bi bi-diagram-3-fill me-2"></i> {{ $units->total() }} Unit Terdaftar
                </div>
            </div>
        </div>

        {{-- Add Unit Form Card --}}
        <div class="filter-card">
            <h5 class="fw-bold mb-3" style="color: #0f172a; font-size: 1.1rem;"><i class="bi bi-plus-circle-fill text-emerald-500 me-2" style="color:#10b981;"></i>Tambah Unit Baru</h5>
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <select name="branch_id" class="add-select" required>
                            <option value="">— Pilih Cabang —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="name" class="add-input" placeholder="Nama unit (misal: SDIA 1 Pusat)..." required>
                    </div>
                    <div class="col-md-2">
                        <label class="modern-checkbox-wrap">
                            <input type="checkbox" name="is_sekretariat" value="1" id="is_sekretariat">
                            <span>Sekretariat Pusat</span>
                        </label>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-add w-100"><i class="bi bi-plus-lg"></i> Simpan Unit</button>
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
                        <th>Profil Unit</th>
                        <th style="width: 140px;">Tipe Unit</th>
                        <th>Edit Data Unit</th>
                        <th style="text-align: right; width: 80px; padding-right: 2rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        @if($unit->name === 'Administrator') @continue @endif
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: 0.85rem;">
                                {{ $units->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="unit-avatar {{ $unit->is_sekretariat ? 'ua-sekretariat' : 'ua-unit' }}">
                                        <i class="bi {{ $unit->is_sekretariat ? 'bi-star-fill' : 'bi-diagram-3-fill' }}"></i>
                                    </div>
                                    <div>
                                        <div class="unit-name">{{ $unit->name }}</div>
                                        <div class="unit-branch"><i class="bi bi-building me-1"></i> {{ $unit->branch->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($unit->is_sekretariat)
                                    <span class="role-pill rp-sek"><i class="bi bi-star-fill"></i> Sekretariat</span>
                                @else
                                    <span class="role-pill rp-biasa"><i class="bi bi-diagram-3"></i> Unit Biasa</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('units.update', $unit) }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf @method('PUT')
                                    <div style="flex: 1; min-width: 150px; max-width: 200px;">
                                        <select name="branch_id" class="edit-select" required>
                                            @foreach($branches as $b)
                                                <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="flex: 2; min-width: 200px;">
                                        <input type="text" name="name" value="{{ $unit->name }}" class="edit-input" required>
                                    </div>
                                    <button class="action-btn btn-save" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
                                </form>
                            </td>
                            <td style="text-align: right; padding-right: 2rem;">
                                <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="action-btn btn-del" onclick="return confirm('Yakin ingin menghapus unit ini secara permanen?')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-diagram-3"></i>
                                    <div class="empty-title">Tidak Ada Unit</div>
                                    <div class="empty-desc">Tambahkan unit kerja baru melalui form di atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="mobile-cards">
            @forelse($units as $unit)
                @if($unit->name === 'Administrator') @continue @endif
                <div class="m-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="unit-avatar {{ $unit->is_sekretariat ? 'ua-sekretariat' : 'ua-unit' }}">
                            <i class="bi {{ $unit->is_sekretariat ? 'bi-star-fill' : 'bi-diagram-3-fill' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="unit-name">{{ $unit->name }}</div>
                            <div class="unit-branch"><i class="bi bi-building me-1"></i> {{ $unit->branch->name ?? '—' }}</div>
                        </div>
                        @if($unit->is_sekretariat)
                            <span class="role-pill rp-sek"><i class="bi bi-star-fill"></i></span>
                        @endif
                    </div>
                    
                    <form action="{{ route('units.update', $unit) }}" method="POST" class="pt-3 border-top mt-2">
                        @csrf @method('PUT')
                        <div class="mb-2">
                            <label class="text-muted" style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block;">Ubah Cabang</label>
                            <select name="branch_id" class="edit-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted" style="font-size:0.75rem; font-weight:600; margin-bottom:4px; display:block;">Ubah Nama Unit</label>
                            <input type="text" name="name" value="{{ $unit->name }}" class="edit-input" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="action-btn btn-save flex-grow-1" type="submit" style="height:42px;"><i class="bi bi-check2-circle"></i> Simpan</button>
                    </form>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" style="margin: 0;">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-del" style="height:42px; width:42px; border-radius: 0.5rem;" onclick="return confirm('Hapus unit ini?')"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-diagram-3"></i>
                    <div class="empty-title">Tidak Ada Unit</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $units->links() }}
        </div>

    </div>
</div>
@endsection
