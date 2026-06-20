@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    @php
        $hour = now()->hour;
        if ($hour < 10) {
            $waktu = 'Pagi';
        } elseif ($hour < 15) {
            $waktu = 'Siang';
        } elseif ($hour < 18) {
            $waktu = 'Sore';
        } else {
            $waktu = 'Malam';
        }
    @endphp

    {{-- Greeting & Info Akun --}}
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Assalamualaikum, Selamat {{ $waktu }} {{ Auth::user()->name }}!</h4>
        <p class="text-muted">
            @php
                $roleLabel = str_replace('_', ' ', Auth::user()->role);
            @endphp
            <span class="text-capitalize">{{ $roleLabel }}</span> – Unit {{ Auth::user()->unit->name }}
        </p>
    </div>

    {{-- Ringkasan Statistik Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-envelope-fill fs-3 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small fw-semibold text-uppercase">Surat Masuk (Hari Ini)</p>
                        <h3 class="mb-0 fw-bold">{{ $inboundToday }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-send-fill fs-3 text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small fw-semibold text-uppercase">Surat Keluar (Hari Ini)</p>
                        <h3 class="mb-0 fw-bold">{{ $outboundToday }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-bell-fill fs-3 text-warning"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small fw-semibold text-uppercase">Belum Dibaca</p>
                        <h3 class="mb-0 fw-bold">{{ $unreadCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-arrow-repeat fs-3 text-info"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small fw-semibold text-uppercase">Menunggu Disposisi</p>
                        <h3 class="mb-0 fw-bold">{{ $withDisposition }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout Bawah: Chart & Notifikasi --}}
    <div class="row g-4">
        {{-- GRAFIK 7 HARI TERAKHIR (Khusus Staf TU / Kasubag) --}}
        @if(in_array(Auth::user()->role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat']))
            <div class="col-lg-7">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill text-primary me-2"></i> Laju Persuratan (7 Hari)</h5>
                    <canvas id="letterChart" height="120"></canvas>
                </div>
            </div>
        @endif

        {{-- Notifikasi Surat Baru / Disposisi --}}
        <div class="{{ in_array(Auth::user()->role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat']) ? 'col-lg-5' : 'col-12' }}">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Terbaru & Perlu Tindakan</h5>
                
                @if(count($notifications) > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($notifications as $note)
                            <a href="{{ route('letters.show', ['letter' => \Vinkla\Hashids\Facades\Hashids::encode($note->letter_id)]) }}" 
                               class="text-decoration-none p-3 rounded bg-light border border-white hover-shadow transition-all d-block">
                                <div class="d-flex align-items-start">
                                    <div class="mt-1 me-3">
                                        <i class="bi {{ $note->icon }} fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-dark fw-bold mb-1">{{ $note->letter_number }}</h6>
                                        <p class="text-muted small mb-1">{{ $note->text }}</p>
                                        <small class="text-secondary" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock"></i> {{ $note->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check2-circle fs-1 d-block mb-3 text-success"></i>
                        <p class="mb-0">Belum ada notifikasi baru.<br>Semua tugas sudah diselesaikan!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if($unreadCount > 0)
                    Swal.fire({
                        icon: 'info',
                        title: 'Surat Baru!',
                        text: 'Terdapat {{ $unreadCount }} surat yang belum diproses.',
                        timer: 4000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                @endif

                @if($withDisposition > 0)
                    Swal.fire({
                        icon: 'warning',
                        title: 'Disposisi Menunggu!',
                        text: 'Ada {{ $withDisposition }} disposisi yang butuh tanggapan.',
                        timer: 4000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                @endif
            });
        </script>

        @if(in_array(Auth::user()->role, ['staf_tu', 'kasubag_tu', 'kepala_sekretariat']))
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('letterChart').getContext('2d');
                    
                    // Gradient fill for lines
                    let gradientIn = ctx.createLinearGradient(0, 0, 0, 400);
                    gradientIn.addColorStop(0, 'rgba(15, 76, 129, 0.2)');
                    gradientIn.addColorStop(1, 'rgba(15, 76, 129, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($labels),
                            datasets: [
                                {
                                    label: 'Surat Masuk',
                                    data: @json($dataInbound),
                                    borderColor: '#0f4c81',
                                    backgroundColor: gradientIn,
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Surat Keluar',
                                    data: @json($dataOutbound),
                                    borderColor: '#198754',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    borderDash: [5, 5]
                                },
                                {
                                    label: 'Disposisi',
                                    data: @json($dataDispo),
                                    borderColor: '#ffc107',
                                    borderWidth: 2,
                                    tension: 0.4
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                });
            </script>
        @endif
    @endpush
@endsection