<x-admin-layout title="Tambah Produk">
    <form method="POST" action="{{ route('products.store') }}" class="card">
        <div class="card-body">
            @csrf
            @include('master.products._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</x-admin-layout>