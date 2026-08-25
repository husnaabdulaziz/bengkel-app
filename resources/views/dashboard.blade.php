<x-admin-layout title="Dashboard">

    <!-- Filter Periode -->
    <div class="btn-group mb-3">
        @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan (Sen-Min)', 'bulanan' => 'Bulanan'] as $key => $label)
            <a href="{{ route('dashboard', ['period' => $key]) }}"
               class="btn {{ $period === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Info Boxes -->
    <div class="row d-flex align-items-stretch">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                    <p>Total Penjualan</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalLaba, 0, ',', '.') }}</h4>
                    <p>Total Laba</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning h-100">
                <div class="inner">
                    <h4>{{ $totalPelanggan }}</h4>
                    <p>Pelanggan Bertransaksi <br><small>{{ $totalCustomerKeseluruhan }} pelanggan terdaftar</small></p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                    <p>Total Pengeluaran</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Grafik Penjualan</h3>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="80"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('load', function () {
            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249,115,22,0.1)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            ticks: {
                                callback: (value) => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>