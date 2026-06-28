@extends('layouts.mailbox')
@section('title', 'Monitoring Surat')

@section('content')
<div class="mail-scroll p-4" style="background:#f8fafc;">
<style>
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; margin-bottom: 2rem; }
    
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
    
    .badge-status { display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; text-transform: uppercase; letter-spacing: 0.02em; }
    
    .search-box {
        display: flex; gap: 0.5rem; max-width: 400px; margin-bottom: 1.5rem;
    }
    .search-input {
        flex: 1; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; outline: none; transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #4f46e5; }
    .search-btn {
        background: #4f46e5; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-weight: 600; cursor: pointer;
    }
</style>

<div>
    <h1 class="page-title"><i class="bi bi-envelope-open-fill text-primary me-2"></i>Monitoring Surat</h1>
    <p class="page-sub">Pemantauan semua aktivitas surat yang terdaftar di dalam sistem.</p>
</div>

<form method="GET" action="{{ route('admin.monitoring') }}" class="search-box">
    <input type="text" name="search" class="search-input" placeholder="Cari No. Surat atau Subjek..." value="{{ request('search') }}">
    <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
    @if(request('search'))
        <a href="{{ route('admin.monitoring') }}" class="btn btn-light border">Reset</a>
    @endif
</form>

<div class="table-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th style="width:50px;">#</th>
                <th>Subjek / No Surat</th>
                <th>Pembuat</th>
                <th>Tipe & Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $index => $l)
                <tr>
                    <td class="text-muted">{{ $letters->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight: 600; color: #0f172a; margin-bottom: 2px;">{{ $l->subject }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $l->letter_number ?? 'Belum ada nomor (Draft)' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; color: #334155;">{{ $l->sender->name ?? 'Sistem' }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $l->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="margin-bottom:4px;">
                            <span class="badge bg-secondary" style="font-size:0.65rem;">{{ strtoupper($l->type ?? 'INTERNAL') }}</span>
                        </div>
                        @if($l->status == 'draft')
                            <span class="badge-status" style="background:#fef3c7; color:#d97706; border-color:#fde68a;">Draft</span>
                        @elseif($l->status == 'final')
                            <span class="badge-status">Selesai</span>
                        @else
                            <span class="badge-status" style="background:#e0e7ff; color:#4f46e5; border-color:#c7d2fe;">Proses</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('letters.show', $l) }}" class="btn btn-sm btn-light border rounded-pill" style="font-size:0.75rem; font-weight:600; color:#4f46e5;">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada surat yang terdaftar di sistem.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    @if($letters->hasPages())
    <div class="p-3 bg-light border-top">
        {{ $letters->links() }}
    </div>
    @endif
</div>

</div>
@endsection
