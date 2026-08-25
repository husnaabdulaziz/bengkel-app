<x-admin-layout title="Laporan Penjualan Detail">

    <div style="max-width: 700px;" class="mx-auto">

        <form method="GET" class="d-flex flex-wrap align-items-center mb-3" style="gap: 0.5rem;">
            <div class="btn-group">
                @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                    <a href="{{ route('reports.financial.sales-detail', array_filter(['period' => $key, 'branch_id' => $branchId])) }}"
                       class="btn {{ $period === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <input type="date" name="start_date" value="{{ $period === 'custom' ? $start->format('Y-m-d') : '' }}" class="form-control" style="width: 150px;">
            <span>s/d</span>
            <input type="date" name="end_date" value="{{ $period === 'custom' ? $end->format('Y-m-d') : '' }}" class="form-control" style="width: 150px;">
            <input type="hidden" name="period" id="periodField" value="{{ $period }}">
            <button type="submit" onclick="document.getElementById('periodField').value = 'custom';" class="btn btn-outline-primary">Terapkan</button>

            @if ($branches->count() > 1)
                <select name="branch_id" onchange="this.form.submit()" class="form-control" style="width: auto;">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->nama_cabang }}</option>
                    @endforeach
                </select>
            @endif
        </form>

        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-1">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</p>
                <h2 class="mb-3">{{ $itemCount }} item transaksi ditemukan</h2>
                <p class="text-muted">File Excel akan berisi rincian per item produk: tanggal, invoice, pelanggan, produk, kategori, modal, harga jual, mekanik, dan fee.</p>
                <a href="{{ route('reports.financial.sales-detail-excel', request()->query()) }}" class="btn btn-success btn-lg">
                    <i class="fas fa-file-excel"></i> Download Excel Penjualan Detail
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>