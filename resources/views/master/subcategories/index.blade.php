<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sub Kategori Produk</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-3">Tambah Sub Kategori</h3>
            <form method="POST" action="{{ route('product-subcategories.store') }}" class="flex gap-2">
                @csrf
                <select name="category_id" required class="border rounded px-3 py-2">
                    <option value="">- Kategori Induk -</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
                <input type="text" name="nama" placeholder="Nama sub kategori" required class="border rounded px-3 py-2 flex-1">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
            </form>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Kategori Induk</th>
                        <th class="p-3">Nama Sub Kategori</th>
                        <th class="p-3 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subcategories as $sub)
                        <tr class="border-t">
                            <td class="p-3">{{ $sub->category->nama }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('product-subcategories.update', $sub) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="category_id" value="{{ $sub->category_id }}">
                                    <input type="text" name="nama" value="{{ $sub->nama }}" class="border rounded px-2 py-1 flex-1">
                                    <button type="submit" class="text-blue-600 text-sm">Simpan</button>
                                </form>
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('product-subcategories.destroy', $sub) }}" onsubmit="return confirm('Hapus sub kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-3 text-gray-500">Belum ada sub kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $subcategories->links() }}</div>
    </div>
</x-app-layout>