<x-admin-layout title="Laporan Fee Teknisi">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 0.5rem;">
        <form method="GET" class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
    <div class="btn-group">
        @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
            <a href="{{ route('reports.technician-fee', array_filter(['period' => $key, 'technician_id' => $technicianId])) }}"
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

    <select name="technician_id" onchange="this.form.submit()" class="form-control" style="width: auto;">
        <option value="">Semua Teknisi</option>
        @foreach ($technicians as $tech)
            <option value="{{ $tech->id }}" @selected($technicianId == $tech->id)>{{ $tech->name }}</option>
        @endforeach
    </select>
</form>

        <div>
    <a href="{{ route('technician-manual-fees.create') }}" class="btn btn-primary mr-2"><i class="fas fa-plus"></i> Input Fee Manual</a>
    <a href="{{ route('reports.technician-fee.pdf', request()->query()) }}" target="_blank" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Export PDF</a>
</div>
    </div>

    <div class="text-muted small mb-2">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Teknisi</th>
                        <th class="d-none d-md-table-cell">Produk</th>
                        <th>Keterangan</th>
                        <th class="text-right" style="width: 260px;">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        @php
                            $updateUrl = $row->source === 'manual'
                                ? route('technician-manual-fees.update', $row->id)
                                : route('reports.technician-fee.update', $row->id);
                            $editPageUrl = $row->source === 'manual'
                                ? route('technician-manual-fees.edit', $row->id)
                                : route('reports.technician-fee.edit', $row->id);
                            $notesFieldName = $row->source === 'manual' ? 'notes' : 'fee_notes';
                            $isManual = $row->source === 'manual' || ($row->is_manual_case ?? false);
                            $isEmpty = $row->fee_amount == 0;
                        @endphp
                        <tr x-data="{ editing: {{ $isEmpty ? 'true' : 'false' }} }">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ $editPageUrl }}">{{ $row->technician->inisial ?? '-' }} - {{ $row->technician->name }}</a>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $row->product_name }}</td>

                            <!-- Mode baca -->
                            <td x-show="!editing" x-cloak>
                                {{ $row->notes ?? '-' }}
                                @if($isManual)
                                    <span class="badge badge-warning">Manual</span>
                                @endif
                            </td>
                            <td x-show="!editing" x-cloak class="text-right">
                                <span>Rp {{ number_format($row->fee_amount, 0, ',', '.') }}</span>
                                <button type="button" @click="editing = true" class="btn btn-link p-0 ml-2" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>

                            <!-- Mode edit inline -->
                            <td x-show="editing" x-cloak colspan="1">
                                <form method="POST" action="{{ $updateUrl }}" class="d-flex align-items-start">
                                    @csrf @method('PATCH')
                                    <div class="flex-grow-1">
                                        <input type="text" name="{{ $notesFieldName }}" value="{{ $row->notes }}" placeholder="Keterangan"
                                               class="form-control form-control-sm">
                                        @if($isManual)
                                            <span class="badge badge-warning mt-1">Manual</span>
                                        @endif
                                    </div>
                            </td>
                            <td x-show="editing" x-cloak class="text-right">
                                    <div class="d-flex align-items-center justify-content-end" style="gap: 0.25rem;">
                                        <input type="number" step="0.01" name="fee_amount" value="{{ $row->fee_amount }}" placeholder="Fee"
                                               required class="form-control form-control-sm text-right {{ $isEmpty ? 'border-warning' : '' }}" style="width: 100px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                        @if(!$isEmpty)
                                            <button type="button" @click="editing = false" class="btn btn-outline-secondary btn-sm">Batal</button>
                                        @endif
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data fee pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3 text-right font-weight-bold border-top">Total Fee: Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small">Menampilkan {{ $data->count() }} dari {{ $data->total() }} data</span>
        {{ $data->links() }}
    </div>
</x-admin-layout>