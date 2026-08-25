<x-admin-layout title="Edit Fee Teknisi">
    <div style="max-width: 600px;" class="mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="card-title">Info Transaksi</h3></div>
            <div class="card-body">
                <div><strong>Teknisi:</strong> {{ $row->technician->inisial ?? '-' }} - {{ $row->technician->name }}</div>
                <div><strong>Produk:</strong> {{ $row->item->item_name }}</div>
                <div><strong>No. Invoice:</strong> {{ $row->item->workOrder->invoice_number }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.technician-fee.update', $row->id) }}" class="card">
            @csrf @method('PATCH')
            <div class="card-body">
                <div class="form-group">
                    <label>Nominal Fee</label>
                    <input type="number" step="0.01" name="fee_amount" value="{{ old('fee_amount', $row->fee_amount) }}" required min="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="fee_notes" rows="3" class="form-control">{{ old('fee_notes', $row->fee_notes) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('reports.technician-fee') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>