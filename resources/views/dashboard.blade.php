@extends('layouts.mailbox')
@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;
    $waktu = $hour < 10 ? 'Pagi' : ($hour < 15 ? 'Siang' : ($hour < 18 ? 'Sore' : 'Malam'));
    $user  = Auth::user();
    $role  = $user->role;
    $isManager = in_array($role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat']);
@endphp



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
            <div class="stat-icon" style="background:#eef2ff;">
                <i class="bi bi-envelope-arrow-down-fill" style="color:#6366f1;"></i>
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
                    <div class="notif-icon-wrap" style="background:#eef2ff;">
                        <i class="bi {{ $note->icon }}" style="color:#6366f1;"></i>
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
                    borderColor: '#6366f1',
                    backgroundColor: gradIn,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
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