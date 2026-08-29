<x-admin-layout title="Tambah Toko">
    <div style="max-width: 600px;" class="mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('super-admin.companies.store') }}" enctype="multipart/form-data" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Logo (opsional)</label>
                    <input type="file" name="logo" accept="image/*" class="form-control-file">
                </div>
                <div class="form-group">
                    <label>Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Alamat Toko</label>
                    <textarea name="alamat_toko" rows="2" class="form-control">{{ old('alamat_toko') }}</textarea>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Telpon</label>
                        <input type="text" name="telpon" value="{{ old('telpon') }}" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label>Nama Cabang Utama</label>
                    <input type="text" name="nama_cabang_utama" value="{{ old('nama_cabang_utama', 'Cabang Utama') }}" required class="form-control">
                    <small class="text-muted">Setiap toko baru otomatis dibuatkan 1 cabang utama supaya langsung bisa dipakai operasional.</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Buat Toko</button>
            </div>
        </form>
    </div>
</x-admin-layout>