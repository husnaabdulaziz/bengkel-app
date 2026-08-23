<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori Produk</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-3">Tambah Kategori</h3>
            <form method="POST" action="{{ route('product-categories.store') }}" class="flex gap-2">
                @csrf
                <input type="text" name="nama" placeholder="Nama kategori" required
                       class="border rounded px-3 py-2 flex-1">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
            </form>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-t">
                            <td class="p-3">
                                <form method="POST" action="{{ route('product-categories.update', $category) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="nama" value="{{ $category->nama }}"
                                           class="border rounded px-2 py-1 flex-1">
                                    <button type="submit" class="text-blue-600 text-sm">Simpan</button>
                                </form>
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('product-categories.destroy', $category) }}"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="p-3 text-gray-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $categories->links() }}</div>
    </div>
</x-app-layout>