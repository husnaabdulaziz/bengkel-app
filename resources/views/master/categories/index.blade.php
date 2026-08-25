<x-admin-layout title="Kategori Produk">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tambah Kategori</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('product-categories.store') }}" class="form-inline">
                @csrf
                <input type="text" name="nama" placeholder="Nama kategori" required class="form-control mr-2" style="flex: 1; min-width: 200px;">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Nama</th><th style="width: 160px;">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('product-categories.update', $category) }}" class="form-inline">
                                    @csrf @method('PUT')
                                    <input type="text" name="nama" value="{{ $category->nama }}" class="form-control mr-2" style="flex: 1;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('product-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $categories->links() }}
</x-admin-layout>