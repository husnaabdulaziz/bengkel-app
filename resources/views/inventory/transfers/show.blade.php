<x-admin-layout title="Detail Transfer">

    <div style="max-width: 800px;" class="mx-auto">
        <h5 class="mb-3">{{ $transfer->kode_transfer }}</h5>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><strong>Dari:</strong> {{ $transfer->fromBranch->nama_cabang }}</div>
                    <div class="col-6"><strong>Ke:</strong> {{ $transfer->toBranch->nama_cabang }}</div>
                    <div class="col-6"><strong>Status:</strong> {{ $transfer->status }}</div>
                    <div class="col-6"><strong>Diajukan:</strong> {{ $transfer->requested_at?->format('d/m/Y H:i') }}</div>
                </div>

                @if ($transfer->status === 'requested')
                    <form method="POST" action="{{ route('stock-transfers.approve', $transfer) }}">
                        @csrf
                        <table class="table table-sm mb-3">
                            <thead><tr><th>Produk</th><th class="text-right">Qty Diminta</th><th class="text-right">Qty Disetujui</th></tr></thead>
                            <tbody>
                                @foreach ($transfer->items as $item)
                                    <tr>
                                        <td>{{ $item->product->nama }}</td>
                                        <td class="text-right">{{ $item->qty_requested }}</td>
                                        <td class="text-right">
                                            <input type="number" name="qty_approved[{{ $item->id }}]" value="{{ $item->qty_requested }}" min="0"
                                                   class="form-control form-control-sm text-right" style="width: 90px; display: inline-block;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">Setujui Transfer</button>
                    </form>
                @endif

                @if ($transfer->status === 'approved')
                    <table class="table table-sm mb-3">
                        <thead><tr><th>Produk</th><th class="text-right">Qty Disetujui</th></tr></thead>
                        <tbody>
                            @foreach ($transfer->items as $item)
                                <tr><td>{{ $item->product->nama }}</td><td class="text-right">{{ $item->qty_approved }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <form method="POST" action="{{ route('stock-transfers.ship', $transfer) }}"
                          onsubmit="return confirm('Kirim barang sekarang? Stock cabang asal akan berkurang.')">
                        @csrf
                        <button type="submit" class="btn btn-primary">Tandai Sudah Dikirim</button>
                    </form>
                @endif

                @if ($transfer->status === 'shipped')
                    <table class="table table-sm mb-3">
                        <thead><tr><th>Produk</th><th class="text-right">Qty Dikirim</th></tr></thead>
                        <tbody>
                            @foreach ($transfer->items as $item)
                                <tr><td>{{ $item->product->nama }}</td><td class="text-right">{{ $item->qty_shipped }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <form method="POST" action="{{ route('stock-transfers.receive', $transfer) }}"
                          onsubmit="return confirm('Konfirmasi barang sudah diterima? Stock cabang tujuan akan bertambah.')">
                        @csrf
                        <button type="submit" class="btn btn-success">Konfirmasi Diterima</button>
                    </form>
                @endif

                @if ($transfer->status === 'received')
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Produk</th><th class="text-right">Qty Diterima</th></tr></thead>
                        <tbody>
                            @foreach ($transfer->items as $item)
                                <tr><td>{{ $item->product->nama }}</td><td class="text-right">{{ $item->qty_received }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-success font-weight-bold mt-3 mb-0">Transfer selesai, stock kedua cabang sudah ter-update.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>