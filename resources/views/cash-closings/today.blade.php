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

            <div class="card" x-data="{
                denoms: { 1000: 0, 2000: 0, 5000: 0, 10000: 0, 20000: 0, 50000: 0, 100000: 0 },
                reserved: { 1000: 0, 2000: 0, 5000: 0, 10000: 0, 20000: 0, 50000: 0, 100000: 0 },
                get total() {
                    return Object.keys(this.denoms).reduce((sum, d) => sum + (d * (parseInt(this.denoms[d]) || 0)), 0);
                },
                get totalReserved() {
                    return Object.keys(this.reserved).reduce((sum, d) => sum + (d * (parseInt(this.reserved[d]) || 0)), 0);
                },
                get totalKamar() {
                    return this.total - this.totalReserved;
                },
                kamarCount(denom) {
                    const c = parseInt(this.denoms[denom]) || 0;
                    const r = parseInt(this.reserved[denom]) || 0;
                    return Math.max(c - r, 0);
                }
            }">
                <div class="card-header"><h3 class="card-title">Tutup Kas</h3></div>
                <div class="card-body">
                    <p class="text-muted">Hitung jumlah lembar uang fisik di laci per pecahan. Isi juga berapa lembar yang akan disisihkan sebagai uang kecil untuk besok — sisanya otomatis dihitung sebagai yang masuk ke kamar.</p>
                    <form method="POST" action="{{ route('cash-closings.close', $closing) }}">
                        @csrf

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pecahan</th>
                                    <th style="width: 100px;">Jumlah Lembar</th>
                                    <th style="width: 100px;">Untuk Besok</th>
                                    <th style="width: 80px;" class="text-center">Ke Kamar</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="denom in [1000, 2000, 5000, 10000, 20000, 50000, 100000]" :key="denom">
                                    <tr>
                                        <td x-text="'Rp ' + denom.toLocaleString('id-ID')"></td>
                                        <td>
                                            <input type="number" :name="`denominations[${denom}]`" x-model.number="denoms[denom]" min="0" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="number" :name="`reserved[${denom}]`" x-model.number="reserved[denom]" min="0" :max="denoms[denom]" class="form-control form-control-sm">
                                        </td>
                                        <td class="text-center text-muted" x-text="kamarCount(denom)"></td>
                                        <td class="text-right" x-text="'Rp ' + (denom * (parseInt(denoms[denom]) || 0)).toLocaleString('id-ID')"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="text-muted">
                                    <td colspan="3" class="text-right">Total Untuk Besok (Petty Cash):</td>
                                    <td></td>
                                    <td class="text-right" x-text="'Rp ' + totalReserved.toLocaleString('id-ID')"></td>
                                </tr>
                                <tr class="text-muted">
                                    <td colspan="3" class="text-right">Total Ke Kamar:</td>
                                    <td></td>
                                    <td class="text-right" x-text="'Rp ' + totalKamar.toLocaleString('id-ID')"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total Uang Real:</td>
                                    <td class="text-right" x-text="'Rp ' + total.toLocaleString('id-ID')"></td>
                                </tr>
                            </tfoot>
                        </table>

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
                $totalReserved = $closing->denominations->sum('reserved_for_next_day');
                $totalReservedAmount = $closing->denominations->sum(fn($d) => $d->reserved_for_next_day * $d->denomination);
                $totalKamarAmount = $closing->actual_balance - $totalReservedAmount;
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
                        <tr><td>Untuk Besok (Petty Cash)</td><td class="text-right">Rp {{ number_format($totalReservedAmount, 0, ',', '.') }}</td></tr>
                        <tr><td><strong>Masuk ke Kamar</strong></td><td class="text-right"><strong>Rp {{ number_format($totalKamarAmount, 0, ',', '.') }}</strong></td></tr>
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
                    @if ($closing->denominations->isNotEmpty())
                        <table class="table table-sm mb-3">
                            <thead>
                                <tr>
                                    <th>Pecahan</th>
                                    <th class="text-right">Lembar</th>
                                    <th class="text-right">Untuk Besok</th>
                                    <th class="text-right">Ke Kamar</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($closing->denominations->sortByDesc('denomination') as $d)
                                    <tr>
                                        <td>Rp {{ number_format($d->denomination, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ $d->count }}</td>
                                        <td class="text-right">{{ $d->reserved_for_next_day }}</td>
                                        <td class="text-right">{{ $d->kamar_count }}</td>
                                        <td class="text-right">Rp {{ number_format($d->denomination * $d->count, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td class="text-right" colspan="2">Total:</td>
                                    <td class="text-right">{{ $totalReserved }}</td>
                                    <td class="text-right">{{ $closing->denominations->sum(fn($d) => $d->kamar_count) }}</td>
                                    <td class="text-right">Rp {{ number_format($closing->actual_balance, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                    <p class="text-muted small mb-0">Ditutup oleh {{ $closing->closedBy?->name }} pada {{ $closing->closed_at?->format('d/m/Y H:i') }}</p>

                    <form method="POST" action="{{ route('cash-closings.reopen', $closing) }}" class="mt-3" onsubmit="return confirm('Buka kembali kas ini? Data penutupan sebelumnya akan direset dan Anda perlu tutup kas ulang.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm">Buka Kembali Kas</button>
                    </form>
                </div>
            </div>
        @endif

        <div class="text-center mt-3">
            <a href="{{ route('cash-closings.index') }}" class="text-muted">Lihat Riwayat Kas Harian</a>
        </div>
    </div>
</x-admin-layout>