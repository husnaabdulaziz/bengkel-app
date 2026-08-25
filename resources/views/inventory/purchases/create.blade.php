<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catat Pembelian dari Vendor</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
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
              class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Cabang</label>
                    <select name="branch_id" required class="border rounded px-3 py-2 w-full">
                        <option value="">- Pilih Cabang -</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Vendor</label>
                    <div class="relative">
                        <input type="text" x-model="supplierQuery" @input="filterSuppliers" @focus="filterSuppliers"
                               placeholder="Ketik atau pilih..." autocomplete="off"
                               class="border rounded px-3 py-2 w-full">
                        <input type="hidden" name="supplier_id" :value="selectedSupplierId" required>
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
                    <label class="block text-sm font-medium mb-1">No. Invoice Vendor</label>
                    <input type="text" name="invoice_number" required class="border rounded px-3 py-2 w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Faktur</label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="border rounded px-3 py-2 w-full">
                </div>
            </div>

            <hr>
            <h3 class="font-semibold">Item Produk</h3>

            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-12 gap-2 items-end">
                    <div class="col-span-5 relative">
                        <label class="block text-xs mb-1">Produk</label>
                        <input type="text" x-model="item.productQuery" @input="filterProducts(item)" @focus="filterProducts(item)"
                               placeholder="Ketik nama produk..." autocomplete="off"
                               class="border rounded px-2 py-2 w-full text-sm">
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id" required>
                        <div x-show="item.results && item.results.length > 0"
                             @click.outside="item.results = []" x-cloak
                             class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-48 overflow-y-auto">
                            <template x-for="p in item.results" :key="p.id">
                                <div @click="selectProduct(item, p)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b">
                                    <span x-text="p.nama"></span>
                                    <span class="text-gray-400 text-xs" x-text="'(' + p.satuan + ')'"></span>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-red-500 mt-1" x-show="item.productQuery.length > 1 && item.results.length === 0 && !item.product_id">
                            Tidak ditemukan. <a href="{{ route('products.create') }}" target="_blank" class="underline">Tambah produk baru</a>
                        </p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs mb-1">Qty</label>
                        <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="border rounded px-2 py-2 w-full text-sm">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-xs mb-1">Harga Beli/Unit</label>
                        <input type="number" step="0.01" :name="`items[${index}][price_per_unit]`" x-model.number="item.price_per_unit" min="0" required class="border rounded px-2 py-2 w-full text-sm">
                    </div>
                    <div class="col-span-1 text-sm font-medium text-gray-600" x-text="'Rp ' + (item.quantity * item.price_per_unit).toLocaleString('id-ID')"></div>
                    <div class="col-span-1">
                        <button type="button" @click="items.splice(index, 1)" class="text-red-600 text-sm">Hapus</button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem()" class="text-blue-600 text-sm">+ Tambah Baris Item</button>

            <div class="text-right font-semibold text-lg pt-2 border-t" x-text="'Total: Rp ' + total.toLocaleString('id-ID')"></div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan Pembelian</button>
        </form>
    </div>

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
</x-app-layout>