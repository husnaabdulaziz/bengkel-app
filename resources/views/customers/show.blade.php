<x-admin-layout title="Detail Pelanggan">

    <div style="max-width: 700px;" class="mx-auto">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $customer->nama }}</h3>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><strong>Telpon:</strong> {{ $customer->telpon ?? '-' }}</div>
                    <div class="col-md-6"><strong>Plat Nomor:</strong> {{ $customer->plat_nomor ?? '-' }}</div>
                    <div class="col-md-6"><strong>Kendaraan:</strong> {{ $customer->jenis_kendaraan }} {{ $customer->merk_kendaraan }} {{ $customer->model_kendaraan }}</div>
                    <div class="col-md-6"><strong>Terakhir Kunjungan:</strong> {{ $customer->last_visit_at?->format('d/m/Y') ?? 'Belum pernah' }}</div>
                    <div class="col-12"><strong>Alamat:</strong> {{ $customer->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Riwayat Transaksi</h3></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Tanggal</th><th>No. Invoice</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->workOrders as $wo)
                            <tr>
                                <td>{{ $wo->paid_at?->format('d/m/Y') }}</td>
                                <td><a href="{{ route('pos.invoice', $wo) }}" target="_blank">{{ $wo->invoice_number }}</a></td>
                                <td class="text-right">Rp {{ number_format($wo->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada riwayat transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('customers.index') }}" class="text-muted">← Kembali ke Daftar Pelanggan</a>
    </div>
</x-admin-layout>
