<x-admin-layout title="Dashboard">

    <div x-data="dashboard(
            '{{ $period }}',
            '{{ $startDate }}', '{{ $endDate }}',
            {{ $totalPenjualan }}, {{ $totalLaba }}, {{ $totalPelanggan }},
            {{ $totalPengeluaran }}, {{ $totalCustomerKeseluruhan }},
            {{ json_encode($chartLabels) }}, {{ json_encode($chartData) }}
         )" x-init="initChart()">

        <!-- Filter Periode -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div class="btn-group">
                <button type="button" @click="load('harian')" class="btn" :class="{'btn-primary': period === 'harian', 'btn-outline-secondary': period !== 'harian'}">Harian</button>
                <button type="button" @click="load('mingguan')" class="btn" :class="{'btn-primary': period === 'mingguan', 'btn-outline-secondary': period !== 'mingguan'}">Mingguan (Sen-Min)</button>
                <button type="button" @click="load('bulanan')" class="btn" :class="{'btn-primary': period === 'bulanan', 'btn-outline-secondary': period !== 'bulanan'}">Bulanan</button>
            </div>

            <div class="d-flex align-items-center gap-2 ml-2">
                <input type="date" x-model="customStart" class="form-control form-control-sm" style="width: 150px;">
                <span>s/d</span>
                <input type="date" x-model="customEnd" class="form-control form-control-sm" style="width: 150px;">
                <button type="button" @click="load('custom')" class="btn btn-outline-primary btn-sm">Terapkan</button>
            </div>

            <span x-show="loading" class="text-muted ml-2"><i class="fas fa-spinner fa-spin"></i> Memuat...</span>
        </div>

        <!-- Info Boxes -->
        <div class="row d-flex align-items-stretch">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info h-100">
                    <div class="inner">
                        <h4 x-text="'Rp ' + totalPenjualan.toLocaleString('id-ID')"></h4>
                        <p>Total Penjualan</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success h-100">
                    <div class="inner">
                        <h4 x-text="'Rp ' + totalLaba.toLocaleString('id-ID')"></h4>
                        <p>Laba Bersih</p>
                    </div>
                    <div class="icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning h-100">
                    <div class="inner">
                        <h4 x-text="totalPelanggan"></h4>
                        <p>Pelanggan Bertransaksi <br><small x-text="totalCustomerKeseluruhan + ' pelanggan terdaftar'"></small></p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger h-100">
                    <div class="inner">
                        <h4 x-text="'Rp ' + totalPengeluaran.toLocaleString('id-ID')"></h4>
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
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function dashboard(initPeriod, initStart, initEnd, initPenjualan, initLaba, initPelanggan, initPengeluaran, initCustomerTotal, initLabels, initData) {
            return {
                period: initPeriod,
                customStart: initStart,
                customEnd: initEnd,
                totalPenjualan: initPenjualan,
                totalLaba: initLaba,
                totalPelanggan: initPelanggan,
                totalPengeluaran: initPengeluaran,
                totalCustomerKeseluruhan: initCustomerTotal,
                loading: false,
                chartInstance: null,

                initChart(labels = initLabels, data = initData) {
                        this.chartInstance = new Chart(document.getElementById('salesChart'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                label: 'Penjualan (Rp)',
                                data: data,
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249,115,22,0.1)',
                                tension: 0.3,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { ticks: { callback: (value) => 'Rp ' + value.toLocaleString('id-ID') } }
                            }
                        }
                    });
                },

                load(period) {
                            this.loading = true;
                            this.period = period;
                            const params = new URLSearchParams({ period });
                            if (period === 'custom') {
                                if (!this.customStart || !this.customEnd) { this.loading = false; return; }
                                params.set('start_date', this.customStart);
                                params.set('end_date', this.customEnd);
                            }

                            fetch(`{{ route('dashboard.data') }}?${params.toString()}`)
                                .then(r => {
                                    if (!r.ok) throw new Error('HTTP ' + r.status);
                                    return r.json();
                                })
                                .then(data => {
                                    this.totalPenjualan = parseFloat(data.totalPenjualan) || 0;
                                    this.totalLaba = parseFloat(data.totalLaba) || 0;
                                    this.totalPelanggan = parseInt(data.totalPelanggan) || 0;
                                    this.totalPengeluaran = parseFloat(data.totalPengeluaran) || 0;
                                    this.totalCustomerKeseluruhan = parseInt(data.totalCustomerKeseluruhan) || 0;

                                    if (this.chartInstance) {
                                        this.chartInstance.destroy();
                                    }
                                    this.initChart(data.chartLabels, data.chartData);

                                    this.loading = false;
                                })
                                .catch(err => {
                                    this.loading = false;
                                    alert('Gagal memuat data: ' + err.message);
                                    console.error(err);
                                });
                        },
            }
        }
    </script>
    @endpush
</x-admin-layout>