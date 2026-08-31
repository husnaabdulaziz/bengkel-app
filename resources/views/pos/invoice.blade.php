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
        {{ $workOrder->branch->company->alamat_toko }}<br>
        Telp: {{ $workOrder->branch->company->telpon }}
    </div>
    <hr>
    <div>
        @php
            $technicianLabels = $workOrder->assignedTechnicians()->map(function ($tech) {
                return $tech->name . '(' . ($tech->inisial ?: '-') . ')';
            })->implode(', ');
        @endphp
        <table style="width: 100%; border: none;">
            <tr><td style="border: none; padding: 0; white-space: nowrap; width: 65px;">No. Invoice</td><td style="border: none; padding: 0;">: {{ $workOrder->invoice_number }}</td></tr>
            <tr><td style="border: none; padding: 0; white-space: nowrap; width: 65px;">Tanggal</td><td style="border: none; padding: 0;">: {{ $workOrder->paid_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td style="border: none; padding: 0; white-space: nowrap; width: 65px;">Pelanggan</td><td style="border: none; padding: 0;">: {{ $workOrder->customer->nama }}</td></tr>
            @if ($workOrder->customer->plat_nomor)
                <tr><td style="border: none; padding: 0; white-space: nowrap; width: 65px;">Plat</td><td style="border: none; padding: 0;">: {{ $workOrder->customer->plat_nomor }}</td></tr>
            @endif
            @if ($technicianLabels)
                <tr><td style="border: none; padding: 0; white-space: nowrap; width: 65px;">Mekanik</td><td style="border: none; padding: 0;">: {{ $technicianLabels }}</td></tr>
            @endif
        </table>
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