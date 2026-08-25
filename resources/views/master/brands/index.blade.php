<x-admin-layout title="Brand Produk">

    <div style="max-width: 900px;" class="mx-auto">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tambah Brand</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('product-brands.store') }}">
                @csrf
                <div class="form-group">
                    <label class="text-muted small mb-1">Kategori (bisa pilih lebih dari satu)</label>
                    <div class="d-flex flex-wrap" style="gap: 1rem;">
                        @foreach ($categories as $cat)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="newbrand-cat-{{ $cat->id }}" name="category_ids[]" value="{{ $cat->id }}">
                                <label class="custom-control-label" for="newbrand-cat-{{ $cat->id }}">{{ $cat->nama }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-9">
                        <input type="text" name="nama" placeholder="Nama brand (mis. Shell, Honda)" required class="form-control">
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-primary btn-block">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Kategori</th>
                        <th>Nama Brand</th>
                        <th style="width: 70px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr x-data="{ editing: false }">
                            <!-- Mode baca -->
                            <td x-show="!editing" x-cloak class="align-middle">
                                @forelse ($brand->categories as $cat)
                                    <span class="badge badge-info">{{ $cat->nama }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td x-show="!editing" x-cloak class="align-middle">{{ $brand->nama }}</td>
                            <td x-show="!editing" x-cloak class="align-middle text-nowrap">
                                <button type="button" @click="editing = true" class="btn btn-outline-secondary" title="Edit" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="{{ route('product-brands.destroy', $brand) }}" class="d-inline" onsubmit="return confirm('Hapus brand ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>

                            <!-- Mode edit -->
                            <td colspan="3" x-show="editing" x-cloak class="py-3">
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
                                    <div class="form-row align-items-center">
                                        <div class="col-6">
                                            <input type="text" name="nama" value="{{ $brand->nama }}" class="form-control">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                            <button type="button" @click="editing = false" class="btn btn-outline-secondary btn-sm">Batal</button>
                                        </div>
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

    </div>
</x-admin-layout>