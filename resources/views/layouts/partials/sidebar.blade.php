<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link brand">
        <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo" style="opacity: .8" />
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link" style="{{ Route::is('dashboard') ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-columns"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('transaksi.create') }}" class="nav-link" style="{{ Route::is('kasir') ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>
                            Kasir
                        </p>
                    </a>
                </li>

                {{-- @endif --}}

                @if (auth()->user()->role->nama_role == 'Pemilik')

                <li class="nav-header">MANAJEMEN</li>
                <li class="nav-item">
                    <a href="{{ route('outlets.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['outlets.index', 'outlets.create', 'outlets.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Outlet
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kategori.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['kategori.index', 'kategori.create', 'kategori.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Kategori
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stok.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['stok.index', 'stok.create', 'stok.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Stok
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('menu.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['menu.index', 'menu.create', 'menu.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>
                            Menu
                        </p>
                    </a>
                </li>

                @endif

                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item">
                    <a href="{{ route('transaksi.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['transaksi.index', 'transaksi.create', 'transaksi.edit']) ? 'color: white; background-color: #8266a9;' : '' }}"
                        <i class="nav-icon fas fa-poll"></i>
                        <p>
                            Laporan Transaksi
                        </p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('pembelian.index') }}" class="nav-link {{(Route::is('pembelian.index')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>
                            Pembelian
                        </p>
                    </a>
                </li> --}}
                {{-- <li class="nav-item">
                    <a href="{{ route('penjualan.index') }}" class="nav-link {{(Route::is('penjualan.index')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>
                            Penjualan
                        </p>
                    </a>
                </li> --}}
                {{-- <li class="nav-item">
                    <a href="{{ route('jual.index') }}" class="nav-link {{(Route::is('jual.index')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>
                            Transaksi
                        </p>
                    </a>
                </li> --}}

                <li class="nav-header">LAPORAN</li>
                {{-- <li class="nav-item">
                    <a href="{{ route('laporan.index') }}" class="nav-link {{(Route::is('laporan.index')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Laporan Pendapatan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.labarugi') }}" class="nav-link {{(Route::is('laporan.labarugi')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Laporan Omset
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('terlaris.index') }}" class="nav-link {{(Route::is('terlaris.index')) ? 'nav-link active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Laporan Barang
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
