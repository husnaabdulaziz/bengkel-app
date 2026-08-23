<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $workOrder->invoice_number }}</title>
    <style>
        body { font-family: monospace; font-size: 13px; max-width: 400px; margin: 20px auto; color: #111; }
        .center { text-align: center; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td, th { padding: 2px 0; }
        hr { border: none; border-top: 1px dashed #999; }
        .no-print { margin-top: 20px; text-align: center; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="center">
        <strong>{{ $workOrder->branch->company->nama_toko }}</strong><br>
        {{ $workOrder->branch->nama_cabang }}<br>
        {{ $workOrder->branch->alamat }}
    </div>
    <hr>
    <div>
        No. Invoice: {{ $workOrder->invoice_number }}<br>
        Tanggal: {{ $workOrder->paid_at->format('d/m/Y H:i') }}<br>
        Pelanggan: {{ $workOrder->customer->nama }}<br>
        @if ($workOrder->customer->plat_nomor) Plat: {{ $workOrder->customer->plat_nomor }}<br> @endif
    </div>
    <hr>
    <table>
        @foreach ($workOrder->items as $item)
            <tr>
                <td colspan="2">{{ $item->item_name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <hr>
    <table>
        <tr><td>Subtotal</td><td class="right">{{ number_format($workOrder->subtotal, 0, ',', '.') }}</td></tr>
        @if ($workOrder->discount_value > 0)
            <tr><td>Diskon</td><td class="right">-{{ number_format($workOrder->subtotal - $workOrder->total_amount, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td><strong>Total</strong></td><td class="right"><strong>{{ number_format($workOrder->total_amount, 0, ',', '.') }}</strong></td></tr>
        <tr><td>Bayar ({{ ucfirst($workOrder->payment_method) }})</td><td class="right">Lunas</td></tr>
    </table>
    <hr>
    <div class="center">Terima kasih atas kunjungan Anda</div>

    <div class="no-print">
        <button onclick="window.print()">Cetak Invoice</button>
    </div>
</body>
<!-- autoprint -->
<script>
    window.onload = function () {
        window.print();
    };
</script>
</html>