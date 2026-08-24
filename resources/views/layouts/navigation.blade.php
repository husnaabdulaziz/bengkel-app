<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Dropdown: Produk -->
                    <div class="relative flex items-center" x-data="{ openProduk: false }" @click.outside="openProduk = false">
                        <button @click="openProduk = !openProduk"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('product-brands.*') || request()->routeIs('suppliers.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ __('Produk') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openProduk" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                List Produk
                            </a>
                            <a href="{{ route('product-categories.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Kategori Produk
                            </a>
                            <a href="{{ route('product-brands.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Brand
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Supplier
                            </a>
                        </div>
                    </div>

                    <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        {{ __('Pembelian') }}
                    </x-nav-link>

                    <!-- Dropdown: Stock -->
                    <div class="relative flex items-center" x-data="{ openStock: false }" @click.outside="openStock = false">
                        <button @click="openStock = !openStock"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('stock-opnames.*') || request()->routeIs('stock-transfers.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7L12 3 4 7m16 0l-8 4m8-4v10l-8 4m0-6V7m0 10L4 17V7m8 4L4 7"/></svg>
                            {{ __('Stock') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openStock" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('stock-opnames.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Stock Opname
                            </a>
                            <a href="{{ route('stock-transfers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Transfer Stock
                            </a>
                        </div>
                    </div>
                    <!-- Laporan -->
                    <x-nav-link :href="route('reports.technician-fee')" :active="request()->routeIs('reports.technician-fee*')">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                        {{ __('Fee Mekanik') }}
                    </x-nav-link>
                    <x-nav-link :href="route('technician-manual-fees.index')" :active="request()->routeIs('technician-manual-fees.*')">
                        {{ __('Fee Manual') }}
                    </x-nav-link>
                    <x-nav-link :href="route('technicians.index')" :active="request()->routeIs('technicians.*')">
                        {{ __('Mekanik') }}
                    </x-nav-link>
                    <!-- Dropdown: POS -->
                    <div class="relative flex items-center" x-data="{ openPos: false }" @click.outside="openPos = false">
                        <button @click="openPos = !openPos"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('pos.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h1m4-6h10a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                            {{ __('POS') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openPos" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('pos.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Tambah Transaksi
                            </a>
                            <a href="{{ route('pos.queue') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Daftar Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Produk</div>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                {{ __('List Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('product-categories.index')" :active="request()->routeIs('product-categories.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ __('Kategori Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('product-brands.index')" :active="request()->routeIs('product-brands.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                {{ __('Brand') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h11v8H3V7zm11 3h4l3 3v2h-7v-5zM5.5 19a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm12 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>
                {{ __('Supplier') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('Pembelian') }}
            </x-responsive-nav-link>

            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Stock</div>
            <x-responsive-nav-link :href="route('stock-opnames.index')" :active="request()->routeIs('stock-opnames.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                {{ __('Stock Opname') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-transfers.index')" :active="request()->routeIs('stock-transfers.*')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                {{ __('Transfer Stock') }}
            </x-responsive-nav-link>

            <!-- Laporan -->
            <x-nav-link :href="route('reports.technician-fee')" :active="request()->routeIs('reports.technician-fee*')">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                {{ __('Fee Mekanik') }}
            </x-nav-link>
            <x-nav-link :href="route('technician-manual-fees.index')" :active="request()->routeIs('technician-manual-fees.*')">
                {{ __('Fee Manual') }}
            </x-nav-link>
            <x-nav-link :href="route('technicians.index')" :active="request()->routeIs('technicians.*')">
                {{ __('Mekanik') }}
            </x-nav-link>


            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">POS</div>
            <x-responsive-nav-link :href="route('pos.create')" :active="request()->routeIs('pos.create')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Transaksi') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pos.queue')" :active="request()->routeIs('pos.queue')">
                <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('Daftar Transaksi') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
