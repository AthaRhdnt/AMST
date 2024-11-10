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
                    <a href="{{ route('transaksi.create') }}" class="nav-link" style="{{ Route::is('transaksi.create') ? 'color: white; background-color: #8266a9;' : '' }}">
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

                @endif

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

                <li class="nav-header">RIWAYAT</li>
                <li class="nav-item">
                    <a href="{{ route('transaksi.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['transaksi.index', 'transaksi.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>
                            Riwayat Transaksi
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('riwayat.index') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['riwayat.index', 'riwayat.create', 'riwayat.edit']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>
                            Riwayat Stok
                        </p>
                    </a>
                </li>

                <li class="nav-header">LAPORAN</li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index.transaksi') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['laporan.index.transaksi']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Laporan Transaksi
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index.stok') }}" class="nav-link" style="{{ in_array(Route::currentRouteName(), ['laporan.index.stok']) ? 'color: white; background-color: #8266a9;' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Laporan Stok
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
