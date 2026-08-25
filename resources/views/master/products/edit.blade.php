<x-admin-layout title="Edit Produk">
    <form method="POST" action="{{ route('products.update', $product) }}" class="card">
        <div class="card-body">
            @csrf
            @method('PUT')
            @include('master.products._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</x-admin-layout>