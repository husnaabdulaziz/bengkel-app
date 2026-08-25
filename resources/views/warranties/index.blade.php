<x-admin-layout title="Garansi">

    <div class="input-group mb-3" style="max-width: 500px;">
        <form method="GET" class="w-100 d-flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, telpon, atau plat nomor pelanggan..." class="form-control">
            <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode Garansi</th>
                        <th>Pelanggan</th>
                        <th class="d-none d-md-table-cell">Produk</th>
                        <th class="d-none d-md-table-cell">Berlaku Sampai</th>
                        <th>Status</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warranties as $w)
                        @php
                            $badgeColor = ['active' => 'badge-success', 'claimed' => 'badge-info', 'expired' => 'badge-secondary'][$w->display_status];
                            $badgeLabel = ['active' => 'Aktif', 'claimed' => 'Sudah Diklaim', 'expired' => 'Kadaluarsa'][$w->display_status];
                        @endphp
                        <tr>
                            <td>{{ $w->kode_garansi }}</td>
                            <td>{{ $w->customer->nama }} <br><small class="text-muted">{{ $w->customer->plat_nomor }}</small></td>
                            <td class="d-none d-md-table-cell">{{ $w->product->nama }}</td>
                            <td class="d-none d-md-table-cell">{{ $w->warranty_end_date->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $badgeColor }}">{{ $badgeLabel }}</span></td>
                            <td><a href="{{ route('warranties.show', $w) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data garansi ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $warranties->links() }}
</x-admin-layout>