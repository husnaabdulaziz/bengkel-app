<x-admin-layout title="Kelola Menu Sistem">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Menu yang dinonaktifkan di sini akan tersembunyi dan tidak bisa diakses oleh <strong>semua user</strong> (Admin Toko, Karyawan Toko) di <strong>seluruh toko</strong>. Hanya Super Admin yang tetap bisa mengakses.
        </div>

        <form method="POST" action="{{ route('super-admin.system-menus.update') }}" class="card">
            @csrf
            <div class="card-body">
                @foreach ($menuList as $key => $label)
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="menu-{{ $key }}" name="menus[]" value="{{ $key }}"
                               @checked($current[$key] ?? true)>
                        <label class="custom-control-label" for="menu-{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>