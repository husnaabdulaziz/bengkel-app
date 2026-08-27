<x-admin-layout title="Pengaturan Toko">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="card">
            @csrf

            <div class="card-header"><h3 class="card-title">Profil Toko</h3></div>
            <div class="card-body">
                <div class="form-group text-center">
                    @if ($company->logo_path)
                        <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo" style="max-width: 120px; max-height: 120px;" class="mb-2 d-block mx-auto rounded border p-1">
                    @else
                        <div class="text-muted mb-2">Belum ada logo</div>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-upload"></i> Pilih Logo Baru
                        <input type="file" name="logo" accept="image/*" class="d-none">
                    </label>
                </div>

                <div class="form-group">
                    <label>Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $company->nama_toko) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Alamat Toko</label>
                    <textarea name="alamat_toko" rows="2" class="form-control">{{ old('alamat_toko', $company->alamat_toko) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Telpon</label>
                        <input type="text" name="telpon" value="{{ old('telpon', $company->telpon) }}" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required class="form-control">
                    </div>
                </div>
            </div>

            <div class="card-header border-top"><h3 class="card-title">Pengaturan Cetak</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Ukuran Kertas Struk/Invoice</label>
                    <select name="printer_paper_size" class="form-control" style="max-width: 300px;">
                        <option value="58mm" @selected(old('printer_paper_size', $printerPaperSize) === '58mm')>Thermal 58mm</option>
                        <option value="80mm" @selected(old('printer_paper_size', $printerPaperSize) === '80mm')>Thermal 80mm</option>
                        <option value="A4" @selected(old('printer_paper_size', $printerPaperSize) === 'A4')>A4</option>
                    </select>
                </div>
            </div>

            <div class="card-header border-top"><h3 class="card-title">Warna Tampilan</h3></div>
            <div class="card-body">
                <div class="row">
                    @php
                        $colorFields = [
                            'primary_color' => 'Warna Utama (tombol, elemen utama)',
                            'link_color' => 'Warna Link (teks bisa diklik)',
                            'active_menu_color' => 'Warna Menu Aktif',
                            'sidebar_color' => 'Warna Latar Sidebar',
                            'success_color' => 'Warna Sukses (hijau)',
                            'danger_color' => 'Warna Bahaya (merah)',
                            'warning_color' => 'Warna Peringatan (kuning)',
                            'hover_color' => 'Warna Hover (saat kursor diarahkan)',
                            'brand_bg_color' => 'Latar Belakang Logo & Nama Toko',
                            'brand_text_color' => 'Warna Teks Nama Toko',
                        ];
                    @endphp
                    @foreach ($colorFields as $key => $label)
                        <div class="col-md-6 form-group d-flex align-items-center">
                            <input type="color" name="{{ $key }}" value="{{ old($key, $colors[$key]) }}" style="width: 50px; height: 38px; padding: 2px; border: 1px solid #ccc; margin-right: 0.75rem;">
                            <label class="mb-0">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('settings.karyawan-access') }}" class="text-muted"><i class="fas fa-users-cog"></i> Atur Hak Akses Karyawan Toko</a>
        </div>
    </div>
</x-admin-layout>