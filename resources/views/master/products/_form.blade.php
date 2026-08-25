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

<div x-data="productComboboxes(
        {{ $subcategories->map(fn($s) => ['id' => $s->id, 'category_id' => $s->category_id, 'nama' => $s->nama])->values()->toJson() }},
        {{ $brands->map(fn($b) => ['id' => $b->id, 'category_ids' => $b->categories->pluck('id')->values(), 'nama' => $b->nama])->values()->toJson() }},
        {{ $suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama])->values()->toJson() }},
        {{ (int) old('category_id', $p?->category_id ?? 0) }},
        {{ (int) old('subcategory_id', $p?->subcategory_id ?? 0) }},
        '{{ old('subcategory_id') ? '' : addslashes($p?->subcategory?->nama ?? '') }}',
        {{ (int) old('brand_id', $p?->brand_id ?? 0) }},
        '{{ addslashes($p?->brand?->nama ?? '') }}',
        {{ (int) old('default_supplier_id', $p?->default_supplier_id ?? 0) }},
        '{{ addslashes($p?->supplier?->nama ?? '') }}'
     )">

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
            <select name="category_id" x-model="categoryId" @change="onCategoryChange()" class="border rounded px-3 py-2 w-full">
                <option value="0">- Pilih -</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sub Kategori</label>
            <div class="relative">
                <input type="text" x-model="subcategoryQuery" @input="filterSubcategories" @focus="filterSubcategories"
                       placeholder="Ketik atau pilih..." autocomplete="off"
                       class="border rounded px-3 py-2 w-full">
                <input type="hidden" name="subcategory_id" :value="selectedSubcategoryId">
                <div x-show="subcategoryResults.length > 0 || (subcategoryQuery.length > 0 && !subcategoryExactMatch)"
                     @click.outside="subcategoryResults = []" x-cloak
                     class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-48 overflow-y-auto">
                    <template x-for="item in subcategoryResults" :key="item.id">
                        <div @click="selectSubcategory(item)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b" x-text="item.nama"></div>
                    </template>
                    <div x-show="subcategoryQuery.length > 0 && !subcategoryExactMatch"
                         @click="addSubcategory()" class="p-2 hover:bg-blue-50 cursor-pointer text-sm text-blue-600">
                        + Tambah "<span x-text="subcategoryQuery"></span>" sebagai sub kategori baru
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1" x-show="categoryId == 0">Pilih kategori dulu.</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Brand</label>
            <div class="relative">
                <input type="text" x-model="brandQuery" @input="filterBrands" @focus="filterBrands"
                       placeholder="Ketik atau pilih..." autocomplete="off"
                       class="border rounded px-3 py-2 w-full">
                <input type="hidden" name="brand_id" :value="selectedBrandId">
                <div x-show="brandResults.length > 0 || (brandQuery.length > 0 && !brandExactMatch)"
                     @click.outside="brandResults = []" x-cloak
                     class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-48 overflow-y-auto">
                    <template x-for="item in brandResults" :key="item.id">
                        <div @click="selectBrand(item)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b" x-text="item.nama"></div>
                    </template>
                    <div x-show="brandQuery.length > 0 && !brandExactMatch"
                         @click="addBrand()" class="p-2 hover:bg-blue-50 cursor-pointer text-sm text-blue-600">
                        + Tambah "<span x-text="brandQuery"></span>" sebagai brand baru
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1" x-show="categoryId == 0">Pilih kategori dulu.</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Vendor Utama</label>
            <div class="relative">
                <input type="text" x-model="supplierQuery" @input="filterSuppliers" @focus="filterSuppliers"
                       placeholder="Ketik atau pilih..." autocomplete="off"
                       class="border rounded px-3 py-2 w-full">
                <input type="hidden" name="default_supplier_id" :value="selectedSupplierId">
                <div x-show="supplierResults.length > 0 || (supplierQuery.length > 0 && !supplierExactMatch)"
                     @click.outside="supplierResults = []" x-cloak
                     class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-48 overflow-y-auto">
                    <template x-for="item in supplierResults" :key="item.id">
                        <div @click="selectSupplier(item)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b" x-text="item.nama"></div>
                    </template>
                    <div x-show="supplierQuery.length > 0 && !supplierExactMatch"
                         @click="addSupplier()" class="p-2 hover:bg-blue-50 cursor-pointer text-sm text-blue-600">
                        + Tambah "<span x-text="supplierQuery"></span>" sebagai vendor baru
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $p?->satuan ?? 'pcs') }}" required class="border rounded px-3 py-2 w-full">
        </div>
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
<h3 class="font-semibold">Fee Mekanik (opsional)</h3>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Jenis Fee</label>
        <select name="fee_type" class="border rounded px-3 py-2 w-full">
            <option value="fixed" @selected(old('fee_type', $p?->fee?->fee_type) === 'fixed')>Nominal Tetap (Rp)</option>
            <option value="percent" @selected(old('fee_type', $p?->fee?->fee_type) === 'percent')>Persen dari Subtotal (%)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Nilai Fee</label>
        <input type="number" step="0.01" name="fee_value" value="{{ old('fee_value', $p?->fee?->fee_value ?? 0) }}" class="border rounded px-3 py-2 w-full">
    </div>
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

@push('scripts')
<script>
    function productComboboxes(
        subcategories, brands, suppliers,
        initialCategoryId, initialSubcategoryId, initialSubcategoryName,
        initialBrandId, initialBrandName,
        initialSupplierId, initialSupplierName
    ) {
        return {
            categoryId: initialCategoryId,
            allSubcategories: subcategories,
            allBrands: brands,
            allSuppliers: suppliers,

            subcategoryQuery: initialSubcategoryName,
            subcategoryResults: [],
            selectedSubcategoryId: initialSubcategoryId,
            subcategoryExactMatch: !!initialSubcategoryName,

            brandQuery: initialBrandName,
            brandResults: [],
            selectedBrandId: initialBrandId,
            brandExactMatch: !!initialBrandName,

            supplierQuery: initialSupplierName,
            supplierResults: [],
            selectedSupplierId: initialSupplierId,
            supplierExactMatch: !!initialSupplierName,

            onCategoryChange() {
                this.subcategoryQuery = '';
                this.selectedSubcategoryId = 0;
                this.subcategoryExactMatch = false;
                this.brandQuery = '';
                this.selectedBrandId = 0;
                this.brandExactMatch = false;
            },

            filterSubcategories() {
                this.selectedSubcategoryId = 0;
                const q = this.subcategoryQuery.toLowerCase();
                const inCategory = this.allSubcategories.filter(s => s.category_id == this.categoryId);
                this.subcategoryResults = q.length > 0
                    ? inCategory.filter(s => s.nama.toLowerCase().includes(q))
                    : inCategory;
                this.subcategoryExactMatch = inCategory.some(s => s.nama.toLowerCase() === q);
            },
            selectSubcategory(item) {
                this.selectedSubcategoryId = item.id;
                this.subcategoryQuery = item.nama;
                this.subcategoryExactMatch = true;
                this.subcategoryResults = [];
            },
            addSubcategory() {
                if (this.categoryId == 0) { alert('Pilih kategori dulu.'); return; }
                fetch('{{ route('product-subcategories.quick') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ category_id: this.categoryId, nama: this.subcategoryQuery }),
                })
                .then(r => r.json())
                .then(item => {
                    this.allSubcategories.push(item);
                    this.selectSubcategory(item);
                });
            },

            filterBrands() {
                this.selectedBrandId = 0;
                const q = this.brandQuery.toLowerCase();
                const inCategory = this.allBrands.filter(b => b.category_ids.includes(parseInt(this.categoryId)));
                this.brandResults = q.length > 0
                    ? inCategory.filter(b => b.nama.toLowerCase().includes(q))
                    : inCategory;
                this.brandExactMatch = inCategory.some(b => b.nama.toLowerCase() === q);
            },
            selectBrand(item) {
                this.selectedBrandId = item.id;
                this.brandQuery = item.nama;
                this.brandExactMatch = true;
                this.brandResults = [];
            },
            addBrand() {
                if (this.categoryId == 0) { alert('Pilih kategori dulu.'); return; }
                fetch('{{ route('product-brands.quick') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ category_id: this.categoryId, nama: this.brandQuery }),
                })
                .then(r => r.json())
                .then(item => {
                    this.allBrands.push(item);
                    this.selectBrand(item);
                });
            },

            filterSuppliers() {
                this.selectedSupplierId = 0;
                const q = this.supplierQuery.toLowerCase();
                this.supplierResults = q.length > 0
                    ? this.allSuppliers.filter(s => s.nama.toLowerCase().includes(q))
                    : this.allSuppliers;
                this.supplierExactMatch = this.allSuppliers.some(s => s.nama.toLowerCase() === q);
            },
            selectSupplier(item) {
                this.selectedSupplierId = item.id;
                this.supplierQuery = item.nama;
                this.supplierExactMatch = true;
                this.supplierResults = [];
            },
            addSupplier() {
                fetch('{{ route('suppliers.quick') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ nama: this.supplierQuery }),
                })
                .then(r => r.json())
                .then(item => {
                    this.allSuppliers.push(item);
                    this.selectSupplier(item);
                });
            },
        }
    }
</script>
@endpush