<x-admin-layout title="Stock Opname">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Opname Baru</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th>Status</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($opnames as $opname)
                        <tr>
                            <td>{{ $opname->kode_opname }}</td>
                            <td>{{ $opname->opname_date->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ $opname->branch?->nama_cabang }}</td>
                            <td>
                                <span class="badge {{ $opname->status === 'completed' ? 'badge-success' : 'badge-warning' }}">{{ $opname->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('stock-opnames.edit', $opname) }}"
                                class="btn text-nowrap {{ $opname->status === 'draft' ? 'btn-warning text-dark' : 'btn-outline-primary' }}"
                                style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                                    {{ $opname->status === 'draft' ? 'Lanjutkan' : 'Lihat Detail' }}
                                </a>

                                @can('delete_stock_opname')
                                <form method="POST" action="{{ route('stock-opnames.destroy', $opname) }}" class="d-inline" onsubmit="return confirm('Hapus riwayat opname ini? Stock yang sudah disesuaikan TIDAK ikut dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                            
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $opnames->links() }}
</x-admin-layout>