<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bengkel') }} - {{ $title ?? 'Dashboard' }}</title>
    <style>[x-cloak] { display: none !important; }</style>
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
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light">Bengkel Jaya</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
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

                    <li class="nav-item">
                        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Pembelian</p>
                        </a>
                    </li>

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