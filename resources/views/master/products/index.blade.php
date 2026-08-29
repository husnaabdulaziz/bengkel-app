<x-admin-layout title="Produk">

    <div x-data="productList()" x-init="load()">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap: 0.5rem;">
                    <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                        <input type="text" x-model="search" @input.debounce.400ms="page = 1; load()"
                               placeholder="Cari nama produk..." class="form-control" style="width: 220px;">
                        <select x-model="categoryId" @change="page = 1; load()" class="form-control" style="width: 160px;">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                        <select x-model="brandId" @change="page = 1; load()" class="form-control" style="width: 160px;">
                            <option value="">Semua Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->nama }}</option>
                            @endforeach
                        </select>
                        <select x-model="perPage" @change="page = 1; load()" class="form-control" style="width: 140px;">
                            <option value="10">10 / halaman</option>
                            <option value="25">25 / halaman</option>
                            <option value="50">50 / halaman</option>
                            <option value="100">100 / halaman</option>
                        </select>
                    </div>
                    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
                    <a href="{{ route('products.stickers') }}" class="btn btn-outline-secondary"><i class="fas fa-tags"></i> Cetak Sticker</a>
                    <a href="{{ route('products.import') }}" class="btn btn-success"><i class="fas fa-file-import"></i> Import Excel</a>
                    
                </div>

                <div>
                    <span class="text-muted small mr-3">Tampilkan kolom tambahan:</span>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showJualJasa" class="mr-1"> Harga Bawa</label>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showOnline" class="mr-1"> Harga Online</label>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showOjol" class="mr-1"> Harga Ojol</label>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showLokasi" class="mr-1"> Lokasi Rak</label>
                    <label class="d-inline-flex align-items-center"><input type="checkbox" x-model="showModel" class="mr-1"> Ukuran & Model</label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>                            
                            <th x-show="showModel" x-cloak>Ukuran</th>
                            <th x-show="showModel" x-cloak>Model</th>
                            <th class="d-none d-md-table-cell">Kategori</th>
                            <th class="d-none d-md-table-cell">Brand</th>
                            <th class="text-right">Stock</th>
                            <th class="text-right">Harga Jual</th>
                            <th class="text-right" x-show="showJualJasa" x-cloak>Harga Bawa</th>
                            <th class="text-right" x-show="showOnline" x-cloak>Online</th>
                            <th class="text-right" x-show="showOjol" x-cloak>Ojol</th>
                            <th x-show="showLokasi" x-cloak>Lokasi</th>
                            <th style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr><td colspan="9" class="text-center text-muted py-4">Memuat...</td></tr>
                        </template>
                        <template x-if="!loading && items.length === 0">
                            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada produk yang cocok.</td></tr>
                        </template>
                        <template x-for="p in items" :key="p.id">
                            <tr>
                                <td>
                                    <a href="#" @click.prevent="toggleExpand(p.id)" class="d-md-none text-dark" style="text-decoration: none;">
                                        <i class="fas fa-chevron-right mr-1" :class="{'fa-rotate-90': expanded[p.id]}" style="font-size: 0.7rem; transition: transform 0.15s;"></i>
                                    </a>
                                    <span x-text="p.nama"></span>
                                    <span x-show="p.is_jasa" class="badge badge-purple">Jasa</span>
                                    <span x-show="p.status === 'inactive'" class="badge badge-secondary">Nonaktif</span>
                                    <div class="d-md-none small text-muted mt-1" x-show="expanded[p.id]" x-cloak>
                                        Kategori: <span x-text="p.category || '-'"></span><br>
                                        Brand: <span x-text="p.brand || '-'"></span>
                                    </div>
                                </td>
                                
                                <td x-show="showModel" x-cloak x-text="p.ukuran || '-'"></td>
                                <td x-show="showModel" x-cloak x-text="p.model_name || '-'"></td>
                                <td class="d-none d-md-table-cell" x-text="p.category || '-'"></td>
                                <td class="d-none d-md-table-cell" x-text="p.brand || '-'"></td>
                                <td class="text-right">
                                    <template x-if="p.is_jasa">
                                        <span class="text-muted">-</span>
                                    </template>
                                    <template x-if="!p.is_jasa">
                                        <span :class="p.stock_total <= 0 ? 'text-danger font-weight-bold' : ''" x-text="p.stock_total"></span>
                                    </template>
                                </td>
                                <td class="text-right" x-text="p.harga_jual"></td>
                                <td class="text-right" x-show="showJualJasa" x-cloak x-text="p.harga_jual_jasa"></td>
                                <td class="text-right" x-show="showOnline" x-cloak x-text="p.harga_online"></td>
                                <td class="text-right" x-show="showOjol" x-cloak x-text="p.harga_ojol"></td>
                                <td x-show="showLokasi" x-cloak x-text="p.lokasi_rak || '-'"></td>
                                <td class="text-nowrap">
                                    <a :href="p.edit_url" class="btn btn-outline-primary" title="Edit" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-edit"></i></a>
                                    @can('delete_produk')
                                        <button type="button" @click="deleteProduct(p)" class="btn btn-outline-danger" title="Hapus" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                                    @endcan
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-muted small" x-text="`Menampilkan ${items.length} dari ${totalItems} data`"></span>
            <nav x-show="lastPage > 1">
                <ul class="pagination mb-0">
                    <li class="page-item" :class="page === 1 ? 'disabled' : ''">
                        <a href="#" class="page-link" @click.prevent="page = Math.max(1, page - 1); load()">‹</a>
                    </li>
                    <template x-for="p in pageNumbers" :key="p">
                        <li class="page-item" :class="page === p ? 'active' : ''">
                            <a href="#" class="page-link" @click.prevent="page = p; load()" x-text="p"></a>
                        </li>
                    </template>
                    <li class="page-item" :class="page === lastPage ? 'disabled' : ''">
                        <a href="#" class="page-link" @click.prevent="page = Math.min(lastPage, page + 1); load()">›</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    @push('scripts')
    <script>
        function productList() {
            return {
                search: '', categoryId: '', brandId: '', perPage: 10, page: 1,
                showJualJasa: false, showOnline: false, showOjol: false, showLokasi: false, showModel: false,
                items: [], lastPage: 1, totalItems: 0, loading: false,
                expanded: {},

                toggleExpand(id) {
                    this.expanded[id] = !this.expanded[id];
                },

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
</x-admin-layout>