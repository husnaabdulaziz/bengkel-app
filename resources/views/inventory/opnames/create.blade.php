<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Stock Opname Baru</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-opnames.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Cabang</label>
                <select name="branch_id" required class="border rounded px-3 py-2 w-full">
                    <option value="">- Pilih Cabang -</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Opname</label>
                <input type="date" name="opname_date" value="{{ date('Y-m-d') }}" required class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Filter Kategori (opsional)</label>
                <select name="category_id" class="border rounded px-3 py-2 w-full">
                    <option value="">- Semua Kategori -</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Filter Brand (opsional)</label>
                <select name="brand_id" class="border rounded px-3 py-2 w-full">
                    <option value="">- Semua Brand -</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->nama }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-sm text-gray-500">Sistem akan menampilkan daftar produk sesuai filter, lengkap dengan stock sistem saat ini, untuk Anda input stock real-nya di langkah berikutnya.</p>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Buat & Lanjutkan</button>
        </form>
    </div>
</x-app-layout>