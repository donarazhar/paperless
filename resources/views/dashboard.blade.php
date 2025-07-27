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
        <h4 class="mb-1">Assalamualaikum, Selamat {{ $waktu }} {{ Auth::user()->name }}!</h4>
        <p class="text-muted">
            @php
                $roleLabel = [
                    'admin' => 'Administrator',
                    'staff' => 'Staff',
                    'manager' => 'Manajer',
                ][Auth::user()->role] ?? ucfirst(Auth::user()->role);
            @endphp
            {{ $roleLabel }} Unit {{ Auth::user()->unit->name }}
        </p>
    </div>

    <div class="row g-4">
        {{-- Ringkasan Statistik --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-envelope-fill fs-1 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0">Surat Masuk Hari Ini</h6>
                        <p class="h3 mb-0">{{ $inboundToday }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-send-fill fs-1 text-success me-3"></i>
                    <div>
                        <h6 class="mb-0">Surat Keluar Hari Ini</h6>
                        <p class="h3 mb-0">{{ $outboundToday }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-bell-fill fs-1 text-warning me-3"></i>
                    <div>
                        <h6 class="mb-0">Surat Belum Dibaca</h6>
                        <p class="h3 mb-0">{{ $unreadCount }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-arrow-repeat fs-1 text-info me-3"></i>
                    <div>
                        <h6 class="mb-0">Surat Disposisi</h6>
                        <p class="h3 mb-0">{{ $withDisposition }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK 7 HARI TERAKHIR --}}
    @if(Auth::user()->role == 'admin')
        <div class="card my-5">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill"></i> Grafik Surat (7 Hari Terakhir)
            </div>
            <div class="card-body">
                <canvas id="letterChart" height="100"></canvas>
            </div>
        </div>
    @endif

    {{-- Notifikasi Surat Baru --}}
    @if(Auth::user()->role != 'admin')
        <div class="mt-5">
            <h5>Notifikasi Terbaru</h5>
            <ul class="list-group">
                @forelse($notifications as $note)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi {{ $note->icon }} me-1"></i>
                            <a href="{{ route('letters.show', ['letter' => Hashids::encode($note->letter_id)]) }}"
                                class="text-decoration-none">
                                [{{ $note->letter_number }}] {{ $note->text }}
                            </a>
                        </div>
                        <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">
                        Tidak ada notifikasi baru
                    </li>
                @endforelse
            </ul>
        </div>
    @endif

    @push('scripts')
        @if(Auth::user()->role != 'admin')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if($unreadCount > 0)
                        Swal.fire({
                            icon: 'info',
                            title: 'Surat Baru!',
                            text: 'Anda punya {{ $unreadCount }} surat belum dibaca.',
                            timer: 4000,
                            showConfirmButton: false,
                        });
                    @endif

                    @if($withDisposition > 0)
                        Swal.fire({
                            icon: 'warning',
                            title: 'Disposisi Baru!',
                            text: '{{ $withDisposition }} disposisi belum Anda tanggapi.',
                            timer: 4000,
                            showConfirmButton: false,
                        });
                    @endif
                        });
            </script>
        @endif
    @endpush
@endsection

@push('scripts')
    @if(Auth::user()->role == 'admin')
        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('letterChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [
                            {
                                label: 'Surat Masuk',
                                data: @json($dataInbound),
                                tension: 0.3,
                            },
                            {
                                label: 'Surat Keluar',
                                data: @json($dataOutbound),
                                tension: 0.3,
                            },
                            {
                                label: 'Disposisi',
                                data: @json($dataDispo),
                                tension: 0.3,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        stacked: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Jumlah' }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endpush