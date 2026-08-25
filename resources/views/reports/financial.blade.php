<x-admin-layout title="Laporan Laba Rugi">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="d-flex flex-wrap align-items-center mb-3" style="gap: 0.5rem;">
        <div class="btn-group">
            @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                <a href="{{ route('reports.financial', array_filter(['period' => $key, 'branch_id' => $branchId])) }}"
                   class="btn {{ $period === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <input type="date" name="start_date" value="{{ $period === 'custom' ? $start->format('Y-m-d') : '' }}" class="form-control" style="width: 150px;">
        <span>s/d</span>
        <input type="date" name="end_date" value="{{ $period === 'custom' ? $end->format('Y-m-d') : '' }}" class="form-control" style="width: 150px;">
        <input type="hidden" name="period" id="periodField" value="{{ $period }}">
        <button type="submit" onclick="document.getElementById('periodField').value = 'custom';" class="btn btn-outline-primary">Terapkan</button>

        @if ($branches->count() > 1)
            <select name="branch_id" onchange="this.form.submit()" class="form-control" style="width: auto;">
                <option value="">Semua Cabang</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->nama_cabang }}</option>
                @endforeach
            </select>
        @endif

        <div class="ml-auto">
            <a href="{{ route('expenses.create') }}" class="btn btn-warning"><i class="fas fa-plus"></i> Tambah Pengeluaran</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-list"></i> Kelola Pengeluaran</a>
            <a href="{{ route('reports.financial.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger" title="Download Summary (PDF)">PDF<i class="fas fa-download"></i></a>
            <a href="{{ route('reports.financial.excel', request()->query()) }}" class="btn btn-secondary" title="Download Summary (Excel)">Summary<i class="fas fa-download"></i></a>
            <a href="{{ route('reports.financial.sales-detail-excel', request()->query()) }}" class="btn btn-success" title="Download Detail"> Penjualan Detail<i class="fas fa-download"></i></a>
            
        </div>
    </form>

    <div class="text-muted small mb-2">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</div>

    <!-- Ringkasan -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                    <p>Total Penjualan</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalModal, 0, ',', '.') }}</h4>
                    <p>Total Modal (HPP)</p>
                </div>
                <div class="icon"><i class="fas fa-box"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalFee, 0, ',', '.') }}</h4>
                    <p>Total Fee Mekanik</p>
                </div>
                <div class="icon"><i class="fas fa-user-cog"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($labaKotor, 0, ',', '.') }}</h4>
                    <p>Laba Kotor</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-6">
            <div class="small-box bg-danger h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                    <p>Total Pengeluaran</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
        <div class="col-lg-6 col-6">
            <div class="small-box {{ $labaBersih >= 0 ? 'bg-success' : 'bg-danger' }} h-100">
                <div class="inner">
                    <h4>Rp {{ number_format($labaBersih, 0, ',', '.') }}</h4>
                    <p>Laba Bersih</p>
                </div>
                <div class="icon"><i class="fas fa-piggy-bank"></i></div>
            </div>
        </div>
    </div>

    <!-- Rincian Pengeluaran -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Rincian Pengeluaran Periode Ini</h3>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua Pengeluaran</a>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th class="d-none d-md-table-cell">Keterangan</th>
                        <th class="d-none d-md-table-cell">Cabang</th>
                        <th class="text-right">Nominal</th>
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
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada pengeluaran pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>