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
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Dropdown: Produk -->
                    <div class="relative flex items-center" x-data="{ openProduk: false }" @click.outside="openProduk = false">
                        <button @click="openProduk = !openProduk"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('product-brands.*') || request()->routeIs('suppliers.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ __('Produk') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openProduk" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">List Produk</a>
                            <a href="{{ route('product-categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kategori Produk</a>
                            <a href="{{ route('product-brands.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Brand</a>
                            <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Supplier</a>
                        </div>
                    </div>

                    <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                        {{ __('Pembelian') }}
                    </x-nav-link>

                    <!-- Dropdown: Stock -->
                    <div class="relative flex items-center" x-data="{ openStock: false }" @click.outside="openStock = false">
                        <button @click="openStock = !openStock"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('stock-opnames.*') || request()->routeIs('stock-transfers.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ __('Stock') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openStock" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('stock-opnames.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Stock Opname</a>
                            <a href="{{ route('stock-transfers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Transfer Stock</a>
                        </div>
                    </div>

                    <!-- Dropdown: POS -->
                    <div class="relative flex items-center" x-data="{ openPos: false }" @click.outside="openPos = false">
                        <button @click="openPos = !openPos"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                            {{ request()->routeIs('pos.*')
                                ? 'border-indigo-400 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ __('POS') }}
                            <svg class="w-3 h-3 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openPos" x-transition x-cloak
                             class="absolute z-50 top-full left-0 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1">
                            <a href="{{ route('pos.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tambah Transaksi</a>
                            <a href="{{ route('pos.queue') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daftar Transaksi</a>
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
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Produk</div>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('List Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('product-categories.index')" :active="request()->routeIs('product-categories.*')">
                {{ __('Kategori Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('product-brands.index')" :active="request()->routeIs('product-brands.*')">
                {{ __('Brand') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                {{ __('Supplier') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                {{ __('Pembelian') }}
            </x-responsive-nav-link>

            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Stock</div>
            <x-responsive-nav-link :href="route('stock-opnames.index')" :active="request()->routeIs('stock-opnames.*')">
                {{ __('Stock Opname') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-transfers.index')" :active="request()->routeIs('stock-transfers.*')">
                {{ __('Transfer Stock') }}
            </x-responsive-nav-link>

            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">POS</div>
            <x-responsive-nav-link :href="route('pos.create')" :active="request()->routeIs('pos.create')">
                {{ __('Tambah Transaksi') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pos.queue')" :active="request()->routeIs('pos.queue')">
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