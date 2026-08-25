<x-admin-layout title="Sub Kategori Produk">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tambah Sub Kategori</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('product-subcategories.store') }}" class="form-inline">
                @csrf
                <select name="category_id" required class="form-control mr-2 mb-2 mb-sm-0">
                    <option value="">- Kategori Induk -</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
                <input type="text" name="nama" placeholder="Nama sub kategori" required class="form-control mr-2 mb-2 mb-sm-0" style="flex: 1; min-width: 200px;">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Kategori Induk</th><th>Nama Sub Kategori</th><th style="width: 120px;">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($subcategories as $sub)
                        <tr>
                            <td>{{ $sub->category->nama }}</td>
                            <td>
                                <form method="POST" action="{{ route('product-subcategories.update', $sub) }}" class="form-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="category_id" value="{{ $sub->category_id }}">
                                    <input type="text" name="nama" value="{{ $sub->nama }}" class="form-control mr-2" style="flex: 1;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('product-subcategories.destroy', $sub) }}" onsubmit="return confirm('Hapus sub kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">Belum ada sub kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $subcategories->links() }}
</x-admin-layout>