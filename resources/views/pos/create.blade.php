<x-admin-layout title="POS — Servis Baru">

    <div x-data="posForm('{{ $branchId }}')">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Sidebar: transaksi lain yang belum selesai -->
            <div class="col-lg-3 order-2 order-lg-1">
                @include('pos.partials.orders-sidebar')
            </div>

            <!-- Form Utama -->
            <div class="col-lg-9 order-1 order-lg-2">
                <form method="POST" action="{{ route('pos.store') }}" @submit="beforeSubmit">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    <input type="hidden" name="customer_id" x-model="selectedCustomer ? selectedCustomer.id : ''">
                    <input type="hidden" name="customer_price_tier" x-model="priceTier">

                    <!-- Data Pelanggan -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Data Pelanggan</h3></div>
                        <div class="card-body">
                            <template x-if="!selectedCustomer">
                                <div>
                                    <label>Cari Pelanggan (nama / telpon / plat nomor)</label>
                                    <div class="position-relative">
                                        <input type="text" x-model="customerQuery" @input="searchCustomers"
                                               placeholder="Ketik untuk mencari..." class="form-control" autocomplete="off">
                                        <div x-show="customerResults.length > 0" class="list-group position-absolute w-100" style="z-index: 20; max-height: 240px; overflow-y: auto;">
                                            <template x-for="c in customerResults" :key="c.id">
                                                <a href="#" @click.prevent="selectCustomer(c)" class="list-group-item list-group-item-action">
                                                    <div class="font-weight-bold" x-text="c.nama"></div>
                                                    <small class="text-muted" x-text="[c.telpon, c.plat_nomor].filter(Boolean).join(' · ')"></small>
                                                </a>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <a href="#" @click.prevent="isNewCustomer = !isNewCustomer" class="text-primary">
                                            <span x-show="!isNewCustomer">+ Pelanggan tidak ditemukan? Input data baru</span>
                                            <span x-show="isNewCustomer">- Sembunyikan form pelanggan baru</span>
                                        </a>
                                    </div>

                                    <div x-show="isNewCustomer" class="row mt-3 pt-3 border-top">
                                        <div class="col-md-6 mb-2"><input type="text" name="new_customer[nama]" x-model="newCustomer.nama" placeholder="Nama pelanggan" class="form-control"></div>
                                        <div class="col-md-6 mb-2"><input type="text" name="new_customer[telpon]" x-model="newCustomer.telpon" placeholder="Nomor telpon" class="form-control"></div>
                                        <div class="col-md-6 mb-2"><input type="text" name="new_customer[plat_nomor]" x-model="newCustomer.plat_nomor" placeholder="Plat nomor" class="form-control"></div>
                                        <div class="col-12">
                                            <a href="#" @click.prevent="showMoreInfo = !showMoreInfo" class="text-primary d-inline-block mb-2">
                                                <span x-show="!showMoreInfo">+ Tambah informasi lainnya</span>
                                                <span x-show="showMoreInfo">- Sembunyikan informasi lainnya</span>
                                            </a>
                                        </div>
                                        <template x-if="showMoreInfo">
                                            <div class="col-12 mb-2"><input type="text" name="new_customer[alamat]" x-model="newCustomer.alamat" placeholder="Alamat" class="form-control"></div>
                                        </template>
                                        <template x-if="showMoreInfo">
                                            <div class="col-md-4 mb-2"><input type="text" name="new_customer[jenis_kendaraan]" x-model="newCustomer.jenis_kendaraan" placeholder="Jenis kendaraan" class="form-control"></div>
                                        </template>
                                        <template x-if="showMoreInfo">
                                            <div class="col-md-4 mb-2"><input type="text" name="new_customer[merk_kendaraan]" x-model="newCustomer.merk_kendaraan" placeholder="Merk kendaraan" class="form-control"></div>
                                        </template>
                                        <template x-if="showMoreInfo">
                                            <div class="col-md-4 mb-2"><input type="text" name="new_customer[model_kendaraan]" x-model="newCustomer.model_kendaraan" placeholder="Model kendaraan" class="form-control"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="selectedCustomer">
                                <div class="d-flex justify-content-between align-items-start bg-light p-3 rounded">
                                    <div>
                                        <div class="font-weight-bold" x-text="selectedCustomer.nama"></div>
                                        <small class="text-muted" x-text="[selectedCustomer.telpon, selectedCustomer.plat_nomor].filter(Boolean).join(' · ')"></small>
                                    </div>
                                    <button type="button" @click="clearCustomer" class="btn btn-sm btn-outline-danger">Ganti</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kategori Harga -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Kategori Harga</h3></div>
                        <div class="card-body">
                            <select x-model="priceTier" @change="recalcAllPrices" class="form-control" style="max-width: 300px;">
                                <option value="harga_jual">Harga Jual (Reguler)</option>
                                <option value="harga_jual_jasa">Harga Bawa</option>
                                <option value="harga_online">Harga Online</option>
                                <option value="harga_ojol">Harga Ojol</option>
                                <option value="custom">Custom (edit manual per item)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Mekanik -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Mekanik yang Mengerjakan</h3></div>
                        <div class="card-body">
                            <p class="text-muted small">Data ini hanya untuk pencatatan fee internal, tidak ditampilkan di invoice pelanggan.</p>
                            <div class="d-flex flex-wrap" style="gap: 1rem;">
                                @foreach ($technicians as $tech)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="tech-{{ $tech->id }}" value="{{ $tech->id }}" @change="toggleTechnician({{ $tech->id }})" :checked="selectedTechnicians.includes({{ $tech->id }})">
                                        <label class="custom-control-label" for="tech-{{ $tech->id }}">{{ $tech->inisial ?? $tech->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="custom-control custom-checkbox mt-3 pt-3 border-top">
                                <input type="checkbox" class="custom-control-input" id="manualFeeCheck" x-model="manualFee" :disabled="selectedTechnicians.length > 1">
                                <label class="custom-control-label" for="manualFeeCheck">Input Fee Manual</label>
                            </div>
                            <p class="text-muted small mt-1" x-show="selectedTechnicians.length > 1">Fee wajib diisi manual karena lebih dari 1 mekanik dipilih.</p>

                            <template x-for="techId in selectedTechnicians" :key="techId">
                                <input type="hidden" name="technician_ids[]" :value="techId">
                            </template>
                            <input type="hidden" name="manual_fee" :value="(manualFee || selectedTechnicians.length > 1) ? 1 : 0">
                        </div>
                    </div>

                    <!-- Item Produk -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Item Produk / Jasa</h3></div>
                        <div class="card-body">
                            <div class="position-relative mb-3">
                                <input type="text" x-model="productQuery" @input="searchProducts"
                                       placeholder="Cari produk untuk ditambahkan..." class="form-control" autocomplete="off">
                                <div x-show="productResults.length > 0" class="list-group position-absolute w-100" style="z-index: 20; max-height: 240px; overflow-y: auto;">
                                    <template x-for="p in productResults" :key="p.id">
                                        <a href="#" @click.prevent="!p.out_of_stock && addItem(p)"
                                           :class="p.out_of_stock ? 'disabled bg-light' : ''"
                                           class="list-group-item list-group-item-action d-flex justify-content-between">
                                            <span>
                                                <span x-text="p.nama"></span>
                                                <template x-if="!p.is_jasa">
                                                    <small class="ml-1" :class="p.out_of_stock ? 'text-danger font-weight-bold' : 'text-muted'"
                                                           x-text="p.out_of_stock ? '(Stok habis)' : '(Stok: ' + p.stock + ')'"></small>
                                                </template>
                                            </span>
                                            <span class="text-muted" x-text="'Rp ' + priceForTier(p).toLocaleString('id-ID')"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>

                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Harga</th>
                                        <th class="text-right">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td>
                                                <span x-text="item.nama"></span>
                                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                            </td>
                                            <td class="text-right">
                                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" class="form-control form-control-sm text-right" style="width: 70px; display: inline-block;">
                                            </td>
                                            <td class="text-right">
                                                <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" class="form-control form-control-sm text-right" style="width: 110px; display: inline-block;">
                                            </td>
                                            <td class="text-right" x-text="'Rp ' + (item.quantity * item.unit_price).toLocaleString('id-ID')"></td>
                                            <td><button type="button" @click="items.splice(index, 1)" class="btn btn-sm btn-outline-danger">Hapus</button></td>
                                        </tr>
                                    </template>
                                    <tr x-show="items.length === 0">
                                        <td colspan="5" class="text-center text-muted">Belum ada item, cari produk di atas.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-right font-weight-bold h5 pt-2 border-top" x-text="'Total: Rp ' + total.toLocaleString('id-ID')"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Simpan sebagai Draft</button>
                </form>
            </div>
        </div>
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
                selectedTechnicians: [],
                manualFee: false,
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
                        fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}&branch_id=${this.branchId}`)
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
                toggleTechnician(techId) {
                    const idx = this.selectedTechnicians.indexOf(techId);
                    if (idx === -1) this.selectedTechnicians.push(techId);
                    else this.selectedTechnicians.splice(idx, 1);

                    if (this.selectedTechnicians.length > 1) this.manualFee = true;
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
</x-admin-layout>