<x-admin-layout title="Riwayat Kas Harian">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('cash-closings.today') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Kembali ke Kas Hari Ini</a>
        <div class="d-flex align-items-center">
            <a href="{{ route('cash-closings.kamar-report') }}" class="btn btn-outline-primary btn-sm mr-2">
                <i class="fas fa-vault"></i> Lihat Rincian Uang Masuk Kamar
            </a>
            @if ($branches->count() > 1)
                <form method="GET" class="mb-0">
                    <select name="branch_id" onchange="this.form.submit()" class="form-control">
                        <option value="">Semua Cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
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
                        <th class="text-right d-none d-md-table-cell">Petty Cash</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closings as $closing)
                        @php
                            $reservedAmount = $closing->denominations->sum(fn($d) => $d->reserved_for_next_day * $d->denomination);
                            $kamarAmount = $closing->actual_balance !== null ? $closing->actual_balance - $reservedAmount : null;
                        @endphp
                        <tr>
                            <td>{{ $closing->closing_date->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">{{ $closing->branch->nama_cabang }}</td>
                            <td class="text-right">Rp {{ number_format($closing->expected_balance, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $closing->actual_balance !== null ? 'Rp ' . number_format($closing->actual_balance, 0, ',', '.') : '-' }}</td>
                            <td class="text-right {{ $closing->difference > 0 ? 'text-info' : ($closing->difference < 0 ? 'text-danger' : 'text-success') }}">
                                {{ $closing->difference !== null ? ($closing->difference > 0 ? '+' : '') . number_format($closing->difference, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right d-none d-md-table-cell text-muted">
                                Rp {{ number_format($closing->opening_balance, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge {{ $closing->status === 'closed' ? 'badge-secondary' : 'badge-warning' }}">
                                    {{ $closing->status === 'closed' ? 'Ditutup' : 'Berjalan' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $closings->links() }}
</x-admin-layout>