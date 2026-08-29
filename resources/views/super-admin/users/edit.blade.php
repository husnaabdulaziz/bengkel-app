<x-admin-layout title="Edit User">
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

        <form method="POST" action="{{ route('super-admin.users.update', $user) }}" class="card">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Toko</label>
                    <input type="text" value="{{ $user->company->nama_toko }}" disabled class="form-control">
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required class="form-control">
                        <option value="admin_toko" @selected($currentRole === 'admin_toko')>Admin Toko</option>
                        <option value="karyawan_toko" @selected($currentRole === 'karyawan_toko')>Karyawan Toko</option>
                        <option value="teknisi" @selected($currentRole === 'teknisi')>Teknisi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cabang</label>
                    <div class="d-flex flex-wrap" style="gap: 1rem;">
                        @foreach ($branches as $branch)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="branch-{{ $branch->id }}" name="branch_ids[]" value="{{ $branch->id }}"
                                       @checked(in_array($branch->id, $currentBranchIds))>
                                <label class="custom-control-label" for="branch-{{ $branch->id }}">{{ $branch->nama_cabang }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required class="form-control">
                        <option value="active" @selected($user->status === 'active')>Aktif</option>
                        <option value="inactive" @selected($user->status === 'inactive')>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>