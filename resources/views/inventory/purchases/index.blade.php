<x-admin-layout title="Pembelian dari Vendor">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ $purchase->supplier?->nama }}</td>
                            <td class="d-none d-md-table-cell">{{ $purchase->branch?->nama_cabang }}</td>
                            <td class="text-right">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge badge-success">{{ $purchase->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi pembelian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $purchases->links() }}
</x-admin-layout>