<x-admin-layout title="Rincian Uang Masuk Kamar">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('cash-closings.index') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Kembali ke Riwayat Kas Harian</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                @if ($branches->count() > 1)
                    <div class="form-group col-md-3">
                        <label>Cabang</label>
                        <select name="branch_id" class="form-control">
                            <option value="all" @selected($branchId == 'all')>Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rincian Uang Masuk Kamar per Pecahan</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        @if ($branches->count() > 1)
                            <th>Cabang</th>
                        @endif
                        @foreach ($denominations as $denom)
                            <th class="text-right">Rp {{ number_format($denom, 0, ',', '.') }}</th>
                        @endforeach
                        <th class="text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($row['closing_date'])->format('d/m/Y') }}</td>
                            @if ($branches->count() > 1)
                                <td>{{ $row['branch'] }}</td>
                            @endif
                            @foreach ($denominations as $denom)
                                <td class="text-right">{{ $row['columns'][$denom] }}</td>
                            @endforeach
                            <td class="text-right font-weight-bold">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($denominations) + ($branches->count() > 1 ? 3 : 2) }}" class="text-center text-muted py-4">
                                Tidak ada data pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($rows->isNotEmpty())
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td>Total Lembar</td>
                            @if ($branches->count() > 1)
                                <td></td>
                            @endif
                            @foreach ($denominations as $denom)
                                <td class="text-right">{{ $grandTotals[$denom] }}</td>
                            @endforeach
                            <td class="text-right">Rp {{ number_format($grandTotalAmount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</x-admin-layout>