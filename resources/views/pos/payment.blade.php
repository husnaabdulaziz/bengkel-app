<x-admin-layout title="Pembayaran">

    <div class="flex-1 min-w-0 max-w-2xl"
        x-data="{
            discountType: '',
            discountValue: 0,
            subtotal: {{ $workOrder->items->sum('subtotal') }},
            payments: [{ method: 'tunai', amount: {{ $workOrder->items->sum('subtotal') }} }],
            get discountAmount() {
                if (!this.discountType || !this.discountValue) return 0;
                return this.discountType === 'percent' ? this.subtotal * (this.discountValue / 100) : parseFloat(this.discountValue);
            },
            get total() { return Math.max(this.subtotal - this.discountAmount, 0); },
            get paymentsSum() { return this.payments.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0); },
            get remaining() { return Math.round((this.total - this.paymentsSum) * 100) / 100; }
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
                            <template x-for="(pay, index) in payments" :key="index">
                                <div class="form-row align-items-center mb-2">
                                    <div class="col-5">
                                        <select :name="`payments[${index}][method]`" x-model="pay.method" required class="form-control form-control-sm">
                                            <option value="tunai">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="debit">Debit</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <input type="number" step="0.01" :name="`payments[${index}][amount]`" x-model.number="pay.amount" min="0" required class="form-control form-control-sm" placeholder="Nominal">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" @click="payments.splice(index, 1)" x-show="payments.length > 1" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="payments.push({ method: 'tunai', amount: 0 })" class="btn btn-sm btn-outline-secondary">+ Tambah Metode Bayar</button>

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