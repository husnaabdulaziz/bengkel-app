<x-admin-layout title="Pengeluaran">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 0.5rem;">
        <a href="{{ route('reports.financial') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Kembali ke Laporan Laba Rugi</a>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pengeluaran</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th class="d-none d-md-table-cell">Keterangan</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th class="text-right">Nominal</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $exp)
                        <tr>
                            <td>{{ $exp->expense_date->format('d/m/Y') }}</td>
                            <td>{{ $exp->category }}</td>
                            <td class="d-none d-md-table-cell">{{ $exp->description }}</td>
                            <td class="d-none d-md-table-cell">{{ $exp->branch?->nama_cabang }}</td>
                            <td class="text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td>
                                <form method="POST" action="{{ route('expenses.destroy', $exp) }}" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $expenses->links() }}
</x-admin-layout>