<x-admin-layout title="Edit Mekanik">
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

        <form method="POST" action="{{ route('technicians.update', $technician) }}" class="card">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $technician->name) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Inisial</label>
                    <input type="text" name="inisial" value="{{ old('inisial', $technician->inisial) }}" maxlength="10" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Telpon</label>
                    <input type="text" name="telpon" value="{{ old('telpon', $technician->phone) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $technician->email) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>