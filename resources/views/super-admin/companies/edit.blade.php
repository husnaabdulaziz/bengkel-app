<x-admin-layout title="Edit Toko">
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

        <form method="POST" action="{{ route('super-admin.companies.update', $company) }}" enctype="multipart/form-data" class="card">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group text-center">
                    @if ($company->logo_path)
                        <img src="{{ asset('storage/' . $company->logo_path) }}" style="max-width: 100px; max-height: 100px;" class="mb-2 d-block mx-auto rounded border p-1">
                    @endif
                    <input type="file" name="logo" accept="image/*" class="form-control-file">
                </div>
                <div class="form-group">
                    <label>Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $company->nama_toko) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Alamat Toko</label>
                    <textarea name="alamat_toko" rows="2" class="form-control">{{ old('alamat_toko', $company->alamat_toko) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Telpon</label>
                        <input type="text" name="telpon" value="{{ old('telpon', $company->telpon) }}" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>