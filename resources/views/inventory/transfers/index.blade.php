<x-admin-layout title="Transfer Stock Antar Cabang">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Ajukan Transfer</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Tanggal Diajukan</th>
                        <th style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColor = [
                            'requested' => 'badge-warning',
                            'approved'  => 'badge-info',
                            'shipped'   => 'badge-primary',
                            'received'  => 'badge-success',
                            'rejected'  => 'badge-danger',
                        ];
                    @endphp
                    @forelse ($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->kode_transfer }}</td>
                            <td>{{ $transfer->fromBranch?->nama_cabang }}</td>
                            <td>{{ $transfer->toBranch?->nama_cabang }}</td>
                            <td><span class="badge {{ $statusColor[$transfer->status] ?? '' }}">{{ $transfer->status }}</span></td>
                            <td class="d-none d-md-table-cell">{{ $transfer->requested_at?->format('d/m/Y H:i') }}</td>
                            <td><a href="{{ route('stock-transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transfer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $transfers->links() }}
</x-admin-layout>