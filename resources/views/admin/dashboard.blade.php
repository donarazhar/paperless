@extends('layouts.mailbox')
@section('title', 'Superadmin Dashboard')

@section('content')
<div class="mail-scroll p-4" style="background:#f8fafc;">
<style>
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; margin-bottom: 2rem; }
    
    .stat-card {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    
    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .stat-icon.primary { background: #e0e7ff; color: #4f46e5; }
    .stat-icon.success { background: #dcfce7; color: #16a34a; }
    .stat-icon.warning { background: #fef3c7; color: #d97706; }
    .stat-icon.danger { background: #fee2e2; color: #dc2626; }
    
    .stat-info { flex: 1; }
    .stat-val { font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; }

    .section-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .section-title i { color: #4f46e5; }
    
    .table-card {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .table-modern { width: 100%; border-collapse: collapse; }
    .table-modern th { background: #f8fafc; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .table-modern td { padding: 1rem 1.25rem; font-size: 0.875rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .table-modern tr:last-child td { border-bottom: none; }
    .table-modern tr:hover td { background: #f8fafc; }
    
    .badge-role { display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; text-transform: uppercase; letter-spacing: 0.02em; }
    .badge-status { display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; text-transform: uppercase; letter-spacing: 0.02em; }
    
    .user-avatar { width: 36px; height: 36px; border-radius: 10px; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0; }
</style>

<div>
    <h1 class="page-title">Superadmin Dashboard</h1>
    <p class="page-sub">Ringkasan sistem dan monitoring aktivitas e-Office.</p>
</div>

<!-- Stats Row 1: Users -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bi bi-people-fill"></i></div>
            <div class="stat-info">
                <div class="stat-val">{{ $usersCount }}</div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-shield-fill-check"></i></div>
            <div class="stat-info">
                <div class="stat-val">{{ $adminCount }}</div>
                <div class="stat-label">Total Admin</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-envelope-paper-fill"></i></div>
            <div class="stat-info">
                <div class="stat-val">{{ $totalSurat }}</div>
                <div class="stat-label">Total Surat</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon danger"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="stat-info">
                <div class="stat-val">{{ $suratDraft }}</div>
                <div class="stat-label">Draft Surat</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Log Pengguna -->
    <div class="col-lg-5">
        <div class="section-title"><i class="bi bi-person-lines-fill"></i> Pengguna Baru</div>
        <div class="table-card">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar">{{ mb_strtoupper(mb_substr($u->name ?? $u->email, 0, 1)) }}</div>
                                    <div>
                                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;">{{ $u->name ?? 'Belum Login' }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-role">{{ $u->role ?? 'N/A' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3 bg-light border-top text-center">
                <a href="{{ route('users.index') }}" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:#4f46e5;">Kelola Semua Pengguna <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Monitoring Surat -->
    <div class="col-lg-7">
        <div class="section-title"><i class="bi bi-envelope-open-fill"></i> Monitoring Surat Terbaru</div>
        <div class="table-card">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Subjek / No Surat</th>
                        <th>Pembuat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLetters as $l)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #0f172a; margin-bottom: 2px;" class="text-truncate-2">{{ $l->subject }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">{{ $l->letter_number ?? 'Draft' }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; color: #334155;">{{ $l->sender->name ?? 'Sistem' }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $l->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($l->status == 'draft')
                                    <span class="badge-status" style="background:#fef3c7; color:#d97706; border-color:#fde68a;">Draft</span>
                                @elseif($l->status == 'final')
                                    <span class="badge-status">Selesai</span>
                                @else
                                    <span class="badge-status" style="background:#e0e7ff; color:#4f46e5; border-color:#c7d2fe;">Proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">Belum ada surat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3 bg-light border-top text-center">
                <a href="{{ route('letters.index') ?? route('letters.inbound') }}" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:#4f46e5;">Lihat Semua Surat <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
