<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Fee Teknisi</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" class="flex gap-2 mb-4 items-center flex-wrap">
            <div class="flex gap-2">
                @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                    <a href="{{ route('reports.technician-fee', array_filter(['period' => $key, 'technician_id' => $technicianId])) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium border {{ $period === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <select name="technician_id" onchange="this.form.submit()" class="border rounded px-3 py-2">
                <option value="">Semua Teknisi</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" @selected($technicianId == $tech->id)>{{ $tech->name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="period" value="{{ $period }}">

            <a href="{{ route('reports.technician-fee.pdf', request()->query()) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded ml-auto">Export PDF</a>
        </form>

        <div class="text-sm text-gray-500 mb-3">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 w-12">No</th>
                        <th class="p-3">Teknisi</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3 text-right">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr class="border-t">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $row->technician->inisial ?? '-' }} - {{ $row->technician->name }}</td>
                            <td class="p-3">{{ $row->product_name }}</td>
                            <td class="p-3">{{ $row->notes ?? '-' }} @if($row->source === 'manual')<span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded ml-1">Manual</span>@endif</td>
                            <td class="p-3 text-right">Rp {{ number_format($row->fee_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500 text-center">Tidak ada data fee pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3 text-right font-semibold border-t">Total Fee: Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
        </div>
    </div>
</x-app-layout>