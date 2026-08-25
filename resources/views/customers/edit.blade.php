<x-admin-layout title="Edit Pelanggan">
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

        <form method="POST" action="{{ route('customers.update', $customer) }}" class="card">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $customer->nama) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Telpon</label>
                    <input type="text" name="telpon" value="{{ old('telpon', $customer->telpon) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Plat Nomor</label>
                    <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $customer->plat_nomor) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $customer->alamat) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="col-md-4 form-group">
                        <label>Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan', $customer->jenis_kendaraan) }}" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Merk</label>
                        <input type="text" name="merk_kendaraan" value="{{ old('merk_kendaraan', $customer->merk_kendaraan) }}" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Model</label>
                        <input type="text" name="model_kendaraan" value="{{ old('model_kendaraan', $customer->model_kendaraan) }}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>
