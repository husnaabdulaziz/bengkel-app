<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bengkel') }} - {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
</head>
<body class="hold-transition sidebar-mini layout-fixed" x-data="{ sidebarCollapsed: false, userMenuOpen: false }" :class="{ 'sidebar-collapse': sidebarCollapsed }">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" role="button" @click.prevent="sidebarCollapsed = !sidebarCollapsed"><i class="fas fa-bars"></i></a>
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

                    <li class="nav-item {{ request()->routeIs('products.*', 'product-categories.*', 'product-subcategories.*', 'product-brands.*', 'suppliers.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
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

                    <li class="nav-item {{ request()->routeIs('stock-opnames.*', 'stock-transfers.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Stock <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('stock-opnames.index') }}" class="nav-link {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Stock Opname</p></a></li>
                            <li class="nav-item"><a href="{{ route('stock-transfers.index') }}" class="nav-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Transfer Stock</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item {{ request()->routeIs('pos.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>POS <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('pos.create') }}" class="nav-link {{ request()->routeIs('pos.create') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Tambah Transaksi</p></a></li>
                            <li class="nav-item"><a href="{{ route('pos.queue') }}" class="nav-link {{ request()->routeIs('pos.queue') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Daftar Transaksi</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Mekanik</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.technician-fee') }}" class="nav-link {{ request()->routeIs('reports.technician-fee*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Fee Mekanik</p>
                        </a>
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