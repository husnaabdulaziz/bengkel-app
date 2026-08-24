<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fee Mekanik Manual</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <div class="relative inline-block">
                <select onchange="window.location.href = this.value"
                        class="border rounded px-3 py-2 pr-8 bg-white"
                        style="appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                    <option value="{{ route('technician-manual-fees.index') }}">Semua Mekanik</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ route('technician-manual-fees.index', ['technician_id' => $tech->id]) }}" @selected(request('technician_id') == $tech->id)>{{ $tech->name }}</option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <a href="{{ route('technician-manual-fees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Input Fee Manual</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Mekanik</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3 text-right">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fees as $fee)
                        <tr class="border-t">
                            <td class="p-3">{{ $fee->transaction_date->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $fee->technician->name }}</td>
                            <td class="p-3">{{ $fee->product?->nama ?? '-' }}</td>
                            <td class="p-3">{{ $fee->notes }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($fee->fee_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500 text-center">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $fees->links() }}</div>
    </div>
</x-app-layout>