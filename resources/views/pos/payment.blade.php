<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pembayaran — {{ $workOrder->customer->nama }}</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8"
         x-data="{
             discountType: '',
             discountValue: 0,
             subtotal: {{ $workOrder->items->sum('subtotal') }},
             get discountAmount() {
                 if (!this.discountType || !this.discountValue) return 0;
                 return this.discountType === 'percent' ? this.subtotal * (this.discountValue / 100) : parseFloat(this.discountValue);
             },
             get total() { return Math.max(this.subtotal - this.discountAmount, 0); }
         }">

        <div class="bg-white rounded shadow overflow-hidden mb-4">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr><th class="p-3">Item</th><th class="p-3 text-right">Qty</th><th class="p-3 text-right">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach ($workOrder->items as $item)
                        <tr class="border-t">
                            <td class="p-3">{{ $item->item_name }}</td>
                            <td class="p-3 text-right">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('pos.payment.confirm', $workOrder) }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Jenis Diskon (opsional)</label>
                    <select name="discount_type" x-model="discountType" class="border rounded px-3 py-2 w-full">
                        <option value="">Tanpa Diskon</option>
                        <option value="percent">Persen (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nilai Diskon</label>
                    <input type="number" step="0.01" name="discount_value" x-model.number="discountValue" min="0" class="border rounded px-3 py-2 w-full">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Metode Pembayaran</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2"><input type="radio" name="payment_method" value="tunai" required> Tunai</label>
                    <label class="flex items-center gap-2"><input type="radio" name="payment_method" value="transfer"> Transfer</label>
                    <label class="flex items-center gap-2"><input type="radio" name="payment_method" value="debit"> Debit</label>
                </div>
            </div>

            <div class="border-t pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span></div>
                <div class="flex justify-between text-red-600"><span>Diskon</span><span x-text="'- Rp ' + discountAmount.toLocaleString('id-ID')"></span></div>
                <div class="flex justify-between font-bold text-lg"><span>Total</span><span x-text="'Rp ' + total.toLocaleString('id-ID')"></span></div>
            </div>

            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded w-full">Konfirmasi Pembayaran</button>
        </form>
    </div>
</x-app-layout>