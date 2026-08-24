<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Fee Mekanik</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <form method="GET" class="flex gap-2 mb-4 items-center flex-wrap">
            <div class="flex gap-2">
                @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                    <a href="{{ route('reports.technician-fee', array_filter(['period' => $key, 'technician_id' => $technicianId])) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium border {{ $period === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="relative inline-block">
                <select name="technician_id" onchange="this.form.submit()"
                        class="border rounded px-3 py-2 pr-8 bg-white"
                        style="appearance: none; -webkit-appearance: none; -moz-appearance: none;">
                    <option value="">Semua Mekanik</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected($technicianId == $tech->id)>{{ $tech->name }}</option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <input type="hidden" name="period" value="{{ $period }}">

            <a href="{{ route('reports.technician-fee.pdf', request()->query()) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded ml-auto">Export PDF</a>
        </form>

        <div class="text-sm text-gray-500 mb-3">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 w-12">No</th>
                        <th class="p-3">Mekanik</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3 text-right" style="width: 260px;">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        @php
                            $updateUrl = $row->source === 'manual'
                                ? route('technician-manual-fees.update', $row->id)
                                : route('reports.technician-fee.update', $row->id);
                            $notesFieldName = $row->source === 'manual' ? 'notes' : 'fee_notes';
                            $isManual = $row->source === 'manual' || ($row->is_manual_case ?? false);
                            $isEmpty = $row->fee_amount == 0;
                        @endphp
                        <tr class="border-t align-top" x-data="{ editing: {{ $isEmpty ? 'true' : 'false' }} }">
                            <td class="p-3 pt-4">{{ $loop->iteration }}</td>
                            <td class="p-3 pt-4">{{ $row->technician->inisial ?? '-' }} - {{ $row->technician->name }}</td>
                            <td class="p-3 pt-4">{{ $row->product_name }}</td>

                            <!-- Mode baca (fee sudah terisi & belum diklik Edit) -->
                            <td class="p-3 pt-4" x-show="!editing" x-cloak>
                                {{ $row->notes ?? '-' }}
                                @if($isManual)
                                    <span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded ml-1">Manual</span>
                                @endif
                            </td>
                            <td class="p-3 pt-4 text-right" x-show="!editing" x-cloak>
                                <span>Rp {{ number_format($row->fee_amount, 0, ',', '.') }}</span>
                                <button type="button" @click="editing = true" class="inline-flex align-middle text-gray-400 hover:text-blue-600 ml-2" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </td>

                            <!-- Mode edit (fee kosong, atau sudah terisi tapi klik Edit) -->
                            <td class="p-3" x-show="editing" x-cloak colspan="1">
                                <form method="POST" action="{{ $updateUrl }}" class="flex items-start gap-1">
                                    @csrf @method('PATCH')
                                    <div class="flex-1">
                                        <input type="text" name="{{ $notesFieldName }}" value="{{ $row->notes }}" placeholder="Keterangan"
                                               class="border rounded px-2 py-1 text-xs w-full">
                                        @if($isManual)
                                            <span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded inline-block mt-1">Manual</span>
                                        @endif
                                    </div>
                            </td>
                            <td class="p-3 text-right" x-show="editing" x-cloak>
                                    <div class="flex items-center justify-end gap-1">
                                        <input type="number" step="0.01" name="fee_amount" value="{{ $row->fee_amount }}" placeholder="Fee"
                                            required class="border rounded px-2 py-1 w-24 text-right text-xs {{ $isEmpty ? 'border-orange-400' : '' }}">
                                        <button type="submit" class="text-blue-600 text-xs font-medium whitespace-nowrap">Simpan</button>
                                        @if(!$isEmpty)
                                            <button type="button" @click="editing = false" class="text-gray-400 text-xs">Batal</button>
                                        @endif
                                    </div>
                                </form>
                                @if($isEmpty)
                                    @php
                                        $deleteUrl = $row->source === 'manual'
                                            ? route('technician-manual-fees.destroy', $row->id)
                                            : route('reports.technician-fee.destroy', $row->id);
                                    @endphp
                                    <form method="POST" action="{{ $deleteUrl }}" class="inline mt-1" onsubmit="return confirm('Hapus data fee mekanik ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 inline-flex items-center gap-1 text-xs mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500 text-center">Tidak ada data fee pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
<div class="p-3 text-right font-semibold border-t">Total Fee: Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
</div>

<div class="flex justify-between items-center mt-4">
    <form method="GET">
        @foreach (request()->except(['per_page', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <label class="text-sm text-gray-500">
            Tampilkan
            <select name="per_page" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                @foreach ([10, 20, 50, 100] as $opt)
                    <option value="{{ $opt }}" @selected($perPage == $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            data per halaman
        </label>
    </form>

    {{ $data->links() }}
</div>
</div>
</x-app-layout>