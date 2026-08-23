<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Teknisi</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('technicians.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Inisial (ditampilkan di POS, mis. "BD")</label>
                <input type="text" name="inisial" value="{{ old('inisial') }}" maxlength="10" required class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Telpon</label>
                <input type="text" name="telpon" value="{{ old('telpon') }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email (untuk login)</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="border rounded px-3 py-2 w-full">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Simpan</button>
        </form>
    </div>
</x-app-layout>