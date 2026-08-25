<x-admin-layout title="Riwayat Kas Harian">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('cash-closings.today') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Kembali ke Kas Hari Ini</a>
        @if ($branches->count() > 1)
            <form method="GET">
                <select name="branch_id" onchange="this.form.submit()" class="form-control">
                    <option value="">Semua Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->nama_cabang }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th class="text-right">Seharusnya</th>
                        <th class="text-right">Real</th>
                        <th class="text-right">Selisih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closings as $closing)
                        <tr>
                            <td>{{ $closing->closing_date->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ $closing->branch->nama_cabang }}</td>
                            <td class="text-right">Rp {{ number_format($closing->expected_balance, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $closing->actual_balance !== null ? 'Rp ' . number_format($closing->actual_balance, 0, ',', '.') : '-' }}</td>
                            <td class="text-right {{ $closing->difference > 0 ? 'text-info' : ($closing->difference < 0 ? 'text-danger' : 'text-success') }}">
                                {{ $closing->difference !== null ? ($closing->difference > 0 ? '+' : '') . number_format($closing->difference, 0, ',', '.') : '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $closing->status === 'closed' ? 'badge-secondary' : 'badge-warning' }}">
                                    {{ $closing->status === 'closed' ? 'Ditutup' : 'Berjalan' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $closings->links() }}
</x-admin-layout>