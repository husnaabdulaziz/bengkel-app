<x-admin-layout title="Hapus Cabang Permanen">

    <div style="max-width: 600px;" class="mx-auto">
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> Peringatan Serius!</strong><br>
            Menghapus cabang <strong>"{{ $branch->nama_cabang }}"</strong> akan menghapus PERMANEN dan TIDAK BISA DIKEMBALIKAN:
            <ul class="mb-0 mt-2">
                <li>Semua riwayat Transaksi POS</li>
                <li>Semua riwayat Pembelian dari Vendor</li>
                <li>Semua riwayat Pengeluaran & Kas Harian</li>
                <li>Semua riwayat Stock Opname & Transfer Stock</li>
                <li>Semua data Garansi dari cabang ini</li>
                <li>Semua data stock produk di cabang ini</li>
            </ul>
        </div>

        <div class="alert alert-info">
            Kalau cuma ingin cabang ini berhenti dipakai (tanpa kehilangan data historis), gunakan tombol <strong>"Nonaktifkan"</strong> saja, bukan hapus permanen ini.
        </div>

        <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="card">
            @csrf @method('DELETE')
            <div class="card-body">
                <div class="form-group">
                    <label>Ketik ulang nama cabang <strong>"{{ $branch->nama_cabang }}"</strong> untuk konfirmasi</label>
                    <input type="text" name="confirmation" required class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Anda benar-benar yakin? Aksi ini TIDAK BISA DIBATALKAN.')">
                    <i class="fas fa-trash"></i> Hapus Permanen
                </button>
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>