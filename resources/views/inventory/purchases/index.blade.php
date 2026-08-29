<x-admin-layout title="Pembelian dari Vendor">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
        <a href="{{ route('purchases.create-po') }}" class="btn btn-outline-primary"><i class="fas fa-file-invoice"></i> Buat PO dari Stock Menipis</a>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat Pembelian</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th class="d-none d-md-table-cell">Vendor</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->invoice_number ?? '-' }}</td>
                            <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ $purchase->supplier?->nama }}</td>
                            <td class="d-none d-md-table-cell">{{ $purchase->branch?->nama_cabang }}</td>
                            <td class="text-right">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if ($purchase->status === 'pending')
                                    <span class="badge badge-warning">Menunggu Barang</span>
                                @else
                                    <span class="badge badge-success">Selesai</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if ($purchase->status === 'pending')
                                    <a href="{{ route('purchases.receive.show', $purchase) }}" class="btn btn-sm btn-outline-success">Terima Barang</a>
                                @endif
                                <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi pembelian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $purchases->links() }}
</x-admin-layout>