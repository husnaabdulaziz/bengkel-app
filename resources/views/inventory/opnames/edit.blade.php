<x-admin-layout title="Input Stock Real">

    <h5 class="mb-3">{{ $opname->kode_opname }}</h5>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div x-data="{ showKategori: false, showSubkategori: false, showBrand: false, showLokasi: false, searchQuery: '' }">

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small mr-3">Tampilkan kolom tambahan:</span>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showKategori" class="mr-1"> Kategori</label>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showSubkategori" class="mr-1"> Sub Kategori</label>
                    <label class="d-inline-flex align-items-center mr-3"><input type="checkbox" x-model="showBrand" class="mr-1"> Brand</label>
                    <label class="d-inline-flex align-items-center"><input type="checkbox" x-model="showLokasi" class="mr-1"> Lokasi Rak</label>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari nama produk, kategori, sub kategori, atau brand..." class="form-control">
            </div>
        </div>

        <form method="POST" action="{{ route('stock-opnames.update', $opname) }}" class="card">
            @csrf @method('PUT')

            <div class="card-body p-0 table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th x-show="showKategori" x-cloak>Kategori</th>
                            <th x-show="showSubkategori" x-cloak>Sub Kategori</th>
                            <th x-show="showBrand" x-cloak>Brand</th>
                            <th x-show="showLokasi" x-cloak>Lokasi</th>
                            <th class="text-right">Stock Sistem</th>
                            <th class="text-right">Stock Real</th>
                            <th class="text-right">Selisih</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($opname->items as $item)
                            @php
                                $searchable = strtolower($item->product->nama . ' ' . ($item->product->category?->nama ?? '') . ' ' . ($item->product->subcategory?->nama ?? '') . ' ' . ($item->product->brand?->nama ?? '') . ' ' . ($item->product->lokasi_rak ?? ''));
                            @endphp
                            <tr x-show="searchQuery === '' || '{{ addslashes($searchable) }}'.includes(searchQuery.toLowerCase())">
                                <td>{{ $item->product->nama }}</td>
                                <td class="text-muted" x-show="showKategori" x-cloak>{{ $item->product->category?->nama ?? '-' }}</td>
                                <td class="text-muted" x-show="showSubkategori" x-cloak>{{ $item->product->subcategory?->nama ?? '-' }}</td>
                                <td class="text-muted" x-show="showBrand" x-cloak>{{ $item->product->brand?->nama ?? '-' }}</td>
                                <td class="text-muted" x-show="showLokasi" x-cloak>{{ $item->product->lokasi_rak ?? '-' }}</td>
                                <td class="text-right">{{ $item->system_stock }}</td>
                                <td class="text-right">
                                    <input type="number" name="real_stock[{{ $item->id }}]" value="{{ $item->real_stock }}" min="0"
                                           class="form-control form-control-sm text-right" style="width: 90px; display: inline-block;" {{ $opname->status === 'completed' ? 'disabled' : '' }}>
                                </td>
                                <td class="text-right {{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                </td>
                                <td>
                                    <input type="text" name="notes[{{ $item->id }}]" value="{{ $item->notes }}" placeholder="Alasan selisih (opsional)"
                                           class="form-control form-control-sm" {{ $opname->status === 'completed' ? 'disabled' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($opname->status !== 'completed')
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">Simpan Stock Real</button>
                </div>
            @endif
        </form>

        @if ($opname->status !== 'completed')
            <form method="POST" action="{{ route('stock-opnames.adjust', $opname) }}"
                  onsubmit="return confirm('Yakin sesuaikan stock sistem sesuai hasil opname ini? Aksi ini akan mengubah stock secara permanen dan tidak bisa diulang untuk opname yang sama.')">
                @csrf
                <button type="submit" class="btn btn-danger">Sesuaikan Stock Sekarang</button>
            </form>
        @else
            <div class="d-flex justify-content-between align-items-center">
                <p class="text-success font-weight-bold mb-0">Opname ini sudah selesai dan stock telah disesuaikan.</p>
                <a :href="`{{ route('stock-opnames.pdf', $opname) }}?kategori=${showKategori ? 1 : 0}&subkategori=${showSubkategori ? 1 : 0}&brand=${showBrand ? 1 : 0}&lokasi=${showLokasi ? 1 : 0}`"
                   target="_blank" class="btn btn-secondary"><i class="fas fa-file-pdf"></i> Download PDF</a>
            </div>
        @endif
    </div>
</x-admin-layout>