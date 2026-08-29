<x-admin-layout title="Kelola Pengumuman">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('super-admin.announcements.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Pengumuman</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th class="d-none d-md-table-cell">Target</th>
                        <th>Status</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $a)
                        @php
                            $roleLabel = ['all' => 'Semua', 'admin_toko' => 'Admin Toko', 'karyawan_toko' => 'Karyawan Toko', 'teknisi' => 'Teknisi'][$a->target_role];
                        @endphp
                        <tr>
                            <td>{{ $a->title }}</td>
                            <td class="d-none d-md-table-cell"><span class="badge badge-info">{{ $roleLabel }}</span></td>
                            <td>
                                <span class="badge {{ $a->is_active ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $a->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('super-admin.announcements.edit', $a) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('super-admin.announcements.destroy', $a) }}" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>