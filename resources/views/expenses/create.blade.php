<x-admin-layout title="Tambah Pengeluaran">
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

        <form method="POST" action="{{ route('expenses.store') }}" class="card">
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
                    <label>Tanggal</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="mis. Listrik, Sewa, Gaji, Konsumsi" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Nominal</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required min="0" class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>