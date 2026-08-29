<x-admin-layout title="Log Aktivitas">

    <form method="GET" class="d-flex flex-wrap align-items-center mb-3" style="gap: 0.5rem;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan..." class="form-control" style="width: 220px;">
        <select name="user_id" class="form-control" style="width: auto;">
            <option value="">Semua User</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="width: 150px;">
        <span>s/d</span>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="width: 150px;">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 150px;">Waktu</th>
                        @if ($isSuperAdmin)
                            <th>Toko</th>
                        @endif
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th class="d-none d-md-table-cell">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            @if ($isSuperAdmin)
                                <td>{{ $log->company?->nama_toko ?? '-' }}</td>
                            @endif
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td>{{ $log->description }}</td>
                            <td class="d-none d-md-table-cell text-muted small">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $logs->links() }}
</x-admin-layout>