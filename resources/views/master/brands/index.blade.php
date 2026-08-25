<x-admin-layout title="Brand Produk">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tambah Brand</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('product-brands.store') }}">
                @csrf
                <div class="form-group">
                    <label class="text-muted small">Kategori (bisa pilih lebih dari satu)</label>
                    <div class="d-flex flex-wrap" style="gap: 1rem;">
                        @foreach ($categories as $cat)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="newbrand-cat-{{ $cat->id }}" name="category_ids[]" value="{{ $cat->id }}">
                                <label class="custom-control-label" for="newbrand-cat-{{ $cat->id }}">{{ $cat->nama }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-inline">
                    <input type="text" name="nama" placeholder="Nama brand (mis. Shell, Honda)" required class="form-control mr-2" style="flex: 1; min-width: 200px;">
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Kategori</th><th>Nama Brand</th><th style="width: 90px;"></th></tr></thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr x-data="{ editing: false }">
                            <!-- Mode baca -->
                            <td x-show="!editing" x-cloak>
                                @forelse ($brand->categories as $cat)
                                    <span class="badge badge-info">{{ $cat->nama }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td x-show="!editing" x-cloak>{{ $brand->nama }}</td>
                            <td x-show="!editing" x-cloak>
                                <button type="button" @click="editing = true" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('product-brands.destroy', $brand) }}" class="d-inline" onsubmit="return confirm('Hapus brand ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>

                            <!-- Mode edit -->
                            <td colspan="3" x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('product-brands.update', $brand) }}">
                                    @csrf @method('PUT')
                                    <div class="d-flex flex-wrap mb-2" style="gap: 1rem;">
                                        @foreach ($categories as $cat)
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="editbrand-{{ $brand->id }}-{{ $cat->id }}" name="category_ids[]" value="{{ $cat->id }}" @checked($brand->categories->contains('id', $cat->id))>
                                                <label class="custom-control-label" for="editbrand-{{ $brand->id }}-{{ $cat->id }}">{{ $cat->nama }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-inline">
                                        <input type="text" name="nama" value="{{ $brand->nama }}" class="form-control mr-2" style="flex: 1;">
                                        <button type="submit" class="btn btn-sm btn-primary mr-2">Simpan</button>
                                        <button type="button" @click="editing = false" class="btn btn-sm btn-outline-secondary">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">Belum ada brand.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $brands->links() }}
</x-admin-layout>