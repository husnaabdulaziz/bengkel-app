<x-admin-layout title="POS (Kasir)">

    <div x-data="posQueue()" x-init="load()">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                @if ($branches->count() > 1)
                    <span class="badge badge-light border p-2"><i class="fas fa-store"></i> {{ $branches->first()->nama_cabang }}</span>
                @endif
            </div>
            <a href="{{ route('pos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>

        <!-- Search -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" x-model="search" @input.debounce.400ms="page = 1; load()"
                    placeholder="Cari no invoice, pelanggan, atau nomor plat..." class="form-control">
                <div class="input-group-append">
                    <select x-model="perPage" @change="page = 1; load()" class="form-control" style="max-width: 130px;">
                        <option value="10">10 / halaman</option>
                        <option value="20">20 / halaman</option>
                        <option value="50">50 / halaman</option>
                        <option value="100">100 / halaman</option>
                    </select>
                </div>
            </div>

        <!-- Status Tabs -->
        <ul class="nav nav-pills mb-3">
            <template x-for="tab in tabs" :key="tab.key">
                <li class="nav-item">
                    <a href="#" @click.prevent="status = tab.key; page = 1; load()"
                       class="nav-link" :class="status === tab.key ? 'active' : ''">
                        <span x-text="tab.label"></span>
                        <span class="badge" :class="status === tab.key ? 'badge-light' : 'badge-secondary'" x-text="counts[tab.key] ?? 0"></span>
                    </a>
                </li>
            </template>
        </ul>

        <!-- List -->
        <div class="card">
            <div class="card-body p-0">
                <template x-if="loading">
                    <div class="text-center text-muted py-5">Memuat...</div>
                </template>
                <template x-if="!loading && items.length === 0">
                    <div class="text-center text-muted py-5">Tidak ada transaksi.</div>
                </template>

                <div class="list-group list-group-flush">
                    <template x-for="wo in items" :key="wo.id">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <a :href="wo.show_url" class="text-dark flex-grow-1" style="text-decoration: none;">
                                <div class="mb-1">
                                    <i class="fas fa-wrench text-primary mr-1"></i>
                                    <strong x-text="wo.invoice_number || ('DRAFT-' + wo.id)"></strong>
                                    <span class="badge ml-1" :class="badgeColor(wo.stage)" x-text="badgeLabel(wo.stage)"></span>
                                </div>
                                <div class="text-muted small">
                                    <span x-text="wo.customer_nama"></span>
                                    <span x-show="wo.plat_nomor" x-text="' · ' + wo.plat_nomor"></span>
                                    <span x-text="' · ' + wo.created_at"></span>
                                </div>
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <span class="font-weight-bold" :class="wo.stage === 'completed' ? 'text-success' : 'text-danger'"
                                      x-text="(wo.stage === 'completed' ? '' : '- ') + 'Rp ' + Number(wo.total_amount).toLocaleString('id-ID')"></span>
                                <button x-show="wo.can_delete" type="button" @click="deleteOrder(wo)" class="btn btn-sm btn-outline-danger ml-2" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Pagination -->
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

    @if (session('print_invoice_id'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.open(
                    "{{ route('pos.invoice', session('print_invoice_id')) }}",
                    'invoicePopup',
                    'width=420,height=650,scrollbars=yes'
                );
            });
        </script>
    @endif

    @push('scripts')
    <script>
        function posQueue() {
            return {
                status: 'all', search: '', page: 1, perPage: 10,
                counts: {}, items: [], lastPage: 1, totalItems: 0, loading: false,
                                badgeLabel(stage) {
                    return { draft: 'Draft', queue: 'Antrian', completed: 'Selesai' }[stage] ?? stage;
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
                tabs: [
                    { key: 'all', label: 'Semua' },
                    { key: 'draft', label: 'Draft' },
                    { key: 'queue', label: 'Antrian' },
                    { key: 'completed', label: 'Selesai' },
                ],
                badgeColor(stage) {
                    return { draft: 'badge-warning', queue: 'badge-info', completed: 'badge-success' }[stage] ?? 'badge-secondary';
                },
                badgeLabel(stage) {
                    return { draft: 'Draft', queue: 'Antrian', completed: 'Selesai' }[stage] ?? stage;
                },
                load() {
                    this.loading = true;
                    const params = new URLSearchParams();
                        if (this.status !== 'all') params.set('status', this.status);
                        if (this.search) params.set('search', this.search);
                        params.set('per_page', this.perPage);
                        if (this.page > 1) params.set('page', this.page);

                    fetch(`{{ route('pos.queue.data') }}?${params.toString()}`)
                        .then(r => r.json())
                        .then(data => {
                            this.counts = data.counts;
                            this.items = data.items;
                            this.lastPage = data.last_page;
                            this.totalItems = data.total;
                            this.loading = false;
                        });
                },
                deleteOrder(wo) {
                    if (!confirm('Hapus transaksi ini? Data pelanggan yang tidak jadi transaksi juga akan ikut terhapus.')) return;
                    fetch(wo.delete_url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(res => {
                        this.load();
                    });
                },
            }
        }
    </script>
    @endpush
</x-admin-layout>