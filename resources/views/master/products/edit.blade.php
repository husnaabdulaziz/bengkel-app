<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')
            @include('master.products._form')
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan Perubahan</button>
        </form>
    </div>
</x-app-layout>