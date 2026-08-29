<x-admin-layout title="Tambah User">
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

        <form method="POST" action="{{ route('super-admin.users.store') }}" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Toko</label>
                    <select name="company_id" required class="form-control">
                        @foreach ($companies as $c)
                            <option value="{{ $c->id }}" @selected(old('company_id', $companyId) == $c->id)>{{ $c->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Username (opsional)</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required class="form-control">
                        <option value="admin_toko" @selected(old('role') === 'admin_toko')>Admin Toko</option>
                        <option value="karyawan_toko" @selected(old('role') === 'karyawan_toko')>Karyawan Toko</option>
                        <option value="teknisi" @selected(old('role') === 'teknisi')>Teknisi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cabang</label>
                    <div class="d-flex flex-wrap" style="gap: 1rem;">
                        @foreach ($branches as $branch)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="branch-{{ $branch->id }}" name="branch_ids[]" value="{{ $branch->id }}">
                                <label class="custom-control-label" for="branch-{{ $branch->id }}">{{ $branch->nama_cabang }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required class="form-control">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>