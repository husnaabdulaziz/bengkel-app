<x-admin-layout title="Pembayaran">

    <div x-data="{
             discountType: '',
             discountValue: 0,
             subtotal: {{ $workOrder->items->sum('subtotal') }},
             get discountAmount() {
                 if (!this.discountType || !this.discountValue) return 0;
                 return this.discountType === 'percent' ? this.subtotal * (this.discountValue / 100) : parseFloat(this.discountValue);
             },
             get total() { return Math.max(this.subtotal - this.discountAmount, 0); }
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
                            <div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="pay_tunai" name="payment_method" value="tunai" class="custom-control-input" required>
                                    <label class="custom-control-label" for="pay_tunai">Tunai</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="pay_transfer" name="payment_method" value="transfer" class="custom-control-input">
                                    <label class="custom-control-label" for="pay_transfer">Transfer</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="pay_debit" name="payment_method" value="debit" class="custom-control-input">
                                    <label class="custom-control-label" for="pay_debit">Debit</label>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between"><span>Subtotal</span><span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span></div>
                            <div class="d-flex justify-content-between text-danger"><span>Diskon</span><span x-text="'- Rp ' + discountAmount.toLocaleString('id-ID')"></span></div>
                            <div class="d-flex justify-content-between font-weight-bold h5"><span>Total</span><span x-text="'Rp ' + total.toLocaleString('id-ID')"></span></div>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg">Konfirmasi Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>