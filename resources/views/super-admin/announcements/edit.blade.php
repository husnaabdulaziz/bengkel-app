<x-admin-layout title="Edit Pengumuman">
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

        <form method="POST" action="{{ route('super-admin.announcements.update', $announcement) }}" class="card">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="message" rows="5" required class="form-control">{{ old('message', $announcement->message) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Target</label>
                    <select name="target_role" required class="form-control">
                        <option value="all" @selected($announcement->target_role === 'all')>Semua User</option>
                        <option value="admin_toko" @selected($announcement->target_role === 'admin_toko')>Admin Toko</option>
                        <option value="karyawan_toko" @selected($announcement->target_role === 'karyawan_toko')>Karyawan Toko</option>
                        <option value="teknisi" @selected($announcement->target_role === 'teknisi')>Teknisi</option>
                    </select>
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked($announcement->is_active)>
                    <label class="custom-control-label" for="is_active">Aktif</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>