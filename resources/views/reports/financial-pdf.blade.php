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
        .summary-row td { font-weight: bold; background: #f9f9f9; }
        .final-row td { font-weight: bold; background: #e8f5e9; font-size: 13px; }
        .neg { color: #b91c1c; }
    </style>
</head>
<body>
    <table style="border: none; margin-bottom: 10px;">
        <tr>
            <td style="border: none; width: 75%; vertical-align: top; padding: 0;">
                <h2>Laporan Laba Rugi</h2>
                <div class="meta">
                    <strong>{{ $company->nama_toko ?? '-' }}</strong><br>
                    {{ $company->alamat_toko ?? '-' }}<br>
                    <strong>Tanggal:</strong> {{ $dateLabel }}
                </div>
            </td>
            <td style="border: none; width: 25%; text-align: right; vertical-align: top; padding: 0;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-width: 100px; max-height: 100px;">
                @endif
            </td>
        </tr>
    </table>

    <table>
        <tbody>
            <tr><td>Total Penjualan</td><td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
            <tr><td>Total Modal (HPP)</td><td class="text-right neg">- Rp {{ number_format($totalModal, 0, ',', '.') }}</td></tr>
            <tr><td>Total Fee Mekanik</td><td class="text-right neg">- Rp {{ number_format($totalFee, 0, ',', '.') }}</td></tr>
            <tr class="summary-row"><td>Laba Kotor</td><td class="text-right">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td></tr>
            <tr><td>Total Pengeluaran</td><td class="text-right neg">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>
            <tr class="final-row"><td>LABA BERSIH</td><td class="text-right">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <h3 style="margin-top: 20px;">Rincian Pengeluaran</h3>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Nominal</th></tr>
        </thead>
        <tbody>
            @forelse ($expenses as $exp)
                <tr>
                    <td>{{ $exp->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $exp->category }}</td>
                    <td>{{ $exp->description ?? '-' }}</td>
                    <td class="text-right">{{ number_format($exp->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">Tidak ada pengeluaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>