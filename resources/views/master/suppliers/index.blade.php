<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vendor</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-3">Tambah Vendor</h3>
            <form method="POST" action="{{ route('suppliers.store') }}" class="grid grid-cols-2 gap-3">
                @csrf
                <input type="text" name="nama" placeholder="Nama supplier" required class="border rounded px-3 py-2">
                <input type="text" name="contact_person" placeholder="Contact person" class="border rounded px-3 py-2">
                <input type="text" name="telpon" placeholder="Telpon" class="border rounded px-3 py-2">
                <input type="text" name="alamat" placeholder="Alamat" class="border rounded px-3 py-2">
                <button type="submit" class="col-span-2 bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
            </form>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Contact</th>
                        <th class="p-3">Telpon</th>
                        <th class="p-3 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr class="border-t">
                            <td class="p-3">{{ $supplier->nama }}</td>
                            <td class="p-3">{{ $supplier->contact_person }}</td>
                            <td class="p-3">{{ $supplier->telpon }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                      onsubmit="return confirm('Hapus vendor ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3 text-gray-500">Belum ada vendor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $suppliers->links() }}</div>
    </div>
</x-app-layout>