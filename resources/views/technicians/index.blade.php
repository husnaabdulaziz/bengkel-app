<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Mekanik</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('technicians.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah Mekanik</a>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nama Lengkap</th>
                        <th class="p-3">Inisial</th>
                        <th class="p-3">Telpon</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($technicians as $tech)
                        <tr class="border-t">
                            <td class="p-3">{{ $tech->name }}</td>
                            <td class="p-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">{{ $tech->inisial }}</span></td>
                            <td class="p-3">{{ $tech->phone }}</td>
                            <td class="p-3">{{ $tech->email }}</td>
                            <td class="p-3">
                                <a href="{{ route('technicians.edit', $tech) }}" class="text-blue-600 text-sm">Edit</a>
                                <form method="POST" action="{{ route('technicians.destroy', $tech) }}" class="inline" onsubmit="return confirm('Hapus mekanik ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-gray-500 text-center">Belum ada mekanik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $technicians->links() }}</div>
    </div>
</x-app-layout>