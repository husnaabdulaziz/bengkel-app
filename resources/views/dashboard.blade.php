<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        <!-- Filter Periode -->
        <div class="flex gap-2 mb-6">
            @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan (Sen-Min)', 'bulanan' => 'Bulanan'] as $key => $label)
                <a href="{{ route('dashboard', ['period' => $key]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium border {{ $period === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500 mb-1">Total Penjualan</div>
                <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500 mb-1">Total Laba</div>
                <div class="text-2xl font-bold {{ $totalLaba >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500 mb-1">Pelanggan Bertransaksi</div>
                <div class="text-2xl font-bold text-gray-800">{{ $totalPelanggan }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $totalCustomerKeseluruhan }} pelanggan terdaftar total</div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <div class="text-sm text-gray-500 mb-1">Total Pengeluaran</div>
                <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="font-semibold mb-4">Grafik Penjualan</h3>
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
</x-app-layout>