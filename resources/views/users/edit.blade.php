@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<style>
    .form-panel { background:#fff;border:1px solid #e8edf4;border-radius:1rem;padding:2rem; }
    .form-label { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:0.5rem; }
    .form-control,.form-select { height:48px;border-radius:0.75rem;border:1.5px solid #e8edf4;background:#f8faff;font-size:0.95rem;font-weight:500;color:#0f172a;transition:all .2s; }
    .form-control:focus,.form-select:focus { border-color:#2563eb;background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,0.08);outline:none; }
    .form-control::placeholder { color:#94a3b8;font-weight:400; }
    .err-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:0.75rem;padding:1rem 1.25rem;color:#991b1b;display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.5rem; }
    .err-alert i { font-size:1.25rem;color:#dc2626; }
    .err-alert ul { margin:0;padding-left:1.25rem;font-size:0.85rem;margin-top:0.25rem; }
    .btn-submit { background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border:none;border-radius:0.75rem;font-size:0.9rem;font-weight:700;padding:0 2rem;height:48px;display:inline-flex;align-items:center;gap:0.5rem;transition:all .2s; }
    .btn-submit:hover { transform:translateY(-2px);box-shadow:0 8px 16px rgba(37,99,235,0.2);color:#fff; }
    .btn-back { display:inline-flex;align-items:center;gap:0.5rem;background:#f8faff;border:1.5px solid #e8edf4;color:#475569;border-radius:0.6rem;padding:0.45rem 1rem;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all .2s; }
    .btn-back:hover { background:#eff6ff;color:#2563eb;border-color:#bfdbfe; }
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; }

    /* User identity card */
    .identity-card { background:#f8faff;border:1px solid #e8edf4;border-radius:1rem;padding:1.25rem;margin-bottom:1.75rem;display:flex;align-items:center;gap:1rem; }
    .id-avatar { width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:800;flex-shrink:0;background:#dbeafe;color:#2563eb; }
    .id-name { font-weight:800;font-size:1rem;color:#0f172a;line-height:1.2; }
    .id-email { font-size:0.78rem;color:#64748b;margin-top:2px; }

    .info-box { background:linear-gradient(to bottom right,#eff6ff,#fff);border:1px solid #bfdbfe;border-radius:1rem;padding:1.5rem; }
    .info-row { display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:0.85rem;font-size:0.85rem;color:#334155;line-height:1.5; }
    .info-row:last-child { margin-bottom:0; }
    .info-icon { width:22px;height:22px;border-radius:6px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:0.75rem;flex-shrink:0;margin-top:2px; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="page-title">Edit Pengguna</h1>
        <p class="page-sub">Ubah data dan hak akses pengguna.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
</div>

@if($errors->any())
    <div class="err-alert">
        <i class="bi bi-exclamation-octagon-fill"></i>
        <div>
            <strong>Gagal menyimpan perubahan</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <form action="{{ route('users.update', $user) }}" method="POST" class="form-panel shadow-sm">
            @csrf @method('PUT')

            {{-- Identity Summary --}}
            <div class="identity-card">
                <div class="id-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                <div>
                    <div class="id-name">{{ $user->name }}</div>
                    <div class="id-email">{{ $user->email }}</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kata Sandi Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    <div style="font-size:0.72rem;color:#94a3b8;margin-top:5px;display:flex;gap:4px;">
                        <i class="bi bi-info-circle text-primary"></i> Biarkan kosong untuk mempertahankan kata sandi lama.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Role / Hak Akses <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="staf_unit"          {{ old('role',$user->role)==='staf_unit'          ? 'selected':'' }}>Staf Unit — Pembuat Surat</option>
                    <option value="staf_tu"            {{ old('role',$user->role)==='staf_tu'            ? 'selected':'' }}>Staf TU Sekretariat</option>
                    <option value="kasubag_tu"         {{ old('role',$user->role)==='kasubag_tu'         ? 'selected':'' }}>Kasubag TU Sekretariat</option>
                    <option value="kepala_sekretariat" {{ old('role',$user->role)==='kepala_sekretariat' ? 'selected':'' }}>Kepala Sekretariat</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Penempatan Unit <span class="text-danger">*</span></label>
                <select name="unit_id" class="form-select" required>
                    @foreach($units as $unit)
                        @if($unit->name === 'Administrator' && $user->unit_id != $unit->id) @continue @endif
                        <option value="{{ $unit->id }}" {{ $user->unit_id == $unit->id ? 'selected':'' }}>
                            {{ $unit->name }} (Cab. {{ $unit->branch->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex justify-content-end pt-3" style="border-top:1px solid #e8edf4;">
                <button class="btn-submit"><i class="bi bi-save-fill"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="info-box shadow-sm">
            <div style="font-weight:800;color:#1e3a8a;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                <i class="bi bi-shield-fill-check text-primary"></i> Keterangan Role
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="bi bi-person-fill"></i></div>
                <div><strong>Staf Unit</strong> — Membuat dan mengirim surat antar unit.</div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div><strong>Staf TU</strong> — Input surat eksternal, memberi nomor agenda, dan meneruskan ke Kasubag.</div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div><strong>Kasubag TU</strong> — Mereview surat dan membuat disposisi ke unit terkait.</div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="bi bi-star-fill"></i></div>
                <div><strong>Kepala Sekretariat</strong> — Hak akses tertinggi pengelolaan surat di Sekretariat.</div>
            </div>
        </div>
    </div>
</div>
@endsection