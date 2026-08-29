<x-admin-layout title="Kelola Hak Akses">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto">

        <form method="GET" class="d-flex flex-wrap mb-3" style="gap: 0.5rem;">
            <select name="company_id" onchange="this.form.submit()" class="form-control">
                @foreach ($companies as $c)
                    <option value="{{ $c->id }}" @selected($companyId == $c->id)>{{ $c->nama_toko }}</option>
                @endforeach
            </select>
            <select name="role" onchange="this.form.submit()" class="form-control">
                <option value="admin_toko" @selected($roleName === 'admin_toko')>Admin Toko</option>
                <option value="karyawan_toko" @selected($roleName === 'karyawan_toko')>Karyawan Toko</option>
                <option value="teknisi" @selected($roleName === 'teknisi')>Teknisi</option>
            </select>
        </form>

        <div class="alert alert-info">
            Mengatur hak akses untuk role <strong>{{ ['admin_toko' => 'Admin Toko', 'karyawan_toko' => 'Karyawan Toko', 'teknisi' => 'Teknisi'][$roleName] }}</strong>
            di toko <strong>{{ $companies->firstWhere('id', $companyId)?->nama_toko }}</strong>.
        </div>

        <form method="POST" action="{{ route('super-admin.permissions.update') }}" class="card">
            @csrf
            <input type="hidden" name="company_id" value="{{ $companyId }}">
            <input type="hidden" name="role" value="{{ $roleName }}">

            <div class="card-body">
                <h6 class="text-muted">Akses Menu</h6>
                @foreach ($menuList as $key => $label)
                    @if (str_starts_with($key, 'access_'))
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="perm-{{ $key }}" name="permissions[]" value="{{ $key }}"
                                   @checked(in_array($key, $currentPermissions))>
                            <label class="custom-control-label" for="perm-{{ $key }}">{{ $label }}</label>
                        </div>
                    @endif
                @endforeach

                <hr>
                <h6 class="text-muted text-danger">Izin Hapus (Sensitif)</h6>
                @foreach ($menuList as $key => $label)
                    @if (str_starts_with($key, 'delete_'))
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="perm-{{ $key }}" name="permissions[]" value="{{ $key }}"
                                   @checked(in_array($key, $currentPermissions))>
                            <label class="custom-control-label" for="perm-{{ $key }}">{{ $label }}</label>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>