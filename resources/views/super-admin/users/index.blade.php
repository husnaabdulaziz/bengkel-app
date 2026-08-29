<x-admin-layout title="Kelola User">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 0.5rem;">
        <form method="GET">
            <select name="company_id" onchange="this.form.submit()" class="form-control">
                @foreach ($companies as $c)
                    <option value="{{ $c->id }}" @selected($companyId == $c->id)>{{ $c->nama_toko }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('super-admin.users.create', ['company_id' => $companyId]) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $u->email }}</td>
                            <td><span class="badge badge-info">{{ $u->currentRole ?? '-' }}</span></td>
                            <td><span class="badge {{ $u->status === 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $u->status }}</span></td>
                            <td><a href="{{ route('super-admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada user di toko ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>