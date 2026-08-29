<x-admin-layout title="Catat Pembelian dari Vendor">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.store') }}"
          x-data="purchaseForm(
              {{ $products->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama, 'satuan' => $p->satuan, 'harga_modal' => (float) $p->harga_modal])->values()->toJson() }},
              {{ $suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama])->values()->toJson() }},
              {{ (int) old('supplier_id', 0) }},
              '{{ addslashes(old('supplier_nama', '')) }}'
          )"
          class="card">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Cabang</label>
                    <select name="branch_id" required class="form-control">
                        <option value="">- Pilih Cabang -</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id') ? old('branch_id') == $branch->id : $branch->is_main)>{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 form-group">
                    <label>Vendor</label>
                    <div class="position-relative">
                        <input type="text" x-model="supplierQuery" @input="filterSuppliers" @focus="filterSuppliers"
                               placeholder="Ketik atau pilih..." autocomplete="off" class="form-control">
                        <input type="hidden" name="supplier_id" :value="selectedSupplierId" required>
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
                    <label>No. Invoice Vendor</label>
                    <input type="text" name="invoice_number" class="form-control" placeholder="Kosongkan kalau tidak ada nomor invoice">
                </div>
                <div class="col-md-6 form-group">
                    <label>Tanggal Faktur</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="form-control">
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>

            <template x-for="(item, index) in items" :key="index">
                <div class="row align-items-end mb-2">
                    <div class="col-5 position-relative">
                        <label class="small mb-1">Produk</label>
                        <input type="text" x-model="item.productQuery" @input="filterProducts(item)" @focus="filterProducts(item)"
                               placeholder="Ketik nama produk..." autocomplete="off" class="form-control form-control-sm">
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id" required>
                        <div x-show="item.results && item.results.length > 0"
                             @click.outside="item.results = []" x-cloak
                             class="list-group position-absolute" style="z-index: 20; max-height: 200px; overflow-y: auto; width: calc(100% - 30px);">
                            <template x-for="p in item.results" :key="p.id">
                                <a href="#" @click.prevent="selectProduct(item, p)" class="list-group-item list-group-item-action py-1">
                                    <span x-text="p.nama"></span>
                                    <small class="text-muted" x-text="'(' + p.satuan + ')'"></small>
                                </a>
                            </template>
                        </div>
                        <small class="text-danger" x-show="item.productQuery.length > 1 && item.results.length === 0 && !item.product_id">
                            Tidak ditemukan. <a href="{{ route('products.create') }}" target="_blank">Tambah produk baru</a>
                        </small>
                    </div>
                    <div class="col-2">
                        <label class="small mb-1">Qty</label>
                        <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="form-control form-control-sm">
                    </div>
                    <div class="col-3">
                        <label class="small mb-1">Harga Beli/Unit</label>
                        <input type="number" step="0.01" :name="`items[${index}][price_per_unit]`" x-model.number="item.price_per_unit" min="0" required class="form-control form-control-sm">
                    </div>
                    <div class="col-1 small font-weight-bold text-nowrap" x-text="'Rp ' + (item.quantity * item.price_per_unit).toLocaleString('id-ID')"></div>
                    <div class="col-1">
                        <button type="button" @click="items.splice(index, 1)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem()" class="btn btn-link btn-sm px-0">+ Tambah Baris Item</button>

            <div class="text-right font-weight-bold h5 pt-2 border-top" x-text="'Total: Rp ' + total.toLocaleString('id-ID')"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
        </div>
    </form>

    @push('scripts')
    <script>
        function purchaseForm(products, suppliers, initialSupplierId, initialSupplierName) {
            return {
                allProducts: products,
                allSuppliers: suppliers,
                items: [{ product_id: '', productQuery: '', results: [], quantity: 1, price_per_unit: 0 }],

                supplierQuery: initialSupplierName,
                supplierResults: [],
                selectedSupplierId: initialSupplierId,
                supplierExactMatch: !!initialSupplierName,

                addItem() {
                    this.items.push({ product_id: '', productQuery: '', results: [], quantity: 1, price_per_unit: 0 });
                },

                filterProducts(item) {
                    item.product_id = '';
                    const q = item.productQuery.toLowerCase();
                    item.results = q.length > 1
                        ? this.allProducts.filter(p => p.nama.toLowerCase().includes(q))
                        : [];
                },
                selectProduct(item, p) {
                    item.product_id = p.id;
                    item.productQuery = p.nama;
                    item.price_per_unit = p.harga_modal;
                    item.results = [];
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

                get total() {
                    return this.items.reduce((sum, it) => sum + (it.quantity * it.price_per_unit), 0);
                },
            }
        }
    </script>
    @endpush
</x-admin-layout>