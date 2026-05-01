@extends('layout')
@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar data dan status laporan terkini.')

@section('content')

    {{-- WELCOME BANNER --}}
    <div
        class="bg-slate-900 rounded-xl border border-slate-800 p-8 sm:p-10 flex flex-col justify-center relative overflow-hidden mb-8">
        <div class="relative z-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight mb-3">Selamat datang, Admin.</h2>
            <p class="text-slate-400 text-sm sm:text-base max-w-xl leading-relaxed">
                Sistem Informasi Tanggap Bencana memberikan Anda kendali penuh untuk memantau laporan masyarakat,
                memverifikasi kejadian, dan mengambil tindakan cepat.
            </p>
        </div>

        {{-- Subtle background element --}}
        <div
            class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-primary-600/20 to-transparent pointer-events-none">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Chart Area (Left, span 2) --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col h-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-slate-900">Statistik Laporan Masuk (7 Hari Terakhir)</h3>
            </div>
            <div class="flex-1 w-full relative min-h-[300px]">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Stat Cards (Right, span 1) --}}
        <div class="flex flex-col gap-4 h-full">
            {{-- Total --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-slate-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Total Laporan</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $total ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-file-earmark-text text-xl"></i>
                </div>
            </div>

            {{-- Pending --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-amber-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Menunggu Tindakan</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $pending ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-hourglass-split text-xl"></i>
                </div>
            </div>

            {{-- Verified --}}
            <div
                class="flex-1 bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex items-center justify-between group hover:border-emerald-300 transition-colors">
                <div>
                    <p class="text-xs text-slate-500 font-medium mb-1">Terverifikasi</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $selesai ?? 0 }}</p>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:bg-white transition-colors">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reportChart').getContext('2d');

            // Data from backend
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const data = {!! json_encode($chartData ?? []) !!};

            // Theme colors
            const primaryColor = '#4f46e5';
            const primaryColorLight = 'rgba(79, 70, 229, 0.1)';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: data,
                        borderColor: primaryColor,
                        backgroundColor: primaryColorLight,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: primaryColor,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 13, family: "'Inter', sans-serif" },
                            displayColors: false,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { family: "'Inter', sans-serif", size: 12 },
                                color: '#64748b'
                            },
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false,
                            },
                            border: { display: false }
                        },
                        x: {
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 12 },
                                color: '#64748b'
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            border: { display: false }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
@endsection