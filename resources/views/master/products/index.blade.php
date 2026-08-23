<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                       class="border rounded px-3 py-2 w-64">
                <select name="category_id" class="border rounded px-3 py-2">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->nama }}</option>
                    @endforeach
                </select>
                <select name="brand_id" class="border rounded px-3 py-2">
                    <option value="">Semua Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
            </form>
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Produk</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Brand</th>
                        <th class="p-3 text-right">Harga Jual</th>
                        <th class="p-3 text-right">Jual+Jasa</th>
                        <th class="p-3 text-right">Online</th>
                        <th class="p-3 text-right">Ojol</th>
                        <th class="p-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-t">
                            <td class="p-3">{{ $product->nama }} @if($product->is_jasa)<span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded ml-1">Jasa</span>@endif</td>
                            <td class="p-3">{{ $product->category?->nama ?? '-' }}</td>
                            <td class="p-3">{{ $product->brand?->nama ?? '-' }}</td>
                            <td class="p-3 text-right">{{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">{{ number_format($product->harga_jual_jasa, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">{{ number_format($product->harga_online, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">{{ number_format($product->harga_ojol, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <a href="{{ route('products.edit', $product) }}" class="text-blue-600 text-sm">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-3 text-gray-500">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</x-app-layout>