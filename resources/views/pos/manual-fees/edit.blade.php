<x-admin-layout title="Edit Fee Mekanik Manual">
    <div style="max-width: 700px;" class="mx-auto"
         x-data="{
             productQuery: '{{ $fee->product?->nama }}', productResults: [], selectedProduct: {{ $fee->product ? json_encode(['id' => $fee->product->id, 'nama' => $fee->product->nama]) : 'null' }},
             searchProducts() {
                 clearTimeout(this._t);
                 if (this.productQuery.length < 2) { this.productResults = []; return; }
                 this._t = setTimeout(() => {
                     fetch(`{{ route('pos.search-product') }}?q=${encodeURIComponent(this.productQuery)}`)
                         .then(r => r.json())
                         .then(data => this.productResults = data);
                 }, 300);
             },
             selectProduct(p) {
                 this.selectedProduct = p;
                 this.productQuery = p.nama;
                 this.productResults = [];
             }
         }">

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Fee diinput manual, bukan dari POS. Tidak ada nomor invoice untuk transaksi ini.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('technician-manual-fees.update', $fee->id) }}" class="card">
            @csrf @method('PATCH')
            <div class="card-body">
                <div class="form-group">
                    <label>Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $fee->transaction_date->format('Y-m-d')) }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Nama Mekanik</label>
                    <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                        @foreach ($technicians as $tech)
                            <label class="border rounded px-3 py-2 mb-0" style="cursor: pointer;">
                                <input type="radio" name="user_id" value="{{ $tech->id }}" @checked($fee->user_id == $tech->id) required class="mr-1">
                                <strong>{{ $tech->inisial ?? '-' }}</strong> - {{ $tech->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group position-relative">
                    <label>Nama Produk</label>
                    <input type="text" x-model="productQuery" @input="searchProducts" placeholder="Cari produk..." class="form-control" autocomplete="off">
                    <input type="hidden" name="product_id" x-model="selectedProduct ? selectedProduct.id : ''">
                    <div x-show="productResults.length > 0" @click.outside="productResults = []" x-cloak
                         class="list-group position-absolute w-100" style="z-index: 20; max-height: 200px; overflow-y: auto;">
                        <template x-for="p in productResults" :key="p.id">
                            <a href="#" @click.prevent="selectProduct(p)" class="list-group-item list-group-item-action" x-text="p.nama"></a>
                        </template>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nominal Fee</label>
                    <input type="number" step="0.01" name="fee_amount" value="{{ old('fee_amount', $fee->fee_amount) }}" required min="0" class="form-control">
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $fee->notes) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('reports.technician-fee') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>