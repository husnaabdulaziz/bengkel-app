<x-admin-layout title="Bertindak Sebagai Toko">

    @if (session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <div style="max-width: 500px;" class="mx-auto">
        <div class="alert alert-info">
            Pilih toko yang mau Anda kelola. Selama mode ini aktif, Anda akan melihat sistem persis seperti Admin Toko di toko tersebut.
        </div>

        <div class="card">
            <div class="card-body">
                @forelse ($companies as $company)
                    <form method="POST" action="{{ route('switch-company.store') }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <button type="submit" class="btn btn-outline-primary btn-block text-left">
                            <i class="fas fa-store mr-2"></i> {{ $company->nama_toko }}
                        </button>
                    </form>
                @empty
                    <p class="text-muted">Belum ada toko terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>