<x-admin-layout title="Fee Mekanik Manual">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 0.5rem;">
        <select onchange="window.location.href = this.value" class="form-control" style="width: auto;">
            <option value="{{ route('technician-manual-fees.index') }}">Semua Mekanik</option>
            @foreach ($technicians as $tech)
                <option value="{{ route('technician-manual-fees.index', ['technician_id' => $tech->id]) }}" @selected(request('technician_id') == $tech->id)>{{ $tech->name }}</option>
            @endforeach
        </select>
        <a href="{{ route('technician-manual-fees.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Input Fee Manual</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mekanik</th>
                        <th class="d-none d-md-table-cell">Produk</th>
                        <th>Keterangan</th>
                        <th class="text-right">Fee</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fees as $fee)
                        <tr>
                            <td>{{ $fee->transaction_date->format('d/m/Y') }}</td>
                            <td>{{ $fee->technician->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $fee->product?->nama ?? '-' }}</td>
                            <td>{{ $fee->notes }}</td>
                            <td class="text-right">Rp {{ number_format($fee->fee_amount, 0, ',', '.') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('technician-manual-fees.edit', $fee) }}" class="btn btn-outline-primary" title="Edit" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('technician-manual-fees.destroy', $fee) }}" class="d-inline" onsubmit="return confirm('Hapus data fee ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $fees->links() }}
</x-admin-layout>