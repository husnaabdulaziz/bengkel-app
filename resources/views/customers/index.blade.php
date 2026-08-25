<x-admin-layout title="Manajemen Pelanggan">

    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap: 0.5rem;">
        <form method="GET" class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, telpon, atau plat nomor..." class="form-control" style="width: 250px;">

            <div class="btn-group">
                @foreach (['semua' => 'Semua', '3bulan' => '>3 Bulan', '6bulan' => '>6 Bulan', '1tahun' => '>1 Tahun', '2tahun' => '>2 Tahun'] as $key => $label)
                    <button type="submit" name="filter" value="{{ $key }}" class="btn {{ ($filter ?? 'semua') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <span>atau belum kembali sejak:</span>
            <input type="date" name="cutoff_date" value="{{ request('cutoff_date') }}" class="form-control" style="width: 160px;">
            <button type="submit" name="filter" value="custom" class="btn btn-outline-primary">Terapkan</button>
        </form>
        <a href="{{ route('customers.export', request()->query()) }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Download Excel</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Telpon</th>
                        <th class="d-none d-md-table-cell">Plat Nomor</th>
                        <th>Terakhir Kunjungan</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $c)
                        <tr>
                            <td>{{ $c->nama }}</td>
                            <td class="d-none d-md-table-cell">{{ $c->telpon }}</td>
                            <td class="d-none d-md-table-cell">{{ $c->plat_nomor }}</td>
                            <td>
                                @if ($c->last_visit_at)
                                    {{ $c->last_visit_at->format('d/m/Y') }}
                                    <span class="text-muted small">({{ $c->last_visit_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-muted">Belum pernah</span>
                                @endif
                            </td>
                            <td><a href="{{ route('customers.show', $c) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada pelanggan yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $customers->links() }}
</x-admin-layout>