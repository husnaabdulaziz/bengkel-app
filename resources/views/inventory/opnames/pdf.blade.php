<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { margin-bottom: 0; }
        .meta { margin-bottom: 16px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-green { color: #15803d; }
        .text-red { color: #b91c1c; }
        .summary { margin-top: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Laporan Stock Opname</h2>
    <div class="meta">
        <strong>Kode:</strong> {{ $opname->kode_opname }}<br>
        <strong>Tanggal:</strong> {{ $opname->opname_date->format('d/m/Y') }}<br>
        <strong>Cabang:</strong> {{ $opname->branch->nama_cabang }}<br>
        <strong>Status:</strong> {{ $opname->status }}<br>
        <strong>Diselesaikan:</strong> {{ $opname->completed_at?->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                @if ($showKategori) <th>Kategori</th> @endif
                @if ($showSubkategori) <th>Sub Kategori</th> @endif
                @if ($showBrand) <th>Brand</th> @endif
                @if ($showLokasi) <th>Lokasi</th> @endif
                <th class="text-right">Stock Sistem</th>
                <th class="text-right">Stock Real</th>
                <th class="text-right">Selisih</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($opname->items as $item)
                <tr>
                    <td>{{ $item->product->nama }}</td>
                    @if ($showKategori) <td>{{ $item->product->category?->nama ?? '-' }}</td> @endif
                    @if ($showSubkategori) <td>{{ $item->product->subcategory?->nama ?? '-' }}</td> @endif
                    @if ($showBrand) <td>{{ $item->product->brand?->nama ?? '-' }}</td> @endif
                    @if ($showLokasi) <td>{{ $item->product->lokasi_rak ?? '-' }}</td> @endif
                    <td class="text-right">{{ $item->system_stock }}</td>
                    <td class="text-right">{{ $item->real_stock }}</td>
                    <td class="text-right {{ $item->difference > 0 ? 'text-green' : ($item->difference < 0 ? 'text-red' : '') }}">
                        {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                    </td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        Total selisih: {{ $opname->items->sum(fn($i) => $i->real_stock - $i->system_stock) }} unit
    </div>
</body>
</html>