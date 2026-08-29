<x-admin-layout title="Terima Barang">

    <div style="max-width: 600px;" class="mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="card-title">Detail PO — {{ $purchase->supplier->nama }}</h3></div>
            <div class="card-body p-0">
                

            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-right">Qty</th>
                        <th style="width: 150px;">Harga Beli/Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->nama }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td>
                                <input type="number" step="0.01" name="prices[{{ $item->id }}]" value="{{ $item->price_per_unit }}" min="0" form="receive-form" class="form-control form-control-sm text-right">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-muted small px-3">Sesuaikan harga kalau ternyata beda dari invoice asli vendor. Harga Modal produk akan otomatis ter-update ke harga terbaru ini.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('purchases.receive', $purchase) }}" id="receive-form" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>No. Invoice dari Vendor (opsional)</label>
                    <input type="text" name="invoice_number" class="form-control" placeholder="Kosongkan kalau vendor tidak kasih nomor invoice">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success" onclick="return confirm('Konfirmasi barang sudah diterima? Stock akan otomatis bertambah.')">Konfirmasi Terima Barang</button>
            </div>
        </form>
    </div>
</x-admin-layout>