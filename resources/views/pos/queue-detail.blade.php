<x-app-layout>
    <x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $workOrder->invoice_number ?? 'Detail Servis' }} — {{ $workOrder->customer->nama }}
            </h2>
            <a href="{{ route('pos.queue') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Daftar</a>
        </div>
        @if ($workOrder->stage === 'completed')
            <button type="button" onclick="printInvoice()" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h14z" />
                </svg>
                Print Invoice
            </button>
        @endif
    </div>
</x-slot>

<script>
    function printInvoice() {
        window.open(
            "{{ route('pos.invoice', $workOrder) }}",
            'invoicePopup',
            'width=420,height=650,scrollbars=yes'
        );
    }
</script>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-4 items-start">
    <div class="order-2 lg:order-1 w-full lg:w-auto">
        @include('pos.partials.orders-sidebar')
    </div>

    <div class="order-1 lg:order-2 flex-1 min-w-0 w-full"
         x-data="queueDetail({{ $workOrder->id }}, {{ json_encode($assignedTechnicianIds) }}, {{ $currentManualFee ? 'true' : 'false' }})">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-4">
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><strong>Nama:</strong> {{ $workOrder->customer->nama }}</div>
                <div><strong>Telpon:</strong> {{ $workOrder->customer->telpon }}</div>
                <div><strong>Plat Nomor:</strong> {{ $workOrder->customer->plat_nomor }}</div>
                <div><strong>Status:</strong> {{ ucfirst($workOrder->stage) }}</div>
                @if ($workOrder->stage === 'completed')
                    <div><strong>Metode Bayar:</strong> {{ ucfirst($workOrder->payment_method) }}</div>
                    <div><strong>Dibayar:</strong> {{ $workOrder->paid_at?->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>
        
        @if ($workOrder->stage !== 'completed')
        <div class="bg-white p-6 rounded shadow mb-4">
            <h3 class="font-semibold mb-3">Tambah Item</h3>
            <div class="relative mb-3">
                <input type="text" x-model="productQuery" @input="searchProducts" placeholder="Cari produk..." class="border rounded px-3 py-2 w-full" autocomplete="off">
                <div x-show="productResults.length > 0" class="absolute z-10 bg-white border rounded shadow w-full mt-1 max-h-60 overflow-y-auto">
                    <template x-for="p in productResults" :key="p.id">
                        <div @click="!p.out_of_stock && addItem(p)"
                            :class="p.out_of_stock ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-gray-100 cursor-pointer'"
                            class="p-2 text-sm border-b flex justify-between items-center">
                            <div>
                                <span x-text="p.nama"></span>
                                <template x-if="!p.is_jasa">
                                    <span class="text-xs ml-1" :class="p.out_of_stock ? 'text-red-600 font-semibold' : 'text-gray-400'"
                                        x-text="p.out_of_stock ? '(Stok habis)' : '(Stok: ' + p.stock + ')'"></span>
                                </template>
                            </div>
                            <span class="text-gray-500" x-text="'Rp ' + priceForTier(p).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                </div>
            </div>

            <form id="add-item-form" method="POST" action="{{ route('pos.queue.add-item', $workOrder) }}" class="hidden">
                @csrf
                <input type="hidden" name="product_id" x-ref="product_id">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="unit_price" x-ref="unit_price">
            </form>
        </div>
        @endif
        <div class="bg-white rounded shadow overflow-hidden mb-4">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Item</th>
                        <th class="p-3 text-right">Qty</th>
                        <th class="p-3 text-right">Harga</th>
                        <th class="p-3 text-right">Subtotal</th>
                        @if ($workOrder->stage !== 'completed')
                            <th class="p-3 w-20">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrder->items as $item)
                        <tr class="border-t">
                            <td class="p-3">{{ $item->item_name }}</td>
                            <td class="p-3 text-right">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            @if ($workOrder->stage !== 'completed')
                            <td class="p-3">
                                <form method="POST" action="{{ route('pos.queue.remove-item', [$workOrder, $item]) }}" onsubmit="return confirm('Hapus item ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-xs">Hapus</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500 text-center">Belum ada item.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3 text-right font-semibold border-t">Total: Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</div>
        </div>
        @if ($workOrder->stage !== 'completed')
        <div class="bg-white p-6 rounded shadow mb-4">
            <h3 class="font-semibold mb-3">Mekanik yang Mengerjakan</h3>
            <p class="text-xs text-gray-400 mb-2">Data ini hanya untuk pencatatan fee internal, tidak ditampilkan di invoice pelanggan.</p>

            <form method="POST" action="{{ route('pos.queue.update-technicians', $workOrder) }}">
                @csrf
                <div class="flex flex-wrap gap-4 mb-3">
                    @foreach ($technicians as $tech)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="technician_ids[]" value="{{ $tech->id }}" @change="toggleTechnician({{ $tech->id }})" :checked="selectedTechnicians.includes({{ $tech->id }})">
                            {{ $tech->inisial ?? $tech->name }}
                        </label>
                    @endforeach
                </div>
                <label class="flex items-center gap-2 text-sm mb-3">
                    <input type="checkbox" x-model="manualFee" :disabled="selectedTechnicians.length > 1">
                    Input Fee Manual
                </label>
                <input type="hidden" name="manual_fee" :value="(manualFee || selectedTechnicians.length > 1) ? 1 : 0">
                <p class="text-xs text-gray-400 mb-3" x-show="selectedTechnicians.length > 1">
                    Fee wajib diisi manual karena lebih dari 1 mekanik dipilih.
                </p>
                <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded text-sm">Simpan Mekanik</button>
            </form>
        </div>
        @else
            @if ($assignedTechnicians->isNotEmpty())
                <div class="bg-white p-4 rounded shadow mb-4 flex items-center gap-2">
                    <span class="text-sm text-gray-500">Mekanik:</span>
                    @foreach ($assignedTechnicians as $tech)
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">{{ $tech->inisial ?? $tech->name }}</span>
                    @endforeach
                </div>
            @endif
        @endif
        @if ($workOrder->stage === 'draft')
        <form method="POST" action="{{ route('pos.queue.process', $workOrder) }}">
            @csrf
            <template x-for="techId in selectedTechnicians" :key="techId">
                <input type="hidden" name="technician_ids[]" :value="techId">
            </template>
            <input type="hidden" name="manual_fee" :value="(manualFee || selectedTechnicians.length > 1) ? 1 : 0">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Lanjut ke Pembayaran</button>
        </form>
@elseif ($workOrder->stage === 'queue')
    <a href="{{ route('pos.payment', $workOrder) }}" class="inline-block bg-green-600 text-white px-6 py-2 rounded">Lanjut ke Pembayaran</a>
@else
    <p class="text-green-700 font-medium">Transaksi ini sudah selesai dan lunas.</p>
@endif
    </div>
    </div>
    @push('scripts')
    <script>
        function queueDetail(workOrderId, initialTechnicianIds, initialManualFee) {
            return {
                productQuery: '', productResults: [], _debounce: null,
                selectedTechnicians: initialTechnicianIds,
                manualFee: initialManualFee,

                searchProducts() {
                    clearTimeout(this._debounce);
                    if (this.productQuery.length < 2) { this.productResults = []; return; }
                    this._debounce = setTimeout(() => {
                        fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}&branch_id={{ $workOrder->branch_id }}`)
                            .then(r => r.json())
                            .then(data => this.productResults = data);
                    }, 300);
                },

                submitAddItem(p) {
                    const tier = '{{ $workOrder->customer_price_tier }}';
                    const price = parseFloat(p[tier] ?? p.harga_jual);
                    this.$refs.product_id.value = p.id;
                    this.$refs.unit_price.value = price;
                    document.getElementById('add-item-form').submit();
                },

                toggleTechnician(techId) {
                    const idx = this.selectedTechnicians.indexOf(techId);
                    if (idx === -1) this.selectedTechnicians.push(techId);
                    else this.selectedTechnicians.splice(idx, 1);

                    if (this.selectedTechnicians.length > 1) this.manualFee = true;
                },
            }
        }
    </script>
    @endpush
</x-app-layout>