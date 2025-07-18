<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monitoring LCO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen py-10 px-4 md:px-10 font-sans">

    <!--  Login / Tambah Data -->
    <div class="flex justify-between items-center max-w-6xl mx-auto mb-6">
        @auth
            <a href="{{ route('monitoring.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded">
                + Tambah Data
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded ml-4">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Login
            </a>
        @endauth
    </div>

    <div class="max-w-6xl mx-auto space-y-10">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold">📊 Monitoring for LCO Live Trial</h1>
            <p class="text-green-400 mt-2">✅ Total Activation (Latest Day)</p>
            <p class="text-4xl font-bold mt-1">{{ $totalDone }} / {{ $totalTarget }}</p>
        </div>

        <!-- Daily Activation Chart -->
        <div>
            <h2 class="text-xl font-semibold mb-3">📅 Daily Activation</h2>
            <div class="bg-white rounded-xl p-6 shadow-lg overflow-x-auto">
                <canvas id="barChart" height="120"></canvas>
            </div>
        </div>

        <!--  Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!--  Daily Progress -->
            <div>
                <h2 class="text-xl font-semibold mb-3">📈 Daily Progress (Latest)</h2>
                <div class="bg-white text-black rounded-xl p-6 shadow-lg">
                    <canvas id="pieChart" height="200"></canvas>
                    <p class="text-xs text-center italic mt-2">*Activation was done on latest schedule</p>
                </div>
            </div>

            <!--  Product Distribution -->
            <div>
                <h2 class="text-xl font-semibold mb-3">📦 Product Distribution</h2>
                <div class="bg-white text-black rounded-xl p-6 shadow-lg">
                    <canvas id="productChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!--  Weekly Report -->
        <div>
            <h2 class="text-xl font-semibold mb-3">📊 Weekly Report (Activation)</h2>
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <canvas id="barHorizontal" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        // Daily Activation (Avg Jam)
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(
                    $barChartData->map(fn($item) => $item['day'] . ', ' . \Carbon\Carbon::parse($item['date'])->format('d M'))
                ) !!},
                datasets: [{
                    label: 'Rata-rata Jam',
                    data: {!! json_encode($barChartData->pluck('avg_jam')) !!},
                    backgroundColor: '#38bdf8',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#000' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#000' },
                        title: {
                            display: true,
                            text: 'Rata-rata Jam',
                            color: '#000'
                        },
                        grid: { color: '#e5e7eb' }
                    },
                    x: {
                        ticks: { color: '#000' },
                        title: {
                            display: true,
                            text: 'Hari, Tanggal',
                            color: '#000'
                        },
                        grid: { color: '#e5e7eb' }
                    }
                }
            }
        });

        //  Daily Progress Pie Chart
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: ['Done', 'Remaining'],
                datasets: [{
                    data: [{{ $totalDone }}, {{ $totalTarget - $totalDone }}],
                    backgroundColor: ['#22c55e', '#d1d5db']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#000' }
                    }
                }
            }
        });

        // Product Distribution Pie Chart
        new Chart(document.getElementById('productChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($productDist->pluck('label')) !!},
                datasets: [{
                    data: {!! json_encode($productDist->pluck('jumlah')) !!},
                    backgroundColor: ['#60a5fa', '#f97316', '#10b981', '#a855f7']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#000' }
                    }
                }
            }
        });

        //  Weekly Report
        new Chart(document.getElementById('barHorizontal'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($weeklyReport->pluck('label')) !!},
                datasets: [
                    {
                        label: 'Done',
                        data: {!! json_encode($weeklyReport->pluck('done')) !!},
                        backgroundColor: '#60a5fa'
                    },
                    {
                        label: 'Not Yet',
                        data: {!! json_encode($weeklyReport->pluck('not_yet')) !!},
                        backgroundColor: '#94a3b8'
                    }
                ]
            },
            options: {
                responsive: true,
                indexAxis: 'x',
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#000' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#000' }
                    },
                    x: {
                        ticks: { color: '#000' }
                    }
                }
            }
        });
    </script>
</body>
</html>
