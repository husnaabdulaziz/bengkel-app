<x-admin-layout title="Stock Menipis">

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('purchases.create-po') }}" class="btn btn-primary"><i class="fas fa-file-invoice"></i> Buat PO ke Vendor</a>
</div>
    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="d-none d-md-table-cell">Kategori</th>
                        <th class="d-none d-md-table-cell">Brand</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th class="text-right">Stock Sekarang</th>
                        <th class="text-right">Minimum Stock</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStockItems as $item)
                        <tr class="{{ $item->stock_qty <= 0 ? 'table-danger' : 'table-warning' }}">
                            <td>{{ $item->product->nama }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->product->category?->nama ?? '-' }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->product->brand?->nama ?? '-' }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->branch->nama_cabang }}</td>
                            <td class="text-right font-weight-bold">{{ $item->stock_qty }}</td>
                            <td class="text-right">{{ $item->product->minimum_stock }}</td>
                            <td>
                                @if ($item->stock_qty <= 0)
                                    <span class="badge badge-danger">Habis</span>
                                @else
                                    <span class="badge badge-warning">Menipis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Semua stock produk masih aman, tidak ada yang menipis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>