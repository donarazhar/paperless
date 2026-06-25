@extends('layouts.mailbox')
@section('title', 'Manajemen Unit')

@section('content')
<style>
    /* ══ GMAIL-STYLE LAYOUT ══ */
    .compose-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #f6f8fc;
        overflow: hidden;
    }

    /* Top bar */
    .compose-topbar {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .compose-topbar h1 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        flex: 1;
    }
    .btn-back-compose {
        display: inline-flex; align-items: center; gap: .4rem;
        background: none; border: 1.5px solid #e2e8f0; color: #475569;
        border-radius: 100px; padding: .4rem 1rem; font-size: .82rem;
        font-weight: 600; text-decoration: none; transition: all .2s;
        cursor: pointer;
    }
    .btn-back-compose:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

    /* Scrollable body */
    .compose-body {
        flex: 1;
        overflow-y: auto;
        display: flex;
        justify-content: center;
        padding: 1.5rem 1rem 2rem;
    }

    /* Card */
    .compose-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(15,23,42,.08), 0 1px 4px rgba(15,23,42,.04);
        width: 100%;
        max-width: 1000px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        height: fit-content;
    }

    /* Add Form Bar */
    .add-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        align-items: center;
    }
    .add-select {
        border: none;
        outline: none;
        font-size: .95rem;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        color: #0f172a;
        background: transparent;
        padding: .5rem 0;
        cursor: pointer;
        border-bottom: 1.5px solid transparent;
        transition: border-color 0.2s;
    }
    .add-select:focus { border-bottom-color: #10b981; }
    
    .add-input {
        flex: 1;
        min-width: 200px;
        border: none;
        outline: none;
        font-size: .95rem;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        color: #0f172a;
        background: transparent;
        padding: .5rem 0;
    }
    .add-input::placeholder { color: #cbd5e1; font-weight: 400; }
    
    .btn-add {
        display: inline-flex; align-items: center; gap: .45rem;
        background: #10b981; color: #fff;
        border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(16,185,129,.25);
        white-space: nowrap;
    }
    .btn-add:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(16,185,129,.35); }

    /* Custom Checkbox */
    .modern-checkbox-wrap { 
        display: flex; align-items: center; gap: 0.5rem; cursor: pointer; 
        padding: 0.25rem 0.5rem; border-radius: 0.5rem; transition: background 0.2s; 
    }
    .modern-checkbox-wrap:hover { background: #f1f5f9; }
    .modern-checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: #10b981; cursor: pointer; }
    .modern-checkbox-wrap span { font-weight: 600; font-size: 0.9rem; color: #475569; user-select: none; }

    /* Table Styling */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th {
        background: #fff; padding: 1rem 1.25rem; font-weight: 700; color: #94a3b8; 
        font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e2e8f0;
    }
    .modern-table td { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .modern-table tbody tr:hover { background: #f8fafc; }
    .modern-table tbody tr:last-child td { border-bottom: none; }

    /* Avatar & Info */
    .unit-avatar { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .ua-sekretariat { background: #fce7f3; color: #be185d; }
    .ua-unit { background: #dcfce7; color: #15803d; }
    
    .unit-name { font-weight: 600; font-size: .9rem; color: #0f172a; margin-bottom: 0.15rem; }
    .unit-branch { font-size: .75rem; color: #64748b; font-weight: 500; }

    /* Inline Edit Form */
    .edit-input, .edit-select { background: transparent; border: none; border-bottom: 1.5px solid #e2e8f0; border-radius: 0; padding: .25rem 0; font-size: .9rem; font-weight: 500; transition: all .2s; width: 100%; outline: none; }
    .edit-input:focus, .edit-select:focus { border-color: #10b981; }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 100px; border: none; transition: all .2s; text-decoration: none; font-weight: 600; font-size: .8rem; padding: .4rem .8rem; gap: .3rem; cursor: pointer; }
    .btn-save { background: #ecfdf5; color: #10b981; border: 1.5px solid #a7f3d0; }
    .btn-save:hover { background: #10b981; color: #fff; border-color: #10b981; }
    .btn-del { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; width: 32px; height: 32px; padding: 0; border-radius: 50%; }
    .btn-del:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
    
    /* Role Pills */
    .role-pill { display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .75rem; border-radius: 100px; font-size: .75rem; font-weight: 600; }
    .rp-sek { background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8; }
    .rp-biasa { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
    .empty-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
    .empty-desc { color: #94a3b8; font-size: .9rem; }

    /* Pagination container */
    .pagination-wrapper { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; background: #fff; }
    .pagination-wrapper nav { margin-bottom: 0; }
    .pagination-wrapper p { margin-bottom: 0; }

    /* Error alert */
    .err-alert {
        margin: 1rem 1.25rem 0;
        background: #fef2f2; border: 1px solid #fecaca;
        border-radius: .75rem; padding: .9rem 1rem;
        color: #991b1b; font-size: .82rem;
        display: flex; gap: .75rem; align-items: flex-start;
    }
    .err-alert i { color: #dc2626; font-size: 1rem; flex-shrink: 0; margin-top: .05rem; }
    .err-alert ul { margin: 0; padding-left: 1.1rem; }

    /* Responsive */
    @media (max-width: 640px) {
        .compose-topbar { padding: .65rem .9rem; }
        .compose-body { padding: .75rem .5rem 1.5rem; }
        .add-bar { flex-direction: column; align-items: stretch; }
        .btn-add { justify-content: center; }
        .action-btn span { display: none; }
        .btn-save { padding: .4rem; width: 32px; height: 32px; border-radius: 50%; }
        .btn-save i { margin: 0; }
        .table-responsive-hide { display: none; }
        .add-select, .add-input { width: 100%; border-bottom: 1.5px solid #e2e8f0; }
    }
</style>

<div class="compose-wrapper">

    <!-- Top bar -->
    <div class="compose-topbar">
        <a href="{{ url()->previous() }}" class="btn-back-compose">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-diagram-3-fill me-2" style="color:#10b981;"></i>Manajemen Unit</h1>
    </div>

    <!-- Body -->
    <div class="compose-body">
        <div class="compose-card">
            
            <!-- Error -->
            @if($errors->any())
            <div class="err-alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div>
                    <strong>Gagal menyimpan data</strong>
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Add Form Bar -->
            <form action="{{ route('units.store') }}" method="POST" class="add-bar">
                @csrf
                <i class="bi bi-diagram-3" style="font-size:1.2rem; color:#94a3b8;"></i>
                
                <select name="branch_id" class="add-select" required>
                    <option value="">— Pilih Cabang —</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>

                <input type="text" name="name" class="add-input" placeholder="Ketik nama unit baru (misal: SDIA 1 Pusat)..." required>
                
                <label class="modern-checkbox-wrap">
                    <input type="checkbox" name="is_sekretariat" value="1" id="is_sekretariat">
                    <span>Sekretariat Pusat</span>
                </label>

                <button type="submit" class="btn-add">
                    <i class="bi bi-plus-lg"></i> Simpan
                </button>
            </form>

            <!-- Table -->
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th>Profil Unit</th>
                            <th class="table-responsive-hide" style="width: 140px;">Tipe Unit</th>
                            <th>Edit Data Unit</th>
                            <th style="text-align: right; width: 100px; padding-right: 1.25rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            @if($unit->name === 'Administrator') @continue @endif
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: .85rem;">
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
                                <td class="table-responsive-hide">
                                    @if($unit->is_sekretariat)
                                        <span class="role-pill rp-sek"><i class="bi bi-star-fill"></i> Sekretariat</span>
                                    @else
                                        <span class="role-pill rp-biasa"><i class="bi bi-diagram-3"></i> Unit Biasa</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('units.update', $unit) }}" method="POST" class="d-flex gap-2 align-items-center" style="margin:0; flex-wrap: wrap;">
                                        @csrf @method('PUT')
                                        <div style="flex: 1; min-width: 150px;">
                                            <select name="branch_id" class="edit-select" required>
                                                @foreach($branches as $b)
                                                    <option value="{{ $b->id }}" {{ $unit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div style="flex: 1.5; min-width: 150px;">
                                            <input type="text" name="name" value="{{ $unit->name }}" class="edit-input" required>
                                        </div>
                                        <button class="action-btn btn-save" type="submit" title="Simpan Perubahan">
                                            <i class="bi bi-check2-circle"></i> <span>Simpan</span>
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align: right; padding-right: 1.25rem;">
                                    <form action="{{ route('units.destroy', $unit) }}" method="POST" style="margin:0; display:inline-block;">
                                        @csrf @method('DELETE')
                                        <button class="action-btn btn-del" type="submit" title="Hapus Unit" onclick="return confirm('Yakin ingin menghapus unit ini secara permanen?')">
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

            <!-- Pagination -->
            @if($units->hasPages())
                <div class="pagination-wrapper">
                    {{ $units->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
