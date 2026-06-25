@extends('layouts.mailbox')
@section('title', 'Manajemen Cabang')

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
        max-width: 900px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        height: fit-content;
    }

    /* Add Form Bar (like type-bar) */
    .add-bar {
        display: flex;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        align-items: center;
    }
    .add-input {
        flex: 1;
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
        background: #4f46e5; color: #fff;
        border: none; border-radius: 100px;
        padding: .55rem 1.4rem; font-size: .875rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
        box-shadow: 0 2px 10px rgba(79,70,229,.25);
    }
    .btn-add:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(79,70,229,.35); }

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

    .branch-avatar { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; background: #eef2ff; color: #4f46e5; }
    .branch-name { font-weight: 600; font-size: .9rem; color: #0f172a; margin-bottom: 0; }

    .edit-input { background: transparent; border: none; border-bottom: 1.5px solid #e2e8f0; border-radius: 0; padding: .25rem 0; font-size: .9rem; font-weight: 500; transition: all .2s; width: 100%; outline: none; }
    .edit-input:focus { border-color: #6366f1; }

    /* Action Buttons */
    .action-btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 100px; border: none; transition: all .2s; text-decoration: none; font-weight: 600; font-size: .8rem; padding: .4rem .8rem; gap: .3rem; cursor: pointer; }
    .btn-save { background: #eef2ff; color: #4f46e5; border: 1.5px solid #c7d2fe; }
    .btn-save:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .btn-del { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; width: 32px; height: 32px; padding: 0; border-radius: 50%; }
    .btn-del:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
    
    .role-pill { display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .75rem; border-radius: 100px; font-size: .75rem; font-weight: 600; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

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
    }
</style>

<div class="compose-wrapper">

    <!-- Top bar -->
    <div class="compose-topbar">
        <a href="{{ url()->previous() }}" class="btn-back-compose">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-building-fill me-2" style="color:#6366f1;"></i>Manajemen Cabang</h1>
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
            <form action="{{ route('branches.store') }}" method="POST" class="add-bar">
                @csrf
                <i class="bi bi-building-add" style="font-size:1.2rem; color:#94a3b8;"></i>
                <input type="text" name="name" class="add-input" placeholder="Ketik nama cabang baru (misal: Cabang Kebayoran Baru)..." required>
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
                            <th>Profil Cabang</th>
                            <th class="table-responsive-hide" style="width: 150px;">Total Unit</th>
                            <th>Edit Nama</th>
                            <th style="text-align: right; width: 100px; padding-right: 1.25rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-weight: 600; font-size: .85rem;">
                                    {{ $branches->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="branch-avatar">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="branch-name">{{ $branch->name }}</div>
                                    </div>
                                </td>
                                <td class="table-responsive-hide">
                                    <span class="role-pill"><i class="bi bi-diagram-3"></i> {{ $branch->units_count }} Unit</span>
                                </td>
                                <td>
                                    <form action="{{ route('branches.update', $branch) }}" method="POST" class="d-flex gap-2 align-items-center" style="margin:0;">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $branch->name }}" class="edit-input" required>
                                        <button class="action-btn btn-save" type="submit" title="Simpan Perubahan">
                                            <i class="bi bi-check2-circle"></i> <span>Simpan</span>
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align: right; padding-right: 1.25rem;">
                                    <form action="{{ route('branches.destroy', $branch) }}" method="POST" style="margin:0; display:inline-block;">
                                        @csrf @method('DELETE')
                                        <button class="action-btn btn-del" type="submit" title="Hapus Cabang" onclick="return confirm('Yakin ingin menghapus cabang ini? Pastikan tidak ada unit yang masih terkait.')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-building-dash"></i>
                                        <div class="empty-title">Tidak Ada Cabang</div>
                                        <div class="empty-desc">Tambahkan cabang baru melalui form di atas.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($branches->hasPages())
                <div class="pagination-wrapper">
                    {{ $branches->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
