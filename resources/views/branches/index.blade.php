<x-admin-layout title="Kelola Cabang">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div style="max-width: 700px;" class="mx-auto">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('branches.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Cabang</a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Cabang</th>
                            <th class="d-none d-md-table-cell">Telpon</th>
                            <th class="text-center">User</th>
                            <th style="width: 130px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr>
                                <td>
                                    {{ $branch->nama_cabang }}
                                    @if ($branch->is_main)
                                        <span class="badge badge-info">Utama</span>
                                    @endif
                                    @if (!$branch->is_active)
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">{{ $branch->telpon }}</td>
                                <td class="text-center">{{ $branch->users_count }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @if (!$branch->is_main)
                                        <form method="POST" action="{{ route('branches.toggle-active', $branch) }}" class="d-inline" onsubmit="return confirm('{{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }} cabang ini? Seluruh riwayat data tetap aman, tidak akan terhapus.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $branch->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                {{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        @if (auth()->user()->is_super_admin)
                                            <a href="{{ route('branches.confirm-delete', $branch) }}" class="btn btn-sm btn-outline-danger">Hapus Permanen</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>