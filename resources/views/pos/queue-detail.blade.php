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

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8"
         x-data="queueDetail({{ $workOrder->id }})">

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
                        <div @click="submitAddItem(p)" class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b flex justify-between">
                            <span x-text="p.nama"></span>
                            <span class="text-gray-500" x-text="'Rp ' + parseFloat(p['{{ $workOrder->customer_price_tier }}'] ?? p.harga_jual).toLocaleString('id-ID')"></span>
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

        @if ($workOrder->stage === 'draft')
    <form method="POST" action="{{ route('pos.queue.process', $workOrder) }}">
        @csrf
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded">Mulai Proses (Masuk Antrian)</button>
    </form>
@elseif ($workOrder->stage === 'queue')
    <a href="{{ route('pos.payment', $workOrder) }}" class="inline-block bg-green-600 text-white px-6 py-2 rounded">Lanjut ke Pembayaran</a>
@else
    <p class="text-green-700 font-medium">Transaksi ini sudah selesai dan lunas.</p>
@endif
    </div>

    @push('scripts')
    <script>
        function queueDetail(workOrderId) {
            return {
                productQuery: '', productResults: [], _debounce: null,
                searchProducts() {
                    clearTimeout(this._debounce);
                    if (this.productQuery.length < 2) { this.productResults = []; return; }
                    this._debounce = setTimeout(() => {
                        fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}`)
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
            }
        }
    </script>
    @endpush
</x-app-layout>