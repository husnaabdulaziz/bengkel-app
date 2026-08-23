@php $p = $product ?? null; @endphp

@if ($errors->any())
    <div class="p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Nama Produk</label>
        <input type="text" name="nama" value="{{ old('nama', $p?->nama) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $p?->sku) }}" class="border rounded px-3 py-2 w-full">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Kategori</label>
        <select name="category_id" class="border rounded px-3 py-2 w-full">
            <option value="">- Pilih -</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $p?->category_id) == $cat->id)>{{ $cat->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Brand</label>
        <select name="brand_id" class="border rounded px-3 py-2 w-full">
            <option value="">- Pilih -</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $p?->brand_id) == $brand->id)>{{ $brand->nama }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Supplier Utama</label>
        <select name="default_supplier_id" class="border rounded px-3 py-2 w-full">
            <option value="">- Pilih -</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('default_supplier_id', $p?->default_supplier_id) == $supplier->id)>{{ $supplier->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Satuan</label>
        <input type="text" name="satuan" value="{{ old('satuan', $p?->satuan ?? 'pcs') }}" required class="border rounded px-3 py-2 w-full">
    </div>
</div>

<div class="flex gap-6">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_jasa" value="1" @checked(old('is_jasa', $p?->is_jasa))>
        <span class="text-sm">Ini item Jasa (tidak punya stock)</span>
    </label>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="garansi_aktif" value="1" @checked(old('garansi_aktif', $p?->garansi_aktif))>
        <span class="text-sm">Aktifkan garansi</span>
    </label>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Durasi Garansi (hari)</label>
    <input type="number" name="garansi_durasi_hari" value="{{ old('garansi_durasi_hari', $p?->garansi_durasi_hari) }}" class="border rounded px-3 py-2 w-full">
</div>

<hr class="my-2">
<h3 class="font-semibold">Tarif Harga</h3>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Harga Modal</label>
        <input type="number" step="0.01" name="harga_modal" value="{{ old('harga_modal', $p?->harga_modal ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Harga Jual</label>
        <input type="number" step="0.01" name="harga_jual" value="{{ old('harga_jual', $p?->harga_jual ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Harga Jual + Jasa</label>
        <input type="number" step="0.01" name="harga_jual_jasa" value="{{ old('harga_jual_jasa', $p?->harga_jual_jasa ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Harga Online</label>
        <input type="number" step="0.01" name="harga_online" value="{{ old('harga_online', $p?->harga_online ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Harga Ojol</label>
        <input type="number" step="0.01" name="harga_ojol" value="{{ old('harga_ojol', $p?->harga_ojol ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Minimum Stock</label>
        <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $p?->minimum_stock ?? 0) }}" required class="border rounded px-3 py-2 w-full">
    </div>
</div>