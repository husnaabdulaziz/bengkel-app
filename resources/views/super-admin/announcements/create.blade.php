<x-admin-layout title="Buat Pengumuman">
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

        <form method="POST" action="{{ route('super-admin.announcements.store') }}" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="message" rows="5" required class="form-control">{{ old('message') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Target</label>
                    <select name="target_role" required class="form-control">
                        <option value="all" @selected(old('target_role') === 'all')>Semua User</option>
                        <option value="admin_toko" @selected(old('target_role') === 'admin_toko')>Admin Toko</option>
                        <option value="karyawan_toko" @selected(old('target_role') === 'karyawan_toko')>Karyawan Toko</option>
                        <option value="teknisi" @selected(old('target_role') === 'teknisi')>Teknisi</option>
                    </select>
                    <small class="text-muted">Muncul otomatis sebagai popup saat user pertama kali buka sistem di hari itu.</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan & Aktifkan</button>
            </div>
        </form>
    </div>
</x-admin-layout>