<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Fee Mekanik Manual</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8"
         x-data="{
             productQuery: '', productResults: [], selectedProduct: null,
             searchProducts() {
                 clearTimeout(this._t);
                 if (this.productQuery.length < 2) { this.productResults = []; return; }
                 this._t = setTimeout(() => {
                     fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}`)
                         .then(r => r.json())
                         .then(data => this.productResults = data);
                 }, 300);
             },
             selectProduct(p) {
                 this.selectedProduct = p;
                 this.productQuery = p.nama;
                 this.productResults = [];
             }
         }">

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('technician-manual-fees.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Transaksi</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="border rounded px-3 py-2 w-full">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Nama Mekanik</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($technicians as $tech)
                        <label class="flex items-center gap-2 border rounded px-3 py-2 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                            <input type="radio" name="user_id" value="{{ $tech->id }}" required class="text-blue-600">
                            <span class="text-sm">
                                <span class="font-semibold">{{ $tech->inisial ?? '-' }}</span>
                                <span class="text-gray-500">- {{ $tech->name }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <label class="block text-sm font-medium mb-1">Nama Produk</label>
                <input type="text" x-model="productQuery" @input="searchProducts" placeholder="Cari produk..."
                       class="border rounded px-3 py-2 w-full" autocomplete="off">
                <input type="hidden" name="product_id" x-model="selectedProduct ? selectedProduct.id : ''">
                <div x-show="productResults.length > 0" class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-60 overflow-y-auto">
                    <template x-for="p in productResults" :key="p.id">
                        <div @click="selectProduct(p)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b" x-text="p.nama"></div>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nominal Fee</label>
                <input type="number" step="0.01" name="fee_amount" required min="0" class="border rounded px-3 py-2 w-full">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Keterangan</label>
                <textarea name="notes" rows="3" class="border rounded px-3 py-2 w-full"></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan</button>
        </form>
    </div>
</x-app-layout>