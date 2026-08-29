<p class="text-muted">
    Setelah akun dihapus, semua data dan sumber daya terkait akan dihapus permanen. Sebelum menghapus akun, unduh data yang ingin Anda simpan.
</p>

<button type="button" onclick="document.getElementById('confirm-delete-form').style.display = 'block'; this.style.display = 'none';" class="btn btn-danger">
    Hapus Akun
</button>

<div id="confirm-delete-form" style="display: none;" class="mt-3">
    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password untuk konfirmasi" class="form-control @error('password', 'userDeletion') is-invalid @enderror">
            @error('password', 'userDeletion')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini secara permanen?')">Konfirmasi Hapus Akun</button>
        <button type="button" onclick="document.getElementById('confirm-delete-form').style.display = 'none';" class="btn btn-outline-secondary">Batal</button>
    </form>
</div>