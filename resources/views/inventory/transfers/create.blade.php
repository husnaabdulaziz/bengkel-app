<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan Transfer Stock</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-transfers.store') }}"
              x-data="{
                  items: [{ product_id: '', qty_requested: 1 }],
                  addItem() { this.items.push({ product_id: '', qty_requested: 1 }) },
                  removeItem(i) { this.items.splice(i, 1) },
              }"
              class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Dari Cabang</label>
                    <select name="from_branch_id" required class="border rounded px-3 py-2 w-full">
                        <option value="">- Pilih -</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ke Cabang</label>
                    <select name="to_branch_id" required class="border rounded px-3 py-2 w-full">
                        <option value="">- Pilih -</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>
            <h3 class="font-semibold">Item yang Diminta</h3>

            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-12 gap-2 items-end">
                    <div class="col-span-8">
                        <label class="block text-xs mb-1">Produk</label>
                        <select :name="`items[${index}][product_id]`" x-model="item.product_id" required class="border rounded px-2 py-2 w-full text-sm">
                            <option value="">- Pilih Produk -</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->nama }} ({{ $product->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-xs mb-1">Qty Diminta</label>
                        <input type="number" :name="`items[${index}][qty_requested]`" x-model.number="item.qty_requested" min="1" required class="border rounded px-2 py-2 w-full text-sm">
                    </div>
                    <div class="col-span-1">
                        <button type="button" @click="removeItem(index)" class="text-red-600 text-sm">Hapus</button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem()" class="text-blue-600 text-sm">+ Tambah Baris Item</button>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Ajukan Transfer</button>
        </form>
    </div>
</x-app-layout>