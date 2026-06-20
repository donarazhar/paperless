@extends('layouts.app')
@section('title', 'Manajemen Unit')

@section('content')
<style>
    .add-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:1.35rem 1.5rem;margin-bottom:1.25rem; }
    .add-panel .ap-title { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#64748b;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem; }
    .add-panel .form-control,.add-panel .form-select { height:44px;border-radius:0.65rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.875rem;font-weight:500;color:#0f172a;transition:all .2s; }
    .add-panel .form-control:focus,.add-panel .form-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,0.08);outline:none; }
    .add-panel .form-control::placeholder { color:#94a3b8;font-weight:400; }

    .data-table { width:100%;border-collapse:separate;border-spacing:0; }
    .data-table thead th { font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;padding:0.6rem 0.85rem;border-bottom:1px solid #e8edf4;white-space:nowrap;background:#fafbfd; }
    .data-table thead th:first-child { border-radius:0.75rem 0 0 0;padding-left:1.25rem; }
    .data-table thead th:last-child  { border-radius:0 0.75rem 0 0;padding-right:1.25rem; }
    .data-table tbody tr:hover td { background:#f8faff; }
    .data-table tbody td { padding:0.85rem 0.85rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:0.865rem;color:#334155; }
    .data-table tbody td:first-child { padding-left:1.25rem; }
    .data-table tbody td:last-child  { padding-right:1.25rem; }
    .data-table tbody tr:last-child td { border-bottom:none; }

    .edit-input,.edit-select { height:36px;border-radius:0.55rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.835rem;padding:0 0.75rem;transition:all .2s; }
    .edit-input { min-width:130px; }
    .edit-select { min-width:110px; }
    .edit-input:focus,.edit-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.09);outline:none; }

    .btn-add  { display:inline-flex;align-items:center;gap:0.45rem;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border:none;border-radius:0.65rem;font-size:0.875rem;font-weight:700;height:44px;padding:0 1.25rem;cursor:pointer;transition:all .15s; }
    .btn-add:hover { opacity:0.88;transform:translateY(-1px); }
    .btn-save { display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#166534;border:none;border-radius:0.5rem;font-size:0.74rem;font-weight:700;padding:0.3rem 0.65rem;cursor:pointer;transition:background .15s;white-space:nowrap; }
    .btn-save:hover { background:#bbf7d0; }
    .btn-del  { display:inline-flex;align-items:center;gap:4px;background:#fef2f2;color:#dc2626;border:none;border-radius:0.5rem;font-size:0.74rem;font-weight:700;padding:0.3rem 0.65rem;cursor:pointer;transition:background .15s;white-space:nowrap; }
    .btn-del:hover { background:#fee2e2; }
    .btn-rpt  { display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#2563eb;border:none;border-radius:0.5rem;font-size:0.74rem;font-weight:700;padding:0.3rem 0.65rem;text-decoration:none;white-space:nowrap;transition:background .15s; }
    .btn-rpt:hover { background:#dbeafe;color:#1d4ed8; }

    .sek-badge { display:inline-flex;align-items:center;gap:4px;background:#dbeafe;color:#1d4ed8;font-size:0.68rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:100px; }
    .unit-badge { display:inline-flex;align-items:center;gap:4px;background:#f1f5f9;color:#475569;font-size:0.68rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:100px; }

    .unit-card { background:#fff;border:1px solid #e8edf4;border-radius:0.9rem;padding:1rem 1.1rem;margin-bottom:0.65rem; }
    .uc-name { font-size:0.9rem;font-weight:700;color:#0f172a;margin-bottom:2px; }
    .uc-branch { font-size:0.75rem;color:#64748b;margin-bottom:0.5rem;display:flex;align-items:center;gap:4px; }
    .uc-actions { display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.65rem; }

    .empty-state { text-align:center;padding:4rem 1rem;color:#94a3b8; }
    .empty-state i { font-size:3rem;display:block;margin-bottom:0.75rem;color:#cbd5e1; }

    .table-wrap { display:block; }
    .cards-wrap { display:none; }
    @media(max-width:900px) { .table-wrap{display:none;} .cards-wrap{display:block;} }

    /* Checkbox custom */
    .form-check-input:checked { background-color:#2563eb;border-color:#2563eb; }
</style>

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
    <table class="data-table">
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
