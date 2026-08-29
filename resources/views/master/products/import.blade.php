<x-admin-layout title="Import Produk">

    <div style="max-width: 700px;" class="mx-auto">

        @if (session('importResult'))
            @php $result = session('importResult'); @endphp
            <div class="alert alert-{{ count($result['errors']) > 0 ? 'warning' : 'success' }}">
                <strong>{{ $result['success'] }} produk berhasil diimport.</strong>
                @if (count($result['errors']) > 0)
                    <p class="mt-2 mb-1">{{ count($result['errors']) }} baris gagal:</p>
                    <ul class="mb-0">
                        @foreach ($result['errors'] as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="card-title">1. Download Template</h3></div>
            <div class="card-body">
                <p class="text-muted">Isi data produk sesuai format template ini. Kolom Kategori/Sub Kategori/Brand/Vendor akan otomatis dibuat kalau belum ada. Kolom "Stock Awal" akan <strong>menambah</strong> stock yang sudah ada di cabang yang dipilih (bukan menimpa) — aman untuk import berkali-kali, misal saat restock.</p>
                <a href="{{ route('products.import.template') }}" class="btn btn-outline-primary"><i class="fas fa-download"></i> Download Template Excel</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">2. Upload File yang Sudah Diisi</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Cabang (untuk Stock Awal)</label>
                        <select name="branch_id" required class="form-control">
                            <option value="">- Pilih Cabang -</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($branch->is_main)>{{ $branch->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File Excel/CSV</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-primary">Import Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>