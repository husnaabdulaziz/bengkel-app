<x-admin-layout title="Kelola Toko">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Toko</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nama Toko</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="text-center">Cabang</th>
                        <th class="text-center">User</th>
                        <th style="width: 130px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td>
                                @if ($company->logo_path)
                                    <img src="{{ asset('storage/' . $company->logo_path) }}" style="width: 32px; height: 32px; object-fit: contain;">
                                @endif
                            </td>
                            <td>{{ $company->nama_toko }}</td>
                            <td class="d-none d-md-table-cell">{{ $company->email }}</td>
                            <td class="text-center">{{ $company->branches_count }}</td>
                            <td class="text-center">{{ $company->users_count }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('super-admin.companies.edit', $company) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('super-admin.users.index', ['company_id' => $company->id]) }}" class="btn btn-sm btn-outline-secondary">User</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada toko terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>