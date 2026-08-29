<x-admin-layout title="Reset Data">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto"
         x-data="{ confirmText: '', selectedAny: false }">

        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> Peringatan!</strong>
            Aksi ini akan <strong>menghapus permanen</strong> data yang dipilih dan <strong>tidak bisa dibatalkan</strong>. Pastikan sudah backup database kalau masih ragu.
        </div>

        <form method="POST" action="{{ route('super-admin.reset-data.process') }}"
              onsubmit="return confirm('Anda YAKIN? Data yang dipilih akan hilang PERMANEN dan tidak bisa dikembalikan.')"
              class="card">
            @csrf
            <div class="card-header"><h3 class="card-title">Pilih Data yang Ingin Dihapus</h3></div>
            <div class="card-body">
                @foreach ($categories as $key => $cat)
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="cat-{{ $key }}" name="categories[]" value="{{ $key }}"
                               @change="selectedAny = $root.querySelectorAll('input[name=\'categories[]\']:checked').length > 0">
                        <label class="custom-control-label" for="cat-{{ $key }}">{{ $cat['label'] }}</label>
                    </div>
                @endforeach

                <hr>

                <div class="form-group">
                    <label>Ketik <strong>HAPUS DATA</strong> untuk konfirmasi</label>
                    <input type="text" name="confirmation" x-model="confirmText" required class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-danger" :disabled="confirmText !== 'HAPUS DATA'">
                    <i class="fas fa-trash"></i> Hapus Data yang Dipilih
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>