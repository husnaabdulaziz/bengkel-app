<x-admin-layout title="Buat PO ke Vendor">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($itemsByVendor->isEmpty())
        <div class="alert alert-info">Tidak ada produk stock menipis yang punya Vendor terdaftar. Isi Vendor di data produk dulu supaya bisa dibuatkan PO otomatis.</div>
    @endif

    <form method="POST" action="{{ route('purchases.store-po') }}">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $branchId }}">

        @foreach ($itemsByVendor as $vendorId => $group)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $group['vendor']->nama }}</h3>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="vendor-all-{{ $vendorId }}" onchange="document.querySelectorAll('.item-{{ $vendorId }}').forEach(el => el.checked = this.checked)">
                        <label class="custom-control-label" for="vendor-all-{{ $vendorId }}">Pilih Semua</label>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Produk</th>
                                <th class="text-right">Stock Sekarang</th>
                                <th class="text-right">Minimum</th>
                                <th style="width: 100px;">Qty Order</th>
                                <th style="width: 130px;">Harga Beli/Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['items'] as $index => $item)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input item-{{ $vendorId }}" id="chk-{{ $vendorId }}-{{ $index }}"
                                                   name="vendors[{{ $vendorId }}][items][{{ $index }}][checked]" value="1" checked>
                                            <label class="custom-control-label" for="chk-{{ $vendorId }}-{{ $index }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $item->product->nama }}
                                        <input type="hidden" name="vendors[{{ $vendorId }}][items][{{ $index }}][product_id]" value="{{ $item->product->id }}">
                                    </td>
                                    <td class="text-right {{ $item->stock_qty <= 0 ? 'text-danger font-weight-bold' : '' }}">{{ $item->stock_qty }}</td>
                                    <td class="text-right">{{ $item->product->minimum_stock }}</td>
                                    <td>
                                        <input type="number" name="vendors[{{ $vendorId }}][items][{{ $index }}][qty]"
                                            value="{{ $item->product->minimum_stock }}" min="1" class="form-control form-control-sm text-right">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="vendors[{{ $vendorId }}][items][{{ $index }}][price]"
                                            value="{{ $item->product->harga_modal }}" min="0" class="form-control form-control-sm text-right">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if ($itemsByVendor->isNotEmpty())
            <button type="submit" class="btn btn-primary btn-lg">Buat PO untuk Vendor yang Dipilih</button>
        @endif
    </form>
</x-admin-layout>