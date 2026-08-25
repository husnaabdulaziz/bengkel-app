<x-admin-layout title="Kas Harian">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ now()->translatedFormat('l, d F Y') }}</h5>
            @if ($branches->count() > 1)
                <form method="GET">
                    <select name="branch_id" onchange="this.form.submit()" class="form-control">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        @if (!$closing)
            <!-- Belum buka kas hari ini -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Buka Kas Hari Ini</h3></div>
                <div class="card-body">
                    <p class="text-muted">Masukkan jumlah "Uang Kecil" yang Anda siapkan pagi ini untuk kembalian.</p>
                    <form method="POST" action="{{ route('cash-closings.open') }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        <div class="form-group">
                            <label>Saldo Awal / Uang Kecil</label>
                            <input type="number" step="0.01" name="opening_balance" required min="0" class="form-control" style="max-width: 300px;">
                        </div>
                        <button type="submit" class="btn btn-primary">Buka Kas</button>
                    </form>
                </div>
            </div>
        @elseif ($closing->status === 'open')
            <!-- Kas sudah dibuka, belum ditutup -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Status Kas Berjalan</h3></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Saldo Awal (Uang Kecil)</td><td class="text-right">Rp {{ number_format($closing->opening_balance, 0, ',', '.') }}</td></tr>
                        <tr><td>+ Penjualan Tunai Hari Ini</td><td class="text-right text-success">Rp {{ number_format($closing->cash_sales, 0, ',', '.') }}</td></tr>
                        <tr><td>- Pengeluaran Hari Ini</td><td class="text-right text-danger">Rp {{ number_format($closing->cash_expenses, 0, ',', '.') }}</td></tr>
                        <tr class="border-top"><td><strong>Seharusnya Ada di Laci</strong></td><td class="text-right"><strong>Rp {{ number_format($closing->expected_balance, 0, ',', '.') }}</strong></td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Tutup Kas</h3></div>
                <div class="card-body">
                    <p class="text-muted">Hitung uang fisik di laci sekarang, masukkan totalnya di bawah ini.</p>
                    <form method="POST" action="{{ route('cash-closings.close', $closing) }}">
                        @csrf
                        <div class="form-group">
                            <label>Jumlah Uang Real di Laci</label>
                            <input type="number" step="0.01" name="actual_balance" required min="0" class="form-control" style="max-width: 300px;">
                        </div>
                        <div class="form-group">
                            <label>Catatan (opsional)</label>
                            <textarea name="notes" rows="2" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Tutup Kas & Hitung Selisih</button>
                    </form>
                </div>
            </div>
        @else
            <!-- Sudah ditutup -->
            @php
                $diff = $closing->difference;
                $diffColor = $diff == 0 ? 'success' : ($diff > 0 ? 'info' : 'danger');
                $diffLabel = $diff == 0 ? 'Pas, tidak ada selisih' : ($diff > 0 ? 'Lebih' : 'Kurang');
            @endphp
            <div class="card">
                <div class="card-header"><h3 class="card-title">Kas Hari Ini Sudah Ditutup</h3></div>
                <div class="card-body">
                    <table class="table table-borderless mb-3">
                        <tr><td>Saldo Awal (Uang Kecil)</td><td class="text-right">Rp {{ number_format($closing->opening_balance, 0, ',', '.') }}</td></tr>
                        <tr><td>+ Penjualan Tunai</td><td class="text-right text-success">Rp {{ number_format($closing->cash_sales, 0, ',', '.') }}</td></tr>
                        <tr><td>- Pengeluaran</td><td class="text-right text-danger">Rp {{ number_format($closing->cash_expenses, 0, ',', '.') }}</td></tr>
                        <tr class="border-top"><td>Seharusnya Ada</td><td class="text-right">Rp {{ number_format($closing->expected_balance, 0, ',', '.') }}</td></tr>
                        <tr><td>Uang Real Dihitung</td><td class="text-right">Rp {{ number_format($closing->actual_balance, 0, ',', '.') }}</td></tr>
                    </table>

                    <div class="alert alert-{{ $diffColor }} text-center">
                        <h4 class="mb-0">{{ $diffLabel }}</h4>
                        @if ($diff != 0)
                            <p class="mb-0">Rp {{ number_format(abs($diff), 0, ',', '.') }}</p>
                        @endif
                    </div>

                    @if ($closing->notes)
                        <p class="text-muted"><strong>Catatan:</strong> {{ $closing->notes }}</p>
                    @endif

                    <p class="text-muted small mb-0">Ditutup oleh {{ $closing->closedBy?->name }} pada {{ $closing->closed_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @endif

        <div class="text-center mt-3">
            <a href="{{ route('cash-closings.index') }}" class="text-muted">Lihat Riwayat Kas Harian</a>
        </div>
    </div>
</x-admin-layout>