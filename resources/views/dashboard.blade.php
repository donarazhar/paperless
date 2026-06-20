@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;
    $waktu = $hour < 10 ? 'Pagi' : ($hour < 15 ? 'Siang' : ($hour < 18 ? 'Sore' : 'Malam'));
    $user  = Auth::user();
    $role  = $user->role;
    $isManager = in_array($role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat']);
@endphp

<style>
    /* ── Stat Cards ── */
    .stat-card {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1rem;
        padding: 1.35rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        transition: transform .15s, box-shadow .15s;
        text-decoration: none;
        color: inherit;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(15,23,42,0.08);
    }

    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 0.2rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .stat-sub {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }

    /* ── Greeting Banner ── */
    .greeting-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #2563eb 100%);
        border-radius: 1.1rem;
        padding: 1.75rem 2rem;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.75rem;
    }

    .greeting-banner::before {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        right: -60px; top: -100px;
        pointer-events: none;
    }

    .greeting-banner .gb-emoji {
        font-size: 2.5rem;
        line-height: 1;
        animation: wave 2s ease-in-out infinite;
        display: inline-block;
    }

    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25%  { transform: rotate(18deg); }
        75%  { transform: rotate(-8deg); }
    }

    .greeting-banner h3 {
        font-size: clamp(1.1rem, 2.5vw, 1.45rem);
        font-weight: 800;
        margin-bottom: 0.35rem;
        letter-spacing: -0.03em;
    }

    .greeting-banner p {
        color: rgba(255,255,255,0.65);
        font-size: 0.875rem;
        margin: 0;
    }

    .gb-chip {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 100px;
        color: rgba(255,255,255,0.9);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.3rem 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 0.65rem;
    }

    /* ── Notif Card ── */
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 0.9rem 1rem;
        border-radius: 0.75rem;
        background: #f8faff;
        border: 1px solid #eef1f7;
        text-decoration: none;
        color: inherit;
        transition: background .15s, border-color .15s, transform .15s;
        margin-bottom: 0.6rem;
    }

    .notif-item:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        transform: translateX(3px);
        color: inherit;
    }

    .notif-icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .notif-title { font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
    .notif-text  { font-size: 0.78rem; color: #64748b; margin-bottom: 3px; }
    .notif-time  { font-size: 0.7rem; color: #94a3b8; display: flex; align-items: center; gap: 4px; }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; color: #cbd5e1; }
    .empty-state p { font-size: 0.875rem; margin: 0; }

    /* ── Section headers ── */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.1rem;
    }

    .section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i { color: #2563eb; }

    /* ── Panel Card ── */
    .panel-card {
        background: #fff;
        border: 1px solid #e8edf4;
        border-radius: 1.1rem;
        padding: 1.5rem;
        height: 100%;
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .greeting-banner { padding: 1.35rem 1.25rem; flex-direction: column; align-items: flex-start; }
        .greeting-banner .gb-right { display: none; }
        .stat-card { padding: 1rem 1.1rem; }
        .stat-icon { width: 44px; height: 44px; font-size: 1.1rem; border-radius: 12px; }
        .stat-value { font-size: 1.6rem; }
    }

    @media (max-width: 480px) {
        .stat-value { font-size: 1.4rem; }
    }
</style>

{{-- ══ GREETING BANNER ══ --}}
<div class="greeting-banner">
    <div>
        <span class="gb-emoji">👋</span>
        <h3 class="mt-2">Selamat {{ $waktu }}, {{ explode(' ', $user->name)[0] }}!</h3>
        <p>Hari yang produktif dimulai dengan pengelolaan surat yang baik.</p>
        <div class="gb-chip">
            <i class="bi bi-person-fill"></i>
            {{ ucwords(str_replace('_', ' ', $role)) }} — {{ $user->unit->name ?? 'Administrator' }}
        </div>
    </div>
    <div class="gb-right text-end" style="flex-shrink:0;">
        <div style="font-size: 4rem; opacity: 0.18;"><i class="bi bi-envelope-paper-fill"></i></div>
    </div>
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <a href="{{ route('letters.inbound') }}" class="stat-card d-block">
            <div class="stat-icon" style="background:#eff6ff;">
                <i class="bi bi-envelope-arrow-down-fill" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-label">Masuk Hari Ini</div>
                <div class="stat-value">{{ $inboundToday }}</div>
                <div class="stat-sub">Surat masuk</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="{{ route('letters.outbound') }}" class="stat-card d-block">
            <div class="stat-icon" style="background:#f0fdf4;">
                <i class="bi bi-send-fill" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="stat-label">Keluar Hari Ini</div>
                <div class="stat-value">{{ $outboundToday }}</div>
                <div class="stat-sub">Surat keluar</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="{{ route('letters.inbound') }}" class="stat-card d-block">
            <div class="stat-icon" style="background:#fffbeb;">
                <i class="bi bi-bell-fill" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="stat-label">Belum Dibaca</div>
                <div class="stat-value">{{ $unreadCount }}</div>
                <div class="stat-sub">Perlu ditinjau</div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff;">
                <i class="bi bi-arrow-repeat" style="color:#9333ea;"></i>
            </div>
            <div>
                <div class="stat-label">Disposisi</div>
                <div class="stat-value">{{ $withDisposition }}</div>
                <div class="stat-sub">Menunggu tindakan</div>
            </div>
        </div>
    </div>
</div>

{{-- ══ CHART + NOTIF ══ --}}
<div class="row g-3">

    @if($isManager)
    <div class="col-lg-7">
        <div class="panel-card">
            <div class="section-header">
                <div class="section-title">
                    <i class="bi bi-bar-chart-line-fill"></i> Laju Persuratan 7 Hari
                </div>
            </div>
            <canvas id="letterChart" style="max-height:240px;"></canvas>
        </div>
    </div>
    @endif

    <div class="{{ $isManager ? 'col-lg-5' : 'col-12' }}">
        <div class="panel-card">
            <div class="section-header">
                <div class="section-title">
                    <i class="bi bi-lightning-charge-fill"></i> Perlu Tindakan
                </div>
                @if(count($notifications) > 0)
                    <span style="background:#fef9c3; color:#b45309; font-size:0.7rem; font-weight:700; padding:0.25rem 0.65rem; border-radius:100px;">
                        {{ count($notifications) }} baru
                    </span>
                @endif
            </div>

            @forelse($notifications as $note)
                <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($note->letter_id)]) }}"
                   class="notif-item">
                    <div class="notif-icon-wrap" style="background:#eff6ff;">
                        <i class="bi {{ $note->icon }}" style="color:#2563eb;"></i>
                    </div>
                    <div style="flex:1; overflow:hidden;">
                        <div class="notif-title text-truncate">{{ $note->letter_number }}</div>
                        <div class="notif-text">{{ $note->text }}</div>
                        <div class="notif-time"><i class="bi bi-clock"></i> {{ $note->created_at->diffForHumans() }}</div>
                    </div>
                    <i class="bi bi-chevron-right" style="color:#cbd5e1; font-size:0.8rem; margin-top:2px;"></i>
                </a>
            @empty
                <div class="empty-state">
                    <i class="bi bi-check2-circle" style="color:#4ade80;"></i>
                    <p>Semua tugas sudah selesai!<br><span style="color:#cbd5e1;">Tidak ada notifikasi baru.</span></p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($unreadCount > 0)
            Swal.fire({ toast:true, icon:'info', title:'{{ $unreadCount }} surat belum dibaca', position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true });
        @endif
        @if($withDisposition > 0)
            Swal.fire({ toast:true, icon:'warning', title:'{{ $withDisposition }} disposisi menunggu', position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true });
        @endif
    });
</script>

@if($isManager)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('letterChart').getContext('2d');

    const gradIn = ctx.createLinearGradient(0, 0, 0, 300);
    gradIn.addColorStop(0, 'rgba(37,99,235,0.18)');
    gradIn.addColorStop(1, 'rgba(37,99,235,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Surat Masuk',
                    data: @json($dataInbound),
                    borderColor: '#2563eb',
                    backgroundColor: gradIn,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'Surat Keluar',
                    data: @json($dataOutbound),
                    borderColor: '#16a34a',
                    borderWidth: 2.5,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#16a34a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    borderDash: [5, 4],
                },
                {
                    label: 'Disposisi',
                    data: @json($dataDispo),
                    borderColor: '#9333ea',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, pointStyleWidth: 8, font: { size: 11, weight: '600' }, color: '#64748b' }
                },
                tooltip: { backgroundColor: '#0f172a', cornerRadius: 8, titleFont: { size: 12 }, bodyFont: { size: 12 } }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    border: { display: false },
                    ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endif
@endpush
@endsection