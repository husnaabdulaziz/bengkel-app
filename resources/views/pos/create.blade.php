<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">POS — Servis Baru</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8"
         x-data="posForm('{{ $branchId }}')">

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pos.store') }}" @submit="beforeSubmit">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branchId }}">
            <input type="hidden" name="customer_id" x-model="selectedCustomer ? selectedCustomer.id : ''">
            <input type="hidden" name="customer_price_tier" x-model="priceTier">

            <!-- ===== BAGIAN PELANGGAN ===== -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Data Pelanggan</h3>

                <template x-if="!selectedCustomer">
                    <div>
                        <label class="block text-sm font-medium mb-1">Cari Pelanggan (nama / telpon / plat nomor)</label>
                        <div class="relative">
                            <input type="text" x-model="customerQuery" @input="searchCustomers"
                                   placeholder="Ketik untuk mencari..." class="border rounded px-3 py-2 w-full" autocomplete="off">
                            <div x-show="customerResults.length > 0" class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-60 overflow-y-auto">
                                <template x-for="c in customerResults" :key="c.id">
                                    <div @click="selectCustomer(c)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b">
                                        <div class="font-medium" x-text="c.nama"></div>
                                        <div class="text-gray-500 text-xs" x-text="[c.telpon, c.plat_nomor].filter(Boolean).join(' · ')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" @click="isNewCustomer = !isNewCustomer" class="text-blue-600 text-sm">
                                <span x-show="!isNewCustomer">+ Pelanggan tidak ditemukan? Input data baru</span>
                                <span x-show="isNewCustomer">- Sembunyikan form pelanggan baru</span>
                            </button>
                        </div>

                        <div x-show="isNewCustomer" class="mt-3 grid grid-cols-2 gap-3 border-t pt-3">
                            <input type="text" name="new_customer[nama]" x-model="newCustomer.nama" placeholder="Nama pelanggan" class="border rounded px-3 py-2">
                            <input type="text" name="new_customer[telpon]" x-model="newCustomer.telpon" placeholder="Nomor telpon" class="border rounded px-3 py-2">
                            <input type="text" name="new_customer[plat_nomor]" x-model="newCustomer.plat_nomor" placeholder="Plat nomor" class="border rounded px-3 py-2">
                            <div></div>

                            <button type="button" @click="showMoreInfo = !showMoreInfo" class="col-span-2 text-blue-600 text-sm text-left">
                                <span x-show="!showMoreInfo">+ Tambah informasi lainnya</span>
                                <span x-show="showMoreInfo">- Sembunyikan informasi lainnya</span>
                            </button>

                            <template x-if="showMoreInfo">
                                <input type="text" name="new_customer[alamat]" x-model="newCustomer.alamat" placeholder="Alamat" class="border rounded px-3 py-2 col-span-2">
                            </template>
                            <template x-if="showMoreInfo">
                                <input type="text" name="new_customer[jenis_kendaraan]" x-model="newCustomer.jenis_kendaraan" placeholder="Jenis kendaraan (motor/mobil)" class="border rounded px-3 py-2">
                            </template>
                            <template x-if="showMoreInfo">
                                <input type="text" name="new_customer[merk_kendaraan]" x-model="newCustomer.merk_kendaraan" placeholder="Merk kendaraan" class="border rounded px-3 py-2">
                            </template>
                            <template x-if="showMoreInfo">
                                <input type="text" name="new_customer[model_kendaraan]" x-model="newCustomer.model_kendaraan" placeholder="Model kendaraan" class="border rounded px-3 py-2">
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="selectedCustomer">
                    <div class="flex justify-between items-start bg-blue-50 p-3 rounded">
                        <div class="text-sm">
                            <div class="font-medium" x-text="selectedCustomer.nama"></div>
                            <div class="text-gray-600" x-text="[selectedCustomer.telpon, selectedCustomer.plat_nomor].filter(Boolean).join(' · ')"></div>
                        </div>
                        <button type="button" @click="clearCustomer" class="text-red-600 text-sm">Ganti</button>
                    </div>
                </template>
            </div>

            <!-- ===== BAGIAN TARIF HARGA ===== -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Kategori Harga</h3>
                <select x-model="priceTier" @change="recalcAllPrices" class="border rounded px-3 py-2">
                    <option value="harga_jual">Harga Jual (Reguler)</option>
                    <option value="harga_jual_jasa">Harga Jual + Jasa</option>
                    <option value="harga_online">Harga Online</option>
                    <option value="harga_ojol">Harga Ojol</option>
                    <option value="custom">Custom (edit manual per item)</option>
                </select>
            </div>

            <!-- ===== BAGIAN ITEM PRODUK ===== -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="font-semibold mb-3">Item Produk / Jasa</h3>

                <div class="relative mb-3">
                    <input type="text" x-model="productQuery" @input="searchProducts"
                           placeholder="Cari produk untuk ditambahkan..." class="border rounded px-3 py-2 w-full" autocomplete="off">
                    <div x-show="productResults.length > 0" class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-60 overflow-y-auto">
                        <template x-for="p in productResults" :key="p.id">
                            <div @click="addItem(p)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b flex justify-between">
                                <span x-text="p.nama"></span>
                                <span class="text-gray-500" x-text="'Rp ' + priceForTier(p).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">Item</th>
                            <th class="p-2 text-right">Qty</th>
                            <th class="p-2 text-right">Harga</th>
                            <th class="p-2 text-right">Subtotal</th>
                            <th class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-t">
                                <td class="p-2">
                                    <span x-text="item.nama"></span>
                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                </td>
                                <td class="p-2 text-right">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" class="border rounded px-2 py-1 w-16 text-right">
                                </td>
                                <td class="p-2 text-right">
                                    <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" class="border rounded px-2 py-1 w-28 text-right">
                                </td>
                                <td class="p-2 text-right" x-text="'Rp ' + (item.quantity * item.unit_price).toLocaleString('id-ID')"></td>
                                <td class="p-2">
                                    <button type="button" @click="items.splice(index, 1)" class="text-red-600 text-xs">Hapus</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="5" class="p-3 text-gray-500 text-center">Belum ada item, cari produk di atas.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-right font-semibold text-lg pt-3 border-t mt-2" x-text="'Total: Rp ' + total.toLocaleString('id-ID')"></div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan sebagai Draft</button>
        </form>
    </div>

    @push('scripts')
    <script>
        function posForm(branchId) {
            return {
                branchId,
                customerQuery: '', customerResults: [], selectedCustomer: null,
                isNewCustomer: false, showMoreInfo: false,
                newCustomer: { nama: '', telpon: '', plat_nomor: '', alamat: '', jenis_kendaraan: '', merk_kendaraan: '', model_kendaraan: '' },
                productQuery: '', productResults: [],
                items: [],
                priceTier: 'harga_jual',
                _debounce: null,

                searchCustomers() {
                    clearTimeout(this._debounce);
                    if (this.customerQuery.length < 2) { this.customerResults = []; return; }
                    this._debounce = setTimeout(() => {
                        fetch(`{{ route('pos.search-customer') }}?q=${encodeURIComponent(this.customerQuery)}`)
                            .then(r => r.json())
                            .then(data => this.customerResults = data);
                    }, 300);
                },
                selectCustomer(c) {
                    this.selectedCustomer = c;
                    this.customerResults = [];
                    this.customerQuery = '';
                },
                clearCustomer() {
                    this.selectedCustomer = null;
                },

                searchProducts() {
                    clearTimeout(this._debounce);
                    if (this.productQuery.length < 2) { this.productResults = []; return; }
                    this._debounce = setTimeout(() => {
                        fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}`)
                            .then(r => r.json())
                            .then(data => this.productResults = data);
                    }, 300);
                },
                priceForTier(p) {
                    if (this.priceTier === 'custom') return parseFloat(p.harga_jual);
                    return parseFloat(p[this.priceTier] ?? p.harga_jual);
                },
                addItem(p) {
                    this.items.push({
                        product_id: p.id,
                        nama: p.nama,
                        quantity: 1,
                        unit_price: this.priceForTier(p),
                        _raw: p,
                    });
                    this.productResults = [];
                    this.productQuery = '';
                },
                recalcAllPrices() {
                    this.items.forEach(item => {
                        if (item._raw) item.unit_price = this.priceForTier(item._raw);
                    });
                },
                get total() {
                    return this.items.reduce((sum, it) => sum + (it.quantity * it.unit_price), 0);
                },
                beforeSubmit(e) {
                    if (!this.selectedCustomer && !this.newCustomer.nama) {
                        alert('Pilih pelanggan atau isi data pelanggan baru terlebih dahulu.');
                        e.preventDefault();
                    }
                },
            }
        }
    </script>
    @endpush
</x-app-layout>