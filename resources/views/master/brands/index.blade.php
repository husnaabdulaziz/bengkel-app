<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Brand Produk</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-3">Tambah Brand</h3>
            <form method="POST" action="{{ route('product-brands.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Kategori (bisa pilih lebih dari satu)</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-1 text-sm">
                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}">
                                {{ $cat->nama }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="nama" placeholder="Nama brand (mis. Shell, Honda)" required
                           class="border rounded px-3 py-2 flex-1">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Nama Brand</th>
                        <th class="p-3 w-20"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr class="border-t align-top" x-data="{ editing: false }">
                            <!-- Mode baca -->
                            <td class="p-3" x-show="!editing" x-cloak>
                                @forelse ($brand->categories as $cat)
                                    <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full mr-1 mb-1">{{ $cat->nama }}</span>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </td>
                            <td class="p-3" x-show="!editing" x-cloak>
                                {{ $brand->nama }}
                            </td>
                            <td class="p-3" x-show="!editing" x-cloak>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="editing = true" class="text-gray-400 hover:text-blue-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('product-brands.destroy', $brand) }}" onsubmit="return confirm('Hapus brand ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <!-- Mode edit -->
                            <td class="p-3" x-show="editing" x-cloak colspan="3">
                                <form method="POST" action="{{ route('product-brands.update', $brand) }}" class="space-y-2">
                                    @csrf @method('PUT')
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($categories as $cat)
                                            <label class="flex items-center gap-1 text-xs">
                                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                                                       @checked($brand->categories->contains('id', $cat->id))>
                                                {{ $cat->nama }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="nama" value="{{ $brand->nama }}" class="border rounded px-2 py-1 flex-1 text-sm">
                                        <button type="submit" class="text-blue-600 text-sm font-medium whitespace-nowrap">Simpan</button>
                                        <button type="button" @click="editing = false" class="text-gray-400 text-sm whitespace-nowrap">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-3 text-gray-500">Belum ada brand.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $brands->links() }}</div>
    </div>
</x-app-layout>