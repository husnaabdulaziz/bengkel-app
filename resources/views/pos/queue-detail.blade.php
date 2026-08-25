<x-admin-layout title="Detail Servis">

    <div class="row" x-data="queueDetail({{ $workOrder->id }}, {{ json_encode($assignedTechnicianIds) }}, {{ $currentManualFee ? 'true' : 'false' }})">
        <div class="col-lg-3 order-2 order-lg-1">
            @include('pos.partials.orders-sidebar')
        </div>

        <div class="col-lg-9 order-1 order-lg-2">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-0">{{ $workOrder->invoice_number ?? 'Detail Servis' }} — {{ $workOrder->customer->nama }}</h4>
                    <a href="{{ route('pos.queue') }}" class="text-muted small">← Kembali ke Daftar</a>
                </div>
                @if ($workOrder->stage === 'completed')
                    <button type="button" onclick="printInvoice()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print Invoice
                    </button>
                @endif
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><strong>Nama:</strong> {{ $workOrder->customer->nama }}</div>
                        <div class="col-md-6"><strong>Telpon:</strong> {{ $workOrder->customer->telpon }}</div>
                        <div class="col-md-6"><strong>Plat Nomor:</strong> {{ $workOrder->customer->plat_nomor }}</div>
                        <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($workOrder->stage) }}</div>
                        @if ($workOrder->stage === 'completed')
                            <div class="col-md-6"><strong>Metode Bayar:</strong> {{ ucfirst($workOrder->payment_method) }}</div>
                            <div class="col-md-6"><strong>Dibayar:</strong> {{ $workOrder->paid_at?->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($workOrder->stage !== 'completed')
            <div class="card">
                <div class="card-header"><h3 class="card-title">Tambah Item</h3></div>
                <div class="card-body">
                    <div class="position-relative mb-2">
                        <input type="text" x-model="productQuery" @input="searchProducts" placeholder="Cari produk..." class="form-control" autocomplete="off">
                        <div x-show="productResults.length > 0" class="list-group position-absolute w-100" style="z-index: 20; max-height: 240px; overflow-y: auto;">
                            <template x-for="p in productResults" :key="p.id">
                                <a href="#" @click.prevent="!p.out_of_stock && submitAddItem(p)"
                                   :class="p.out_of_stock ? 'disabled bg-light' : ''"
                                   class="list-group-item list-group-item-action d-flex justify-content-between">
                                    <span>
                                        <span x-text="p.nama"></span>
                                        <template x-if="!p.is_jasa">
                                            <small class="ml-1" :class="p.out_of_stock ? 'text-danger font-weight-bold' : 'text-muted'"
                                                   x-text="p.out_of_stock ? '(Stok habis)' : '(Stok: ' + p.stock + ')'"></small>
                                        </template>
                                    </span>
                                    <span class="text-muted" x-text="'Rp ' + parseFloat(p['{{ $workOrder->customer_price_tier }}'] ?? p.harga_jual).toLocaleString('id-ID')"></span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <form id="add-item-form" method="POST" action="{{ route('pos.queue.add-item', $workOrder) }}" class="d-none">
                        @csrf
                        <input type="hidden" name="product_id" x-ref="product_id">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="unit_price" x-ref="unit_price">
                    </form>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                                @if ($workOrder->stage !== 'completed')
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workOrder->items as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    @if ($workOrder->stage !== 'completed')
                                        <td>
                                            <form method="POST" action="{{ route('pos.queue.remove-item', [$workOrder, $item]) }}" onsubmit="return confirm('Hapus item ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada item.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-3 text-right font-weight-bold border-top">Total: Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>

            @if ($workOrder->stage !== 'completed')
            <div class="card">
                <div class="card-header"><h3 class="card-title">Mekanik yang Mengerjakan</h3></div>
                <div class="card-body">
                    <p class="text-muted small">Data ini hanya untuk pencatatan fee internal, tidak ditampilkan di invoice pelanggan.</p>
                    <form method="POST" action="{{ route('pos.queue.update-technicians', $workOrder) }}">
                        @csrf
                        <div class="d-flex flex-wrap mb-3" style="gap: 1rem;">
                            @foreach ($technicians as $tech)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edittech-{{ $tech->id }}" name="technician_ids[]" value="{{ $tech->id }}" @change="toggleTechnician({{ $tech->id }})" :checked="selectedTechnicians.includes({{ $tech->id }})">
                                    <label class="custom-control-label" for="edittech-{{ $tech->id }}">{{ $tech->inisial ?? $tech->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="editManualFee" name="manual_fee" value="1" x-model="manualFee" :disabled="selectedTechnicians.length > 1">
                            <label class="custom-control-label" for="editManualFee">Input Fee Manual</label>
                        </div>
                        <p class="text-muted small mb-3" x-show="selectedTechnicians.length > 1">Fee wajib diisi manual karena lebih dari 1 mekanik dipilih.</p>
                        <button type="submit" class="btn btn-secondary btn-sm">Simpan Mekanik</button>
                    </form>
                </div>
            </div>
            @endif

            @if ($workOrder->stage === 'draft')
                <form method="POST" action="{{ route('pos.queue.process', $workOrder) }}">
                    @csrf
                    <template x-for="techId in selectedTechnicians" :key="techId">
                        <input type="hidden" name="technician_ids[]" :value="techId">
                    </template>
                    <input type="hidden" name="manual_fee" :value="(manualFee || selectedTechnicians.length > 1) ? 1 : 0">
                    <button type="submit" class="btn btn-success btn-lg">Lanjut ke Pembayaran</button>
                </form>
            @elseif ($workOrder->stage === 'queue')
                <a href="{{ route('pos.payment', $workOrder) }}" class="btn btn-success btn-lg">Lanjut ke Pembayaran</a>
            @else
                <p class="text-success font-weight-bold">Transaksi ini sudah selesai dan lunas.</p>
            @endif

        </div>
    </div>

    <script>
        function printInvoice() {
            window.open(
                "{{ route('pos.invoice', $workOrder) }}",
                'invoicePopup',
                'width=420,height=650,scrollbars=yes'
            );
        }
    </script>

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
</x-admin-layout>