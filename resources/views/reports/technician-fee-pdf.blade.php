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
    </style>
</head>
<body>
    <table style="border: none; margin-bottom: 10px;">
    <tr>
        <td style="border: none; width: 75%; vertical-align: top; padding: 0;">
            <h2>Laporan Fee Mekanik {{ $periodLabel }}</h2>
            <div class="meta">
                <strong>{{ $company->nama_toko ?? '-' }}</strong><br>
                {{ $company->alamat_toko ?? '-' }}<br>
                <strong>Nama Mekanik:</strong> {{ $technicianLabel }}<br>
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
        <thead>
            <tr>
                <th class="text-center" style="width:30px;">No</th>
                <th>Teknisi</th>
                <th>Produk</th>
                <th>Keterangan</th>
                <th class="text-right">Fee</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $row->technician->inisial ?? '-' }} - {{ $row->technician->name }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->notes ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row->fee_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">Total Fee: Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
</body>
</html>