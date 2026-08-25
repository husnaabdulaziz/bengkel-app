<x-admin-layout title="Ajukan Transfer Stock">

    <div style="max-width: 800px;" class="mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-transfers.store') }}"
              x-data="{
                  items: [{ product_id: '', qty_requested: 1 }],
                  addItem() { this.items.push({ product_id: '', qty_requested: 1 }) },
                  removeItem(i) { this.items.splice(i, 1) },
              }"
              class="card">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Dari Cabang</label>
                        <select name="from_branch_id" required class="form-control">
                            <option value="">- Pilih -</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ke Cabang</label>
                        <select name="to_branch_id" required class="form-control">
                            <option value="">- Pilih -</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <h5>Item yang Diminta</h5>

                <template x-for="(item, index) in items" :key="index">
                    <div class="row align-items-end mb-2">
                        <div class="col-8">
                            <label class="small mb-1">Produk</label>
                            <select :name="`items[${index}][product_id]`" x-model="item.product_id" required class="form-control form-control-sm">
                                <option value="">- Pilih Produk -</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nama }} ({{ $product->satuan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="small mb-1">Qty Diminta</label>
                            <input type="number" :name="`items[${index}][qty_requested]`" x-model.number="item.qty_requested" min="1" required class="form-control form-control-sm">
                        </div>
                        <div class="col-1">
                            <button type="button" @click="removeItem(index)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()" class="btn btn-link btn-sm px-0">+ Tambah Baris Item</button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Ajukan Transfer</button>
            </div>
        </form>
    </div>
</x-admin-layout>