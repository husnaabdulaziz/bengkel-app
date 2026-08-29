<x-admin-layout title="Manajemen Mekanik">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('technicians.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Mekanik</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Inisial</th>
                        <th class="d-none d-md-table-cell">Telpon</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($technicians as $tech)
                        <tr>
                            <td>{{ $tech->name }}</td>
                            <td><span class="badge badge-info">{{ $tech->inisial }}</span></td>
                            <td class="d-none d-md-table-cell">{{ $tech->phone }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('technicians.edit', $tech) }}" class="btn btn-outline-primary" title="Edit" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('technicians.destroy', $tech) }}" class="d-inline" onsubmit="return confirm('Hapus mekanik ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada mekanik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $technicians->links() }}
</x-admin-layout>