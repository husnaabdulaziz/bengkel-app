<x-admin-layout title="Hak Akses Karyawan">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto">
        <p class="text-muted">Centang menu yang boleh diakses oleh akun dengan role <strong>Karyawan Toko</strong>. Perubahan berlaku untuk semua karyawan di toko ini.</p>

        <form method="POST" action="{{ route('settings.karyawan-access.update') }}" class="card">
            @csrf
            <div class="card-body">
                @foreach ($menuList as $key => $label)
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="perm-{{ $key }}" name="permissions[]" value="{{ $key }}"
                               @checked(in_array($key, $currentPermissions))>
                        <label class="custom-control-label" for="perm-{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Hak Akses</button>
            </div>
        </form>

        <a href="{{ route('settings.edit') }}" class="text-muted d-inline-block mt-3">← Kembali ke Pengaturan Toko</a>
    </div>
</x-admin-layout>