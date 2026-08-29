<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { margin-bottom: 2px; }
        .meta { margin-bottom: 16px; color: #444; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 16px; font-weight: bold; text-align: right; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 4px; font-weight: bold; font-size: 11px; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <table style="border: none; margin-bottom: 10px;">
        <tr>
            <td style="border: none; width: 75%; vertical-align: top; padding: 0;">
                <h2>Purchase Order (PO)</h2>
                <div class="meta">
                    <strong>{{ $company->nama_toko ?? '-' }}</strong><br>
                    {{ $company->alamat_toko ?? '-' }}<br>
                    {{ $company->telpon ?? '-' }}
                </div>
            </td>
            <td style="border: none; width: 25%; text-align: right; vertical-align: top; padding: 0;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-width: 100px; max-height: 100px;">
                @endif
            </td>
        </tr>
    </table>

    <table style="border: none; margin-bottom: 16px;">
    <tr>
        <td style="border: none; width: 50%; padding: 0;">
            <strong>Vendor:</strong> {{ $purchase->supplier->nama }}<br>
            <strong>Ditujukan Untuk:</strong> {{ $company->nama_toko ?? '-' }}<br>
            <strong>Tanggal:</strong> {{ $purchase->purchase_date->format('d/m/Y') }}
        </td>
        <td style="border: none; width: 50%; padding: 0; text-align: right;">
            <div><strong>No. Invoice:</strong> {{ $purchase->invoice_number ?? '(belum ada)' }}</div>
            <div style="margin-top: 4px;">
                <span class="status {{ $purchase->status === 'pending' ? 'status-pending' : 'status-completed' }}">
                    {{ $purchase->status === 'pending' ? 'MENUNGGU BARANG' : 'SELESAI' }}
                </span>
            </div>
        </td>
    </tr>
</table>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga/Unit</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->product->nama }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">Total: Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
</body>
</html>