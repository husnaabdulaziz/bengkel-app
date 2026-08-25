<x-admin-layout title="Buat Stock Opname Baru">

    <div style="max-width: 700px;" class="mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-opnames.store') }}" class="card"
                x-data="{
                    allBrands: {{ $brands->map(fn($b) => ['id' => $b->id, 'category_ids' => $b->categories->pluck('id')->values(), 'nama' => $b->nama])->values()->toJson() }},
                    selectedCategory: '{{ old('category_id', '') }}',
                    get filteredBrands() {
                        if (!this.selectedCategory) return this.allBrands;
                        return this.allBrands.filter(b => b.category_ids.includes(parseInt(this.selectedCategory)));
                    }
                }">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Cabang</label>
                    <select name="branch_id" required class="form-control">
                        <option value="">- Pilih Cabang -</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id') ? old('branch_id') == $branch->id : $branch->is_main)>{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Opname</label>
                    <input type="date" name="opname_date" value="{{ date('Y-m-d') }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Filter Kategori (opsional)</label>
                    <select name="category_id" x-model="selectedCategory" class="form-control">
                        <option value="">- Semua Kategori -</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter Brand (opsional)</label>
                    <select name="brand_id" class="form-control">
                        <option value="">- Semua Brand -</option>
                        <template x-for="b in filteredBrands" :key="b.id">
                            <option :value="b.id" x-text="b.nama"></option>
                        </template>
                    </select>
                </div>
                <p class="text-muted small">Sistem akan menampilkan daftar produk sesuai filter, lengkap dengan stock sistem saat ini, untuk Anda input stock real-nya di langkah berikutnya.</p>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Buat & Lanjutkan</button>
            </div>
        </form>
    </div>
</x-admin-layout>