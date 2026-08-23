<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Opname</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('stock-opnames.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Buat Opname Baru</a>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Cabang</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($opnames as $opname)
                        <tr class="border-t">
                            <td class="p-3">{{ $opname->kode_opname }}</td>
                            <td class="p-3">{{ $opname->opname_date->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $opname->branch?->nama_cabang }}</td>
                            <td class="p-3">
                                <span class="text-xs px-2 py-0.5 rounded {{ $opname->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $opname->status }}
                                </span>
                            </td>
                            <td class="p-3">
                                <a href="{{ route('stock-opnames.edit', $opname) }}" class="text-blue-600">
                                    {{ $opname->status === 'draft' ? 'Lanjutkan' : 'Lihat Detail' }}
                                </a>
                                @if ($opname->status === 'completed')
                                    <a href="{{ route('stock-opnames.pdf', $opname) }}" class="text-gray-600 ml-2" target="_blank">PDF</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500">Belum ada opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $opnames->links() }}</div>
    </div>
</x-app-layout>