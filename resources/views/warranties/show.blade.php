<x-admin-layout title="Detail Garansi">

    <div style="max-width: 700px;" class="mx-auto">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @php
            $badgeColor = ['active' => 'badge-success', 'claimed' => 'badge-info', 'expired' => 'badge-secondary'][$warranty->display_status];
            $badgeLabel = ['active' => 'Aktif', 'claimed' => 'Sudah Diklaim', 'expired' => 'Kadaluarsa'][$warranty->display_status];
        @endphp

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $warranty->kode_garansi }}</h3>
                <span class="badge {{ $badgeColor }}">{{ $badgeLabel }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><strong>Pelanggan:</strong> {{ $warranty->customer->nama }}</div>
                    <div class="col-md-6"><strong>Telpon:</strong> {{ $warranty->customer->telpon }}</div>
                    <div class="col-md-6"><strong>Plat Nomor:</strong> {{ $warranty->customer->plat_nomor }}</div>
                    <div class="col-md-6"><strong>Produk:</strong> {{ $warranty->product->nama }}</div>
                    <div class="col-md-6"><strong>Mulai:</strong> {{ $warranty->warranty_start_date->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>Berlaku Sampai:</strong> {{ $warranty->warranty_end_date->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>No. Invoice:</strong> {{ $warranty->workOrderItem->workOrder->invoice_number ?? '-' }}</div>
                </div>
            </div>
        </div>

        @if ($warranty->claims->isNotEmpty())
            <div class="card">
                <div class="card-header"><h3 class="card-title">Riwayat Klaim</h3></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Tanggal</th><th>Catatan</th><th>Oleh</th></tr></thead>
                        <tbody>
                            @foreach ($warranty->claims as $claim)
                                <tr>
                                    <td>{{ $claim->claim_date->format('d/m/Y') }}</td>
                                    <td>{{ $claim->notes }}</td>
                                    <td>{{ $claim->createdBy?->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($warranty->display_status !== 'expired')
            <div class="card">
                <div class="card-header"><h3 class="card-title">Ajukan Klaim Garansi</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('warranties.claim', $warranty) }}">
                        @csrf
                        <div class="form-group">
                            <label>Tanggal Klaim</label>
                            <input type="date" name="claim_date" value="{{ date('Y-m-d') }}" required class="form-control" style="max-width: 250px;">
                        </div>
                        <div class="form-group">
                            <label>Catatan / Keluhan</label>
                            <textarea name="notes" rows="3" required class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Catat klaim garansi ini?')">Catat Klaim</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>