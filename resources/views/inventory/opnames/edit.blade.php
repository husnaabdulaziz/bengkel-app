<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Stock Real — {{ $opname->kode_opname }}</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8" x-data="{ showKategori: false, showSubkategori: false, showBrand: false, searchQuery: '' }">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white p-4 rounded shadow mb-4">
            @if ($opname->status !== 'completed')
                <div class="mb-3">
                    <span class="text-sm text-gray-500 mr-3">Tampilkan kolom tambahan:</span>
                    <label class="inline-flex items-center gap-1 text-sm mr-4">
                        <input type="checkbox" x-model="showKategori"> Kategori
                    </label>
                    <label class="inline-flex items-center gap-1 text-sm mr-4">
                        <input type="checkbox" x-model="showSubkategori"> Sub Kategori
                    </label>
                    <label class="inline-flex items-center gap-1 text-sm">
                        <input type="checkbox" x-model="showBrand"> Brand
                    </label>
                </div>
            @endif

            <input type="text" x-model="searchQuery" placeholder="Cari nama produk, kategori, sub kategori, atau brand..."
                class="border rounded px-3 py-2 w-full">
        </div>

        <form method="POST" action="{{ route('stock-opnames.update', $opname) }}" class="bg-white rounded shadow overflow-hidden mb-4">
            @csrf @method('PUT')

            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Produk</th>
                        <th class="p-3" x-show="showKategori" x-cloak>Kategori</th>
                        <th class="p-3" x-show="showSubkategori" x-cloak>Sub Kategori</th>
                        <th class="p-3" x-show="showBrand" x-cloak>Brand</th>
                        <th class="p-3 text-right">Stock Sistem</th>
                        <th class="p-3 text-right">Stock Real</th>
                        <th class="p-3 text-right">Selisih</th>
                        <th class="p-3">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opname->items as $item)
                        @php
                            $searchable = strtolower($item->product->nama . ' ' . ($item->product->category?->nama ?? '') . ' ' . ($item->product->subcategory?->nama ?? '') . ' ' . ($item->product->brand?->nama ?? ''));
                        @endphp
                        <tr class="border-t" x-show="searchQuery === '' || '{{ addslashes($searchable) }}'.includes(searchQuery.toLowerCase())">
                            <td class="p-3">{{ $item->product->nama }}</td>
                            <td class="p-3 text-gray-500" x-show="showKategori" x-cloak>{{ $item->product->category?->nama ?? '-' }}</td>
                            <td class="p-3 text-gray-500" x-show="showSubkategori" x-cloak>{{ $item->product->subcategory?->nama ?? '-' }}</td>
                            <td class="p-3 text-gray-500" x-show="showBrand" x-cloak>{{ $item->product->brand?->nama ?? '-' }}</td>
                            <td class="p-3 text-right">{{ $item->system_stock }}</td>
                            <td class="p-3 text-right">
                                <input type="number" name="real_stock[{{ $item->id }}]" value="{{ $item->real_stock }}" min="0"
                                       class="border rounded px-2 py-1 w-24 text-right" {{ $opname->status === 'completed' ? 'disabled' : '' }}>
                            </td>
                            <td class="p-3 text-right {{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-400') }}">
                                {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                            </td>
                            <td class="p-3">
                                <input type="text" name="notes[{{ $item->id }}]" value="{{ $item->notes }}" placeholder="Alasan selisih (opsional)"
                                       class="border rounded px-2 py-1 w-full text-xs" {{ $opname->status === 'completed' ? 'disabled' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($opname->status !== 'completed')
                <div class="p-4 bg-gray-50 border-t">
                    <button type="submit" class="bg-gray-700 text-white px-6 py-2 rounded">Simpan Stock Real</button>
                </div>
            @endif
        </form>

        @if ($opname->status !== 'completed')
            <form method="POST" action="{{ route('stock-opnames.adjust', $opname) }}"
                  onsubmit="return confirm('Yakin sesuaikan stock sistem sesuai hasil opname ini? Aksi ini akan mengubah stock secara permanen dan tidak bisa diulang untuk opname yang sama.')">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded">Sesuaikan Stock Sekarang</button>
            </form>
        @else
            <p class="text-green-700 font-medium">Opname ini sudah selesai dan stock telah disesuaikan.</p>
        @endif
    </div>
</x-app-layout>