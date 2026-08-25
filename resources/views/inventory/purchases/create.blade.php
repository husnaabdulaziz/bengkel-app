<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catat Pembelian dari vendor</h2>
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
              x-data="{
                  items: [{ product_id: '', quantity: 1, price_per_unit: 0 }],
                  products: {{ $products->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama, 'satuan' => $p->satuan, 'harga_modal' => (float) $p->harga_modal])->toJson() }},
                  addItem() { this.items.push({ product_id: '', quantity: 1, price_per_unit: 0 }) },
                  removeItem(i) { this.items.splice(i, 1) },
                  fillPrice(i) {
                      const p = this.products.find(p => p.id == this.items[i].product_id);
                      if (p) this.items[i].price_per_unit = p.harga_modal;
                  },
                  get total() {
                      return this.items.reduce((sum, it) => sum + (it.quantity * it.price_per_unit), 0);
                  }
              }"
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
                    <select name="supplier_id" required class="border rounded px-3 py-2 w-full">
                        <option value="">- Pilih Vendor -</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
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
                    <div class="col-span-5">
                        <label class="block text-xs mb-1">Produk</label>
                        <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="fillPrice(index)" required class="border rounded px-2 py-2 w-full text-sm">
                            <option value="">- Pilih Produk -</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.nama + ' (' + p.satuan + ')'"></option>
                            </template>
                        </select>
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
                        <button type="button" @click="removeItem(index)" class="text-red-600 text-sm">Hapus</button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem()" class="text-blue-600 text-sm">+ Tambah Baris Item</button>

            <div class="text-right font-semibold text-lg pt-2 border-t" x-text="'Total: Rp ' + total.toLocaleString('id-ID')"></div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan Pembelian</button>
        </form>
    </div>
</x-app-layout>