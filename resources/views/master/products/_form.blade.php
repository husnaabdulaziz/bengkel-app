@php $p = $product ?? null; @endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
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

    <div class="row">
        <div class="col-md-6 form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama" value="{{ old('nama', $p?->nama) }}" required class="form-control">
        </div>
        
        <div class="col-md-6 form-group">
            <label>Ukuran (opsional, mis. 100/90)</label>
            <input type="text" name="ukuran" value="{{ old('ukuran', $p?->ukuran) }}" class="form-control">
        </div>
        <div class="col-md-6 form-group">
            <label>Nama Model (opsional, untuk sticker rak — mis. "Swallow Razor TL Ring 12")</label>
            <input type="text" name="model_name" value="{{ old('model_name', $p?->model_name) }}" class="form-control">
        </div>
        <div class="col-md-6 form-group">
            <label>SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $p?->sku) }}" class="form-control">
        </div>

        <div class="col-md-6 form-group">
            <label>Kategori</label>
            <select name="category_id" x-model="categoryId" @change="onCategoryChange()" class="form-control">
                <option value="0">- Pilih -</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label>Sub Kategori</label>
            <div class="position-relative">
                <input type="text" x-model="subcategoryQuery" @input="filterSubcategories" @focus="filterSubcategories"
                       placeholder="Ketik atau pilih..." autocomplete="off" class="form-control">
                <input type="hidden" name="subcategory_id" :value="selectedSubcategoryId || ''">
                <div x-show="subcategoryResults.length > 0 || (subcategoryQuery.length > 0 && !subcategoryExactMatch)"
                     @click.outside="subcategoryResults = []" x-cloak
                     class="list-group position-absolute w-100" style="z-index: 20; max-height: 200px; overflow-y: auto;">
                    <template x-for="item in subcategoryResults" :key="item.id">
                        <a href="#" @click.prevent="selectSubcategory(item)" class="list-group-item list-group-item-action" x-text="item.nama"></a>
                    </template>
                    <a href="#" x-show="subcategoryQuery.length > 0 && !subcategoryExactMatch"
                       @click.prevent="addSubcategory()" class="list-group-item list-group-item-action text-primary">
                        + Tambah "<span x-text="subcategoryQuery"></span>" sebagai sub kategori baru
                    </a>
                </div>
            </div>
            <small class="text-muted" x-show="categoryId == 0">Pilih kategori dulu.</small>
        </div>

        <div class="col-md-6 form-group">
            <label>Brand</label>
            <div class="position-relative">
                <input type="text" x-model="brandQuery" @input="filterBrands" @focus="filterBrands"
                       placeholder="Ketik atau pilih..." autocomplete="off" class="form-control">
                <input type="hidden" name="brand_id" :value="selectedBrandId">
                <div x-show="brandResults.length > 0 || (brandQuery.length > 0 && !brandExactMatch)"
                     @click.outside="brandResults = []" x-cloak
                     class="list-group position-absolute w-100" style="z-index: 20; max-height: 200px; overflow-y: auto;">
                    <template x-for="item in brandResults" :key="item.id">
                        <a href="#" @click.prevent="selectBrand(item)" class="list-group-item list-group-item-action" x-text="item.nama"></a>
                    </template>
                    <a href="#" x-show="brandQuery.length > 0 && !brandExactMatch"
                       @click.prevent="addBrand()" class="list-group-item list-group-item-action text-primary">
                        + Tambah "<span x-text="brandQuery"></span>" sebagai brand baru
                    </a>
                </div>
            </div>
            <small class="text-muted" x-show="categoryId == 0">Pilih kategori dulu.</small>
        </div>

        <div class="col-md-6 form-group">
            <label>Vendor Utama</label>
            <div class="position-relative">
                <input type="text" x-model="supplierQuery" @input="filterSuppliers" @focus="filterSuppliers"
                       placeholder="Ketik atau pilih..." autocomplete="off" class="form-control">
                <input type="hidden" name="default_supplier_id" :value="selectedSupplierId">
                <div x-show="supplierResults.length > 0 || (supplierQuery.length > 0 && !supplierExactMatch)"
                     @click.outside="supplierResults = []" x-cloak
                     class="list-group position-absolute w-100" style="z-index: 20; max-height: 200px; overflow-y: auto;">
                    <template x-for="item in supplierResults" :key="item.id">
                        <a href="#" @click.prevent="selectSupplier(item)" class="list-group-item list-group-item-action" x-text="item.nama"></a>
                    </template>
                    <a href="#" x-show="supplierQuery.length > 0 && !supplierExactMatch"
                       @click.prevent="addSupplier()" class="list-group-item list-group-item-action text-primary">
                        + Tambah "<span x-text="supplierQuery"></span>" sebagai vendor baru
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $p?->satuan ?? 'pcs') }}" required class="form-control">
        </div>
        <div class="col-md-6 form-group">
            <label>Lokasi Rak (opsional)</label>
            <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $p?->lokasi_rak) }}" placeholder="mis. Rak 1.A" class="form-control">
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="is_jasa" name="is_jasa" value="1" @checked(old('is_jasa', $p?->is_jasa))>
        <label class="custom-control-label" for="is_jasa">Ini item Jasa (tidak punya stock)</label>
    </div>
    <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="garansi_aktif" name="garansi_aktif" value="1" @checked(old('garansi_aktif', $p?->garansi_aktif))>
        <label class="custom-control-label" for="garansi_aktif">Aktifkan garansi</label>
    </div>
</div>

<div class="form-group">
    <label>Durasi Garansi (hari)</label>
    <input type="number" name="garansi_durasi_hari" value="{{ old('garansi_durasi_hari', $p?->garansi_durasi_hari) }}" class="form-control" style="max-width: 200px;">
</div>

<hr>
<h5>Fee Mekanik (opsional)</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Jenis Fee</label>
        <select name="fee_type" class="form-control">
            <option value="fixed" @selected(old('fee_type', $p?->fee?->fee_type) === 'fixed')>Nominal Tetap (Rp)</option>
            <option value="percent" @selected(old('fee_type', $p?->fee?->fee_type) === 'percent')>Persen dari Subtotal (%)</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Nilai Fee</label>
        <input type="number" step="0.01" name="fee_value" value="{{ old('fee_value', $p?->fee?->fee_value ?? 0) }}" class="form-control">
    </div>
</div>

<hr>
<h5>Tarif Harga</h5>
<div class="row">
    <div class="col-md-6 form-group">
        <label>Harga Modal</label>
        <input type="number" step="0.01" name="harga_modal" value="{{ old('harga_modal', $p?->harga_modal ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Harga Jual</label>
        <input type="number" step="0.01" name="harga_jual" value="{{ old('harga_jual', $p?->harga_jual ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Harga Bawa</label>
        <input type="number" step="0.01" name="harga_jual_jasa" value="{{ old('harga_jual_jasa', $p?->harga_jual_jasa ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Harga Online</label>
        <input type="number" step="0.01" name="harga_online" value="{{ old('harga_online', $p?->harga_online ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Harga Ojol</label>
        <input type="number" step="0.01" name="harga_ojol" value="{{ old('harga_ojol', $p?->harga_ojol ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Minimum Stock</label>
        <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $p?->minimum_stock ?? 0) }}" required class="form-control">
    </div>
    <div class="col-md-6 form-group">
        <label>Status Produk</label>
        <select name="status" class="form-control">
            <option value="active" @selected(old('status', $p?->status ?? 'active') === 'active')>Aktif (bisa dijual)</option>
            <option value="inactive" @selected(old('status', $p?->status) === 'inactive')>Nonaktif (berhenti dijual, tidak muncul di POS)</option>
        </select>
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