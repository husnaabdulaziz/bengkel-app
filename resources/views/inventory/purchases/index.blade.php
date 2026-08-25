<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pembelian dari Vendor</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('purchases.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Catat Pembelian</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">No. Invoice</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Vendor</th>
                        <th class="p-3">Cabang</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr class="border-t">
                            <td class="p-3">{{ $purchase->invoice_number }}</td>
                            <td class="p-3">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $purchase->supplier?->nama }}</td>
                            <td class="p-3">{{ $purchase->branch?->nama_cabang }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">{{ $purchase->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3 text-gray-500">Belum ada transaksi pembelian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $purchases->links() }}</div>
    </div>
</x-app-layout>