<x-app-layout>
    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8"
     x-data="posQueue()" x-init="load()">

            <div x-show="toast" x-transition class="mb-4 p-3 bg-green-100 text-green-700 rounded" x-text="toast" x-cloak></div>

            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2 text-gray-800">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h1m4-6h10a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                    <h1 class="text-xl font-bold">POS (Kasir)</h1>
                </div>
                <div class="flex items-center gap-3">
                    @if ($branches->count() > 1)
                        <div class="flex items-center gap-1 bg-white border text-gray-700 text-sm px-3 py-2 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            {{ $branches->first()->nama_cabang }}
                        </div>
                    @endif
                    <a href="{{ route('pos.create') }}" class="flex items-center gap-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Transaksi
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mb-4">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" @input.debounce.400ms="page = 1; load()" placeholder="Cari no invoice, pelanggan, atau nomor plat..."
                       class="w-full bg-white border border-gray-200 text-gray-800 placeholder-gray-400 rounded pl-10 pr-4 py-3 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 shadow-sm">
            </div>

            <!-- Tabs -->
            <div class="flex gap-2 mb-4 flex-wrap">
                <template x-for="tab in tabs" :key="tab.key">
                    <button type="button" @click="status = tab.key; page = 1; load()"
                        :class="status === tab.key ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'"
                        class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border">
                        <span x-text="tab.label"></span>
                        <span :class="status === tab.key ? 'bg-white/20' : 'bg-gray-100 text-gray-500'" class="text-xs px-1.5 py-0.5 rounded-full" x-text="counts[tab.key] ?? 0"></span>
                    </button>
                </template>
            </div>

            <!-- List -->
            <div class="space-y-2">
                <template x-if="loading">
                    <div class="text-center text-gray-400 py-12">Memuat...</div>
                </template>
                <template x-if="!loading && items.length === 0">
                    <div class="text-center text-gray-400 py-12">Tidak ada transaksi.</div>
                </template>
                <template x-for="wo in items" :key="wo.id">
                    <div class="flex items-center justify-between bg-white border border-gray-200 hover:border-orange-300 hover:shadow-sm rounded-lg p-4 transition">
                        <a :href="wo.show_url" class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                <span class="text-gray-800 font-semibold text-sm" x-text="wo.invoice_number || ('DRAFT-' + wo.id)"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="badgeColor(wo.stage)" x-text="badgeLabel(wo.stage)"></span>
                            </div>
                            <div class="flex items-center gap-4 text-gray-500 text-xs">
                                <span x-text="wo.customer_nama"></span>
                                <span x-show="wo.plat_nomor" x-text="wo.plat_nomor"></span>
                                <span x-text="wo.created_at"></span>
                            </div>
                        </a>
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-sm" :class="wo.stage === 'completed' ? 'text-green-600' : 'text-red-500'"
                                  x-text="(wo.stage === 'completed' ? '' : '- ') + 'Rp ' + Number(wo.total_amount).toLocaleString('id-ID')"></span>
                            <button x-show="wo.can_delete" type="button" @click="deleteOrder(wo)" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                            <a :href="wo.show_url" class="text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center gap-2 mt-4" x-show="lastPage > 1">
                <template x-for="p in lastPage" :key="p">
                    <button type="button" @click="page = p; load()"
                        :class="page === p ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border'"
                        class="px-3 py-1 rounded text-sm" x-text="p"></button>
                </template>
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
                status: 'all', search: '', page: 1,
                counts: {}, items: [], lastPage: 1, loading: false, toast: '',
                tabs: [
                    { key: 'all', label: 'Semua' },
                    { key: 'draft', label: 'Draft' },
                    { key: 'queue', label: 'Antrian' },
                    { key: 'completed', label: 'Selesai' },
                ],
                badgeColor(stage) {
                    return { draft: 'bg-gray-100 text-gray-600', queue: 'bg-blue-100 text-blue-700', completed: 'bg-green-100 text-green-700' }[stage] ?? 'bg-gray-100 text-gray-600';
                },
                badgeLabel(stage) {
                    return { draft: 'Draft', queue: 'Antrian', completed: 'Selesai' }[stage] ?? stage;
                },
                load() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    if (this.status !== 'all') params.set('status', this.status);
                    if (this.search) params.set('search', this.search);
                    if (this.page > 1) params.set('page', this.page);

                    fetch(`{{ route('pos.queue.data') }}?${params.toString()}`)
                        .then(r => r.json())
                        .then(data => {
                            this.counts = data.counts;
                            this.items = data.items;
                            this.lastPage = data.last_page;
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
                        this.toast = res.message;
                        setTimeout(() => this.toast = '', 3000);
                        this.load();
                    });
                },
            }
        }
    </script>
    @endpush
</x-app-layout>