<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $company = \App\Models\Company::first();
        $colorDefaults = [
            'primary_color'      => '#007bff',
            'active_menu_color'  => '#ffc107',
            'sidebar_color'      => '#343a40',
            'success_color'      => '#28a745',
        ];
        $saved = $company ? \App\Models\StoreSetting::where('company_id', $company->id)->whereNull('branch_id')->whereIn('setting_key', array_keys($colorDefaults))->pluck('setting_value', 'setting_key') : collect();
        $c = array_merge($colorDefaults, $saved->toArray());
        $namaToko = $company?->nama_toko ?? config('app.name');
    @endphp

    <title>{{ $namaToko }} - Sistem Manajemen Bengkel</title>
    @if ($company?->logo_path)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $company->logo_path) }}">
    @endif

    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    <style>
        body { background-color: #14161a; color: #e5e5e5; font-family: 'Source Sans Pro', -apple-system, sans-serif; }
        .navbar-landing { background: rgba(20,22,26,0.9); backdrop-filter: blur(8px); padding: 1rem 0; position: sticky; top: 0; z-index: 100; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .navbar-landing .nav-link { color: #cfcfcf; }
        .navbar-landing .nav-link:hover { color: {{ $c['primary_color'] }}; }
        .accent { color: {{ $c['primary_color'] }}; }
        .btn-accent { background-color: {{ $c['primary_color'] }}; border-color: {{ $c['primary_color'] }}; color: #fff; font-weight: 600; }
        .btn-accent:hover { opacity: 0.9; color: #fff; }
        .badge-pill-outline { border: 1px solid {{ $c['primary_color'] }}; color: {{ $c['primary_color'] }}; border-radius: 30px; padding: 0.4rem 1rem; font-size: 0.8rem; font-weight: 600; display: inline-block; }
        .hero-headline { font-size: 3rem; font-weight: 800; line-height: 1.15; }
        .preview-card { background: #1c1f24; border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,0.4); }
        .preview-titlebar { background: #232630; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.4rem; }
        .preview-dot { width: 11px; height: 11px; border-radius: 50%; }
        .stat-box { background: #23262d; border-radius: 10px; padding: 1rem; text-align: center; }
        .trans-row { background: #23262d; border-radius: 8px; padding: 0.7rem 1rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem; }
        .avatar-circle { width: 34px; height: 34px; border-radius: 50%; background: {{ $c['primary_color'] }}33; color: {{ $c['primary_color'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 0.6rem; }
        .stats-strip { background: #1c1f24; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 2rem; }
        .whatsapp-fab { position: fixed; bottom: 24px; right: 24px; background: {{ $c['success_color'] }}; color: #fff; border-radius: 30px; padding: 0.8rem 1.3rem; box-shadow: 0 8px 20px rgba(0,0,0,0.3); z-index: 200; font-weight: 600; }
        .whatsapp-fab:hover { color: #fff; opacity: 0.9; }
        .grid-bg { background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar-landing">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="d-flex align-items-center text-decoration-none">
                @if ($company?->logo_path)
                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo" style="height: 34px; margin-right: 10px;">
                @endif
                <strong style="color: #fff; font-size: 1.2rem;">{{ $namaToko }}</strong>
            </a>
            <ul class="nav d-none d-md-flex">
                <li class="nav-item"><a href="#fitur" class="nav-link">Fitur</a></li>
                <li class="nav-item"><a href="#tentang" class="nav-link">Tentang</a></li>
                <li class="nav-item"><a href="#kontak" class="nav-link">Bantuan</a></li>
            </ul>
            <a href="{{ route('login') }}" class="btn btn-accent px-4">Login</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="grid-bg" style="padding: 5rem 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge-pill-outline mb-3"><i class="fas fa-sparkles mr-1"></i> Sistem Manajemen Bengkel</span>
                    <h1 class="hero-headline mt-3 mb-4" style="color: #fff;">
                        Kelola bengkel lebih<br>
                        <span class="accent">cerdas & efisien.</span>
                    </h1>
                    <p style="color: #a0a0a0; font-size: 1.05rem;" class="mb-4">
                        POS servis, stok sparepart, laporan keuangan, kas harian, dan manajemen mekanik &mdash; semua dalam satu sistem berbasis cloud untuk {{ $namaToko }}.
                    </p>
                    <div class="d-flex flex-wrap" style="gap: 0.75rem;">
                        <a href="{{ route('login') }}" class="btn btn-accent btn-lg px-4">Masuk ke Sistem <i class="fas fa-arrow-right ml-1"></i></a>
                        <a href="#fitur" class="btn btn-outline-light btn-lg px-4"><i class="fas fa-play-circle mr-1"></i> Lihat Fitur</a>
                    </div>

                    <div class="d-flex flex-wrap mt-5" style="gap: 1.5rem; color: #8a8a8a; font-size: 0.9rem;">
                        <span><i class="fas fa-lock mr-1"></i> Data Aman</span>
                        <span><i class="fas fa-cloud mr-1"></i> Akses 24/7</span>
                        <span><i class="fas fa-id-badge mr-1"></i> Buatan Lokal</span>
                    </div>
                </div>

                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="preview-card">
                        <div class="preview-titlebar">
                            <span class="preview-dot" style="background:#ff5f57;"></span>
                            <span class="preview-dot" style="background:#febc2e;"></span>
                            <span class="preview-dot" style="background:#28c840;"></span>
                            <span class="ml-2" style="color:#999; font-size:0.85rem;">{{ $namaToko }}</span>
                        </div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div style="color:#8a8a8a; font-size:0.8rem;">Operasional Hari Ini</div>
                                    <div style="color:#fff; font-size:1.4rem; font-weight:700;">Rp 18.450.000</div>
                                </div>
                                <span class="badge" style="background:{{ $c['primary_color'] }}22; color:{{ $c['primary_color'] }};">Live</span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div style="color:#8a8a8a; font-size:0.75rem;">SERVIS</div>
                                        <div style="color:#fff; font-weight:700; font-size:1.2rem;">42</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div style="color:#8a8a8a; font-size:0.75rem;">ANTRIAN</div>
                                        <div style="color:{{ $c['primary_color'] }}; font-weight:700; font-size:1.2rem;">8</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box">
                                        <div style="color:#8a8a8a; font-size:0.75rem;">STOK</div>
                                        <div style="color:#fff; font-weight:700; font-size:1.2rem;">315</div>
                                    </div>
                                </div>
                            </div>

                            <div class="trans-row">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">A</div>
                                    <div>
                                        <div style="color:#fff; font-size:0.9rem;">Avanza B 2141 KJ</div>
                                        <div style="color:#8a8a8a; font-size:0.75rem;">Ganti oli + tune up</div>
                                    </div>
                                </div>
                                <span class="badge" style="background:{{ $c['active_menu_color'] }}22; color:{{ $c['active_menu_color'] }};">Proses</span>
                            </div>
                            <div class="trans-row">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">B</div>
                                    <div>
                                        <div style="color:#fff; font-size:0.9rem;">Beat F 1842 RD</div>
                                        <div style="color:#8a8a8a; font-size:0.75rem;">CVT Service</div>
                                    </div>
                                </div>
                                <span class="badge" style="background:{{ $c['success_color'] }}22; color:{{ $c['success_color'] }};">Lunas</span>
                            </div>
                            <div class="trans-row mb-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">C</div>
                                    <div>
                                        <div style="color:#fff; font-size:0.9rem;">Innova D 7781 HM</div>
                                        <div style="color:#8a8a8a; font-size:0.75rem;">Rem depan</div>
                                    </div>
                                </div>
                                <span class="badge" style="background:#6c757d33; color:#adb5bd;">Antri</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur -->
    <section id="fitur" class="container" style="padding: 4rem 0;">
        <div class="stats-strip">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <i class="fas fa-cash-register accent" style="font-size:1.8rem;"></i>
                    <div style="color:#fff; font-weight:700; margin-top:0.5rem;">POS Servis</div>
                    <div style="color:#8a8a8a; font-size:0.85rem;">3 tahap, cepat & rapi</div>
                </div>
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <i class="fas fa-boxes accent" style="font-size:1.8rem;"></i>
                    <div style="color:#fff; font-weight:700; margin-top:0.5rem;">Stok Sparepart</div>
                    <div style="color:#8a8a8a; font-size:0.85rem;">Multi-cabang, real-time</div>
                </div>
                <div class="col-md-3 col-6">
                    <i class="fas fa-chart-line accent" style="font-size:1.8rem;"></i>
                    <div style="color:#fff; font-weight:700; margin-top:0.5rem;">Laporan Keuangan</div>
                    <div style="color:#8a8a8a; font-size:0.85rem;">Laba rugi otomatis</div>
                </div>
                <div class="col-md-3 col-6">
                    <i class="fas fa-shield-alt accent" style="font-size:1.8rem;"></i>
                    <div style="color:#fff; font-weight:700; margin-top:0.5rem;">Garansi & Kas Harian</div>
                    <div style="color:#8a8a8a; font-size:0.85rem;">Terlacak otomatis</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="text-center" style="padding: 3rem 0; border-top: 1px solid rgba(255,255,255,0.08); color: #6c6c6c;">
        &copy; {{ date('Y') }} {{ $namaToko }}. All rights reserved.
    </footer>

    <a href="https://wa.me/6281234567890" target="_blank" class="whatsapp-fab">
        <i class="fab fa-whatsapp mr-1"></i> Hubungi Kami
    </a>

</body>
</html>