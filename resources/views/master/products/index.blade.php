<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8"
         x-data="productList()" x-init="load()">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <div class="flex gap-2 flex-wrap">
                <input type="text" x-model="search" @input.debounce.400ms="page = 1; load()"
                       placeholder="Cari nama produk..." class="border rounded px-3 py-2 w-64">
                <select x-model="categoryId" @change="page = 1; load()" class="border rounded px-3 py-2">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
                <select x-model="brandId" @change="page = 1; load()" class="border rounded px-3 py-2">
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->nama }}</option>
                    @endforeach
                </select>
                <select x-model="perPage" @change="page = 1; load()" class="border rounded px-3 py-2">
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                </select>
            </div>
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Produk</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Brand</th>
                        <th class="p-3 text-right">Stock</th>
                        <th class="p-3 text-right">Harga Jual</th>
                        <th class="p-3 text-right">Jual+Jasa</th>
                        <th class="p-3 text-right">Online</th>
                        <th class="p-3 text-right">Ojol</th>
                        <th class="p-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr><td colspan="9" class="p-3 text-gray-400 text-center">Memuat...</td></tr>
                    </template>
                    <template x-if="!loading && items.length === 0">
                        <tr><td colspan="9" class="p-3 text-gray-500 text-center">Tidak ada produk yang cocok.</td></tr>
                    </template>
                    <template x-for="p in items" :key="p.id">
                        <tr class="border-t">
                            <td class="p-3">
                                <span x-text="p.nama"></span>
                                <span x-show="p.is_jasa" class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded ml-1">Jasa</span>
                            </td>
                            <td class="p-3" x-text="p.category || '-'"></td>
                            <td class="p-3" x-text="p.brand || '-'"></td>
                            <td class="p-3 text-right">
                                <template x-if="p.is_jasa">
                                    <span class="text-gray-400">-</span>
                                </template>
                                <template x-if="!p.is_jasa">
                                    <span :class="p.stock_total <= 0 ? 'text-red-600 font-semibold' : 'text-gray-700'" x-text="p.stock_total"></span>
                                </template>
                            </td>
                            <td class="p-3 text-right" x-text="p.harga_jual"></td>
                            <td class="p-3 text-right" x-text="p.harga_jual_jasa"></td>
                            <td class="p-3 text-right" x-text="p.harga_online"></td>
                            <td class="p-3 text-right" x-text="p.harga_ojol"></td>
                            <td class="p-3">
                                <a :href="p.edit_url" class="text-blue-600 text-sm">Edit</a>
                                <button type="button" @click="deleteProduct(p)" class="text-red-600 text-sm ml-2">Hapus</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-4">
    <span class="text-sm text-gray-500" x-text="`Menampilkan ${items.length} dari ${totalItems} data`"></span>
    <div class="flex gap-1" x-show="lastPage > 1">
        <button type="button" @click="page = Math.max(1, page - 1); load()" :disabled="page === 1"
            class="px-3 py-1 rounded text-sm border bg-white text-gray-600 disabled:opacity-40">‹</button>
        <template x-for="p in pageNumbers" :key="p">
            <button type="button" @click="page = p; load()"
                :class="page === p ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border'"
                class="px-3 py-1 rounded text-sm" x-text="p"></button>
        </template>
        <button type="button" @click="page = Math.min(lastPage, page + 1); load()" :disabled="page === lastPage"
            class="px-3 py-1 rounded text-sm border bg-white text-gray-600 disabled:opacity-40">›</button>
    </div>
</div>
    </div>

    @push('scripts')
    <script>
    function productList() {
        return {
            search: '', categoryId: '', brandId: '', perPage: 10, page: 1,
            items: [], lastPage: 1, totalItems: 0, loading: false,

            get pageNumbers() {
                const pages = [];
                const maxShown = 5;
                let startPage = Math.max(1, this.page - Math.floor(maxShown / 2));
                let endPage = Math.min(this.lastPage, startPage + maxShown - 1);
                startPage = Math.max(1, endPage - maxShown + 1);
                for (let i = startPage; i <= endPage; i++) pages.push(i);
                return pages;
            },

            load() {
                this.loading = true;
                const params = new URLSearchParams();
                if (this.search) params.set('search', this.search);
                if (this.categoryId) params.set('category_id', this.categoryId);
                if (this.brandId) params.set('brand_id', this.brandId);
                params.set('per_page', this.perPage);
                if (this.page > 1) params.set('page', this.page);

                fetch(`{{ route('products.data') }}?${params.toString()}`)
                    .then(r => r.json())
                    .then(data => {
                        this.items = data.items;
                        this.lastPage = data.last_page;
                        this.totalItems = data.total;
                        this.loading = false;
                    });
            },

            deleteProduct(p) {
                if (!confirm(`Hapus produk "${p.nama}"?`)) return;
                fetch(p.delete_url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                }).then(() => this.load());
            },
        }
    }
</script>
    @endpush
</x-app-layout>