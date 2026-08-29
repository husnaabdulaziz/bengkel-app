<x-admin-layout title="Pembayaran">

    <div class="flex-1 min-w-0 max-w-2xl"
        x-data="{
                discountType: '',
                discountValue: 0,
                subtotal: {{ $workOrder->items->sum('subtotal') }},
                checked: { tunai: true, transfer: false, debit: false },
                amounts: { tunai: {{ $workOrder->items->sum('subtotal') }}, transfer: 0, debit: 0 },
                get discountAmount() {
                    if (!this.discountType || !this.discountValue) return 0;
                    return this.discountType === 'percent' ? this.subtotal * (this.discountValue / 100) : parseFloat(this.discountValue);
                },
                get total() { return Math.max(this.subtotal - this.discountAmount, 0); },
                get paymentsSum() {
                    return Object.keys(this.checked).filter(m => this.checked[m]).reduce((sum, m) => sum + (parseFloat(this.amounts[m]) || 0), 0);
                },
                get remaining() { return Math.round((this.total - this.paymentsSum) * 100) / 100; },
                onToggleMethod(method) {
                    if (this.checked[method]) {
                        if (!this.amounts[method]) { this.amounts[method] = Math.max(this.remaining, 0); }
                    } else {
                        this.amounts[method] = 0;
                    }
                }
            }">
        <div class="row">
            <div class="col-lg-3 order-2 order-lg-1">
                @include('pos.partials.orders-sidebar')
            </div>

            <div class="col-lg-9 order-1 order-lg-2" style="max-width: 700px;">
                <p class="text-muted mb-2">{{ $workOrder->customer->nama }}</p>

                @if ($assignedTechnicians->isNotEmpty())
                    <div class="mb-3">
                        <span class="text-muted small">Mekanik:</span>
                        @foreach ($assignedTechnicians as $tech)
                            <span class="badge badge-info">{{ $tech->inisial ?? $tech->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="card">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr><th>Item</th><th class="text-right">Qty</th><th class="text-right">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrder->items as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-right">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <form method="POST" action="{{ route('pos.payment.confirm', $workOrder) }}" class="card">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label>Jenis Diskon (opsional)</label>
                                <select name="discount_type" x-model="discountType" class="form-control">
                                    <option value="">Tanpa Diskon</option>
                                    <option value="percent">Persen (%)</option>
                                    <option value="fixed">Nominal (Rp)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nilai Diskon</label>
                                <input type="number" step="0.01" name="discount_value" x-model.number="discountValue" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>

                            <template x-for="method in ['tunai', 'transfer', 'debit']" :key="method">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="custom-control custom-checkbox mr-3" style="width: 110px;">
                                        <input type="checkbox" class="custom-control-input" :id="'chk-' + method"
                                            x-model="checked[method]" @change="onToggleMethod(method)">
                                        <label class="custom-control-label text-capitalize" :for="'chk-' + method" x-text="method"></label>
                                    </div>
                                    <input type="hidden" :name="`payments[${method}][method]`" :value="method" :disabled="!checked[method]">
                                    <input type="number" step="0.01" :name="`payments[${method}][amount]`" x-model.number="amounts[method]"
                                        :disabled="!checked[method]" x-show="checked[method]" x-cloak
                                        min="0" class="form-control form-control-sm" style="max-width: 200px;" placeholder="Nominal">
                                </div>
                            </template>

                            <div class="mt-2 small">
                                <span>Total Dibayar: Rp <span x-text="paymentsSum.toLocaleString('id-ID')"></span></span>
                                <span class="ml-3" :class="remaining === 0 ? 'text-success font-weight-bold' : 'text-danger font-weight-bold'">
                                    Sisa: Rp <span x-text="remaining.toLocaleString('id-ID')"></span>
                                </span>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between"><span>Subtotal</span><span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span></div>
                            <div class="d-flex justify-content-between text-danger"><span>Diskon</span><span x-text="'- Rp ' + discountAmount.toLocaleString('id-ID')"></span></div>
                            <div class="d-flex justify-content-between font-weight-bold h5"><span>Total</span><span x-text="'Rp ' + total.toLocaleString('id-ID')"></span></div>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg" :disabled="remaining !== 0">Konfirmasi Pembayaran</button>
                        <p class="text-danger text-center small mt-1" x-show="remaining !== 0">Total pembayaran harus pas dengan tagihan sebelum bisa disimpan.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>