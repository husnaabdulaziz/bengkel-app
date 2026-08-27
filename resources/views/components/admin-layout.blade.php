<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bengkel') }} - {{ $title ?? 'Dashboard' }}</title>

    @php
        $colorDefaults = [
            'primary_color'      => '#007bff',
            'link_color'         => '#007bff',
            'active_menu_color'  => '#ffc107',
            'sidebar_color'      => '#343a40',
            'success_color'      => '#28a745',
            'danger_color'       => '#dc3545',
            'warning_color'      => '#ffc107',
            'hover_color'        => '#f4f4f4',
            'brand_bg_color'     => '#ffc107',
            'brand_text_color'   => '#1f2d3d',
        ];
        $themeColors = $colorDefaults;
        if (auth()->check()) {
            $saved = \App\Models\StoreSetting::where('company_id', auth()->user()->company_id)
                ->whereNull('branch_id')
                ->whereIn('setting_key', array_keys($colorDefaults))
                ->pluck('setting_value', 'setting_key');
            $themeColors = array_merge($colorDefaults, $saved->toArray());
        }
    @endphp
    <style>
        [x-cloak] { display: none !important; }

        /* Sidebar */
        .main-sidebar { background-color: {{ $themeColors['sidebar_color'] }} !important; }
        /* Logo & Nama Toko */
        .brand-link { background-color: {{ $themeColors['brand_bg_color'] }} !important; }
        .brand-link .brand-text { color: {{ $themeColors['brand_text_color'] }} !important; }

        /* Menu aktif */
        .nav-sidebar > .nav-item > .nav-link.active,
        .nav-sidebar .nav-treeview .nav-link.active {
            background-color: {{ $themeColors['active_menu_color'] }} !important;
            color: #1f2d3d !important;
        }
        .nav-sidebar > .nav-item > .nav-link.active .nav-icon,
        .nav-sidebar .nav-treeview .nav-link.active .nav-icon,
        .nav-sidebar .nav-treeview .nav-link.active .far {
            color: #1f2d3d !important;
        }

        /* Warna Hover */
        .nav-sidebar .nav-link:not(.active):hover {
            background-color: {{ $themeColors['hover_color'] }} !important;
        }
        .table-hover tbody tr:hover {
            background-color: {{ $themeColors['hover_color'] }} !important;
        }
        .list-group-item-action:hover {
            background-color: {{ $themeColors['hover_color'] }} !important;
        }

        /* Paksa menu sidebar tetap horizontal di semua ukuran layar */
        .nav-sidebar .nav-link { display: flex !important; flex-direction: row !important; align-items: center !important; }
        .nav-sidebar .nav-link .nav-icon, .nav-sidebar .nav-link .far { margin-right: 0.5rem; flex-shrink: 0; }
        .nav-sidebar .nav-link p { margin: 0 !important; white-space: normal; }

        /* Warna Utama */
        .btn-primary { background-color: {{ $themeColors['primary_color'] }} !important; border-color: {{ $themeColors['primary_color'] }} !important; }
        .btn-outline-primary { color: {{ $themeColors['primary_color'] }} !important; border-color: {{ $themeColors['primary_color'] }} !important; }
        .btn-outline-primary:hover { background-color: {{ $themeColors['primary_color'] }} !important; color: #fff !important; }
        .badge-primary { background-color: {{ $themeColors['primary_color'] }} !important; }
        .page-item.active .page-link { background-color: {{ $themeColors['primary_color'] }} !important; border-color: {{ $themeColors['primary_color'] }} !important; }
        .custom-control-input:checked ~ .custom-control-label::before { background-color: {{ $themeColors['primary_color'] }} !important; border-color: {{ $themeColors['primary_color'] }} !important; }

        /* Warna Link */
        a, .page-link { color: {{ $themeColors['link_color'] }}; }
        a:hover, .page-link:hover { color: {{ $themeColors['link_color'] }}; opacity: 0.85; }

        /* Warna Sukses */
        .btn-success { background-color: {{ $themeColors['success_color'] }} !important; border-color: {{ $themeColors['success_color'] }} !important; }
        .btn-outline-success { color: {{ $themeColors['success_color'] }} !important; border-color: {{ $themeColors['success_color'] }} !important; }
        .badge-success { background-color: {{ $themeColors['success_color'] }} !important; }
        .alert-success { background-color: {{ $themeColors['success_color'] }}22; border-color: {{ $themeColors['success_color'] }}; color: {{ $themeColors['success_color'] }}; }
        .text-success { color: {{ $themeColors['success_color'] }} !important; }
        .small-box.bg-success { background-color: {{ $themeColors['success_color'] }} !important; }

        /* Warna Bahaya */
        .btn-danger { background-color: {{ $themeColors['danger_color'] }} !important; border-color: {{ $themeColors['danger_color'] }} !important; }
        .btn-outline-danger { color: {{ $themeColors['danger_color'] }} !important; border-color: {{ $themeColors['danger_color'] }} !important; }
        .btn-outline-danger:hover { background-color: {{ $themeColors['danger_color'] }} !important; color: #fff !important; }
        .badge-danger { background-color: {{ $themeColors['danger_color'] }} !important; }
        .alert-danger { background-color: {{ $themeColors['danger_color'] }}22; border-color: {{ $themeColors['danger_color'] }}; color: {{ $themeColors['danger_color'] }}; }
        .text-danger { color: {{ $themeColors['danger_color'] }} !important; }
        .small-box.bg-danger { background-color: {{ $themeColors['danger_color'] }} !important; }

        /* Warna Peringatan */
        .btn-warning { background-color: {{ $themeColors['warning_color'] }} !important; border-color: {{ $themeColors['warning_color'] }} !important; }
        .badge-warning { background-color: {{ $themeColors['warning_color'] }} !important; }
        .alert-warning { background-color: {{ $themeColors['warning_color'] }}22; border-color: {{ $themeColors['warning_color'] }}; color: #664d03; }
        .small-box.bg-warning { background-color: {{ $themeColors['warning_color'] }} !important; }
    </style>

    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
</head>
<body class="hold-transition sidebar-mini layout-fixed" x-data="{ userMenuOpen: false }"
      @click="if (window.innerWidth < 992 && document.body.classList.contains('sidebar-open') && !$event.target.closest('.main-sidebar') && !$event.target.closest('[data-widget=pushmenu]')) { document.body.classList.remove('sidebar-open'); }">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link" href="#" role="button" onclick="event.preventDefault(); if (window.innerWidth < 992) { document.body.classList.toggle('sidebar-open'); } else { document.body.classList.toggle('sidebar-collapse'); } event.stopPropagation();"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item ml-2">
            <a href="{{ route('pos.create') }}" class="btn btn-warning btn-sm text-dark font-weight-bold">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </li>
    </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown" @click.outside="userMenuOpen = false">
    <a class="nav-link" href="#" @click.prevent="userMenuOpen = !userMenuOpen">
        <i class="far fa-user"></i> {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" :class="{ 'show': userMenuOpen }" style="position: absolute; right: 0;">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Log Out</button>
                </form>
            </div>
        </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link d-flex flex-column align-items-center justify-content-center py-2">
            @if (auth()->user()->company?->logo_path)
                <img src="{{ asset('storage/' . auth()->user()->company->logo_path) }}" alt="Logo" class="mb-1" style="max-width: 70%; max-height: 60px; object-fit: contain;">
            @endif
            <span class="brand-text font-weight-bold text-center" style="font-size: 0.95rem; line-height: 1.2;">{{ auth()->user()->company?->nama_toko ?? config('app.name') }}</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    @can('access_dashboard')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    @endcan

                    @can('access_pos')
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('pos.*') ? 'true' : 'false' }} }" :class="{ 'menu-open': open }">
                            <a href="#" class="nav-link" @click.prevent="open = !open">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>POS <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('pos.create') }}" class="nav-link {{ request()->routeIs('pos.create') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Tambah Transaksi</p></a></li>
                            <li class="nav-item"><a href="{{ route('pos.queue') }}" class="nav-link {{ request()->routeIs('pos.queue') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Daftar Transaksi</p></a></li>
                        </ul>
                    </li>
                    @endcan

                    @can('access_produk')
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('products.*', 'product-categories.*', 'product-subcategories.*', 'product-brands.*', 'suppliers.*') ? 'true' : 'false' }} }" :class="{ 'menu-open': open }">
                        <a href="#" class="nav-link" @click.prevent="open = !open">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Produk <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List Produk</p></a></li>
                            <li class="nav-item"><a href="{{ route('product-categories.index') }}" class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Kategori</p></a></li>
                            <li class="nav-item"><a href="{{ route('product-subcategories.index') }}" class="nav-link {{ request()->routeIs('product-subcategories.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Sub Kategori</p></a></li>
                            <li class="nav-item"><a href="{{ route('product-brands.index') }}" class="nav-link {{ request()->routeIs('product-brands.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Brand</p></a></li>
                            <li class="nav-item"><a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Vendor</p></a></li>
                        </ul>
                    </li>
                    @endcan

                    @can('access_garansi')
                    <li class="nav-item">
                        <a href="{{ route('warranties.index') }}" class="nav-link {{ request()->routeIs('warranties.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Garansi</p>
                        </a>
                    </li>
                    @endcan

                    @can('access_pembelian')
                    <li class="nav-item">
                        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Pembelian</p>
                        </a>
                    </li>
                    @endcan

                    @can('access_stock')
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('stock-opnames.*', 'stock-transfers.*') ? 'true' : 'false' }} }" :class="{ 'menu-open': open }">
                    <a href="#" class="nav-link" @click.prevent="open = !open">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Stock <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('stock-opnames.index') }}" class="nav-link {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Stock Opname</p></a></li>
                            <li class="nav-item"><a href="{{ route('stock-transfers.index') }}" class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Transfer Stock</p></a></li>
                        </ul>
                    </li>
                    @endcan

                    @can('access_mekanik')
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('technicians.*', 'reports.technician-fee*', 'technician-manual-fees.*') ? 'true' : 'false' }} }" :class="{ 'menu-open': open }">
                        <a href="#" class="nav-link" @click.prevent="open = !open">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Fee Mekanik <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Mekanik</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.technician-fee') }}" class="nav-link {{ request()->routeIs('reports.technician-fee*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Fee</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('technician-manual-fees.index') }}" class="nav-link {{ request()->routeIs('technician-manual-fees.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Fee Manual</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can('access_pelanggan')
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-address-book"></i>
                            <p>Pelanggan</p>
                        </a>
                    </li>
                    @endcan

                    @canany(['access_laporan_keuangan', 'access_kas_harian'])
                    <li class="nav-item" x-data="{ open: {{ request()->routeIs('reports.financial*', 'expenses.*', 'reports.technician-fee*', 'cash-closings.*') ? 'true' : 'false' }} }" :class="{ 'menu-open': open }">
                        <a href="#" class="nav-link" @click.prevent="open = !open">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>Laporan Keuangan <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('access_laporan_keuangan')
                            <li class="nav-item">
                                <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lap. Laba Rugi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.financial.sales-detail') }}" class="nav-link {{ request()->routeIs('reports.financial.sales-detail') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lap. Penjualan Detail</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lap. Pengeluaran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.technician-fee') }}" class="nav-link {{ request()->routeIs('reports.technician-fee*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lap. Fee Mekanik</p>
                                </a>
                            </li>
                            @endcan
                            @can('access_kas_harian')
                            <li class="nav-item">
                                <a href="{{ route('cash-closings.today') }}" class="nav-link {{ request()->routeIs('cash-closings.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kas Harian</p>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @can('access_log_aktivitas')
                    <li class="nav-item">
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Log Aktivitas</p>
                        </a>
                    </li>
                    @endcan

                    @can('access_pengaturan_toko')
                    <li class="nav-item">
                        <a href="{{ route('settings.edit') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan Toko</p>
                        </a>
                    </li>
                    @endcan
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">{{ $title ?? 'Dashboard' }}</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                {{ $slot }}
            </div>
        </section>
    </div>

        <footer class="main-footer">
        <strong>&copy; {{ date('Y') }} Bengkel Jaya.</strong>
    </footer>
</div>

@stack('scripts')
</body>
</html>