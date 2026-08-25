<x-admin-layout title="Vendor">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tambah Vendor</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('suppliers.store') }}" class="row">
                @csrf
                <div class="col-md-6 mb-2"><input type="text" name="nama" placeholder="Nama vendor" required class="form-control"></div>
                <div class="col-md-6 mb-2"><input type="text" name="contact_person" placeholder="Contact person" class="form-control"></div>
                <div class="col-md-6 mb-2"><input type="text" name="telpon" placeholder="Telpon" class="form-control"></div>
                <div class="col-md-6 mb-2"><input type="text" name="alamat" placeholder="Alamat" class="form-control"></div>
                <div class="col-12"><button type="submit" class="btn btn-primary">Tambah</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Nama</th><th>Contact</th><th>Telpon</th><th style="width: 90px;">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->nama }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->telpon }}</td>
                            <td>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus vendor ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Belum ada vendor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $suppliers->links() }}
</x-admin-layout>