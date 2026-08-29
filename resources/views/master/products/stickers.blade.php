<x-admin-layout title="Cetak Sticker Rak">

    <div class="d-print-none">
        <form method="GET" class="d-flex flex-wrap align-items-center mb-3" style="gap: 0.5rem;">
            <select name="category_id" onchange="this.form.submit()" class="form-control" style="max-width: 250px;">
                <option value="">- Semua Kategori -</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($selectedCategoryId == $cat->id)>{{ $cat->nama }}</option>
                @endforeach
            </select>

            <select name="brand_id" onchange="this.form.submit()" class="form-control" style="max-width: 250px;">
                <option value="">- Semua Brand -</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected($selectedBrandId == $brand->id)>{{ $brand->nama }}</option>
                @endforeach
            </select>

            @if ($groups->isNotEmpty())
                <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Sticker</button>
            @endif
        </form>

        @if (($selectedBrandId || $selectedCategoryId) && $groups->isEmpty())
            <div class="alert alert-info">Tidak ada produk aktif untuk filter ini.</div>
        @endif
    </div>

    <style>
        @media print {
            .content-header, .main-header, .main-sidebar, .main-footer, .d-print-none { display: none !important; }
            .content-wrapper { margin-left: 0 !important; }
            body { background: #fff !important; }
        }
        .sticker-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .sticker-box { border: 2px solid #000; border-radius: 4px; overflow: hidden; break-inside: avoid; page-break-inside: avoid; margin-bottom: 12px; }
        .sticker-title { background: #000; color: #fff; text-align: center; font-weight: bold; padding: 6px; font-size: 0.95rem; }
        .sticker-variants { display: grid; }
        .sticker-variant-header { background: #ffe14d; text-align: center; font-weight: bold; padding: 6px; border-top: 1px solid #000; font-size: 0.9rem; }
        .sticker-variant-price { text-align: center; font-weight: bold; padding: 8px; font-size: 1.1rem; border-top: 1px solid #000; }
    </style>

    <div class="sticker-grid">
        @foreach ($groups as $modelName => $items)
            <div class="sticker-box">
                <div class="sticker-title">{{ $modelName }}</div>
                <div class="sticker-variants" style="grid-template-columns: repeat({{ $items->count() }}, 1fr);">
                    @foreach ($items as $item)
                        <div class="sticker-variant-header">{{ $item->ukuran ?: $item->nama }}</div>
                    @endforeach
                    @foreach ($items as $item)
                        <div class="sticker-variant-price">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>