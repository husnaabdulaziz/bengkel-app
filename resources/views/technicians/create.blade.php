<x-admin-layout title="Tambah Mekanik">
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

        <form method="POST" action="{{ route('technicians.store') }}" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Inisial (ditampilkan di POS, mis. "BD")</label>
                    <input type="text" name="inisial" value="{{ old('inisial') }}" maxlength="10" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Telpon</label>
                    <input type="text" name="telpon" value="{{ old('telpon') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email (untuk login)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>