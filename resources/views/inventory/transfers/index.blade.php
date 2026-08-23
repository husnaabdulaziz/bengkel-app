<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transfer Stock Antar Cabang</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('stock-transfers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Ajukan Transfer</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Dari</th>
                        <th class="p-3">Ke</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal Diajukan</th>
                        <th class="p-3 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColor = [
                            'requested' => 'bg-yellow-100 text-yellow-700',
                            'approved'  => 'bg-blue-100 text-blue-700',
                            'shipped'   => 'bg-purple-100 text-purple-700',
                            'received'  => 'bg-green-100 text-green-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    @forelse ($transfers as $transfer)
                        <tr class="border-t">
                            <td class="p-3">{{ $transfer->kode_transfer }}</td>
                            <td class="p-3">{{ $transfer->fromBranch?->nama_cabang }}</td>
                            <td class="p-3">{{ $transfer->toBranch?->nama_cabang }}</td>
                            <td class="p-3">
                                <span class="text-xs px-2 py-0.5 rounded {{ $statusColor[$transfer->status] ?? '' }}">{{ $transfer->status }}</span>
                            </td>
                            <td class="p-3">{{ $transfer->requested_at?->format('d/m/Y H:i') }}</td>
                            <td class="p-3">
                                <a href="{{ route('stock-transfers.show', $transfer) }}" class="text-blue-600">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3 text-gray-500">Belum ada transfer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $transfers->links() }}</div>
    </div>
</x-app-layout>