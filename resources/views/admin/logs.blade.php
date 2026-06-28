@extends('layouts.mailbox')
@section('title', 'Log Activity')

@section('content')
<div class="mail-scroll p-4" style="background:#f8fafc;">
<style>
    .page-title { font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin-bottom:0.25rem; }
    .page-sub { font-size:0.85rem;color:#64748b; margin-bottom: 2rem; }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.8rem;
        top: 1.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4f46e5;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px #e0e7ff;
    }
</style>

<div>
    <h1 class="page-title"><i class="bi bi-clock-history text-primary me-2"></i>Log Activity</h1>
    <p class="page-sub">Rekam jejak seluruh aktivitas pengguna di dalam sistem (Segera Hadir).</p>
</div>

<div class="timeline mt-4">
    <div class="timeline-item">
        <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">Sistem Monitoring Aktif</div>
        <div style="font-size: 0.85rem; color: #64748b;">Log aktivitas sistem sedang dalam tahap pengumpulan data dari PresensiGPS dan riwayat internal persuratan.</div>
        <div class="mt-2" style="font-size: 0.75rem; color: #94a3b8;"><i class="bi bi-calendar2-event me-1"></i> Saat ini</div>
    </div>
</div>

</div>
@endsection
