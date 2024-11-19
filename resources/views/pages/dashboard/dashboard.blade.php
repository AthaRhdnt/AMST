@extends('layouts.app')

@section('title', 'Dashboard')

{{-- @section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center">
                    <br>
                    <h2>
                        Selamat Datang, 
                        @if (auth()->user()->role->nama_role == 'Pemilik')
                            Owner STM!
                        @else
                            Outlet {{ $outlet->user->nama_user }}
                        @endif
                    </h2>
                    <h4>Anda login sebagai <span class="badge badge-dark">{{ auth()->user()->role->nama_role }}</span></h4>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Metrics -->
    <div class="row">
        <!-- Active Outlets -->
        @if (auth()->user()->role->nama_role == 'Pemilik')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Active Outlets</h5>
                        <p>{{ $activeOutlets }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Low Stock Items -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Low Stock Items</h5>
                    <ul>
                        @foreach($lowStockItems as $item)
                            <li>{{ $item->stok->nama_barang }}: {{ $item->jumlah }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Total Transactions</h5>
                    <p>{{ $totalTransactions }}</p>
                </div>
            </div>
        </div>

        <!-- Cashier-Specific Transactions -->
        @if (auth()->user()->role->nama_role == 'Kasir')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Your Transactions</h5>
                        <p>{{ $cashierTransactions }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row dashboard-grid">
                        @if (auth()->user()->role->nama_role == 'Kasir')
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('transaksi.create') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-cash-register fa-2x"></i>
                                        <p class="mt-3 ml-2">Kasir</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @elseif (auth()->user()->role->nama_role == 'Pemilik')
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('outlets.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-store fa-2x"></i>
                                        <p class="mt-3 ml-2">Outlet</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('kategori.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-box fa-2x"></i>
                                        <p class="mt-3 ml-2">Kategori</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('stok.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-clipboard-list fa-2x"></i>
                                        <p class="mt-3 ml-2">Stok</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('menu.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-book-open fa-2x"></i>
                                        <p class="mt-3 ml-2">Menu</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('transaksi.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-money-check-alt fa-2x"></i>
                                        <p class="mt-3 ml-2">Riwayat Transaksi</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('riwayat.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-poll fa-2x"></i>
                                        <p class="mt-3 ml-2">Riwayat Stok</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('laporan.index.transaksi') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-chart-line fa-2x"></i>
                                        <p class="mt-3 ml-2">Laporan Transaksi</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('laporan.index.stok') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-chart-line fa-2x"></i>
                                        <p class="mt-3 ml-2">Laporan Stok</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body text-center py-4">
                    <h2 class="mb-3">
                        Selamat Datang, 
                        @if (auth()->user()->role->nama_role == 'Pemilik')
                            Owner STM!
                        @else
                            Outlet {{ $outletName }}
                            {{-- Outlet {{ $outlets->first()->user->nama_user }} --}}
                        @endif
                    </h2>
                    <h4>Anda login sebagai <span class="badge badge-dark">{{ auth()->user()->role->nama_role }}</span></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mb-3">
        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    Penjualan Tahun Ini
                </div>
                <div class="card-body">
                    <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <div class="text-center px-3">
                        <a href="" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    Penjualan Bulan Ini
                </div>
                <div class="card-body">
                    <h3>{{ $transactionsToday }}</h3>
                    <div class="text-center px-3">
                        <a href="" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    Stok Hampir Habis
                </div>
                <div class="card-body">
                    <h3>{{ $lowStockCount }}</h3>
                    <div class="text-center px-3">
                        <a href="" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->role->nama_role == 'Pemilik')
        <div class="col-lg-3 col-md-4 col-sm-6 ">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    Outlet Aktif
                </div>
                <div class="card-body">
                    <h3>{{ $totalOutlets }}</h3>
                    <div class="text-center px-3">
                        <a href="" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <!-- Top-Selling Items Card -->
        <div class="col-md-6 ">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    Menu Terjual
                </div>
                <div class="card-body scrollable-card p-2">
                    <table class="table table-sm table-bordered table-striped">
                        @foreach($topSellingItems as $item)
                            <tr>
                                <td class="pl-2">{{ $item->nama_menu }}</td>
                                <td width="10%" class="text-center">{{ $item->sales_count }}</td>
                                <td width="5%" class="px-3">
                                    <a href="" title="Detail" class="badge badge-dark">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Card -->
        <div class="col-md-6 ">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    Penjualan Hari Ini
                </div>
                <div class="card-body scrollable-card p-2">
                    <table class="table table-sm table-bordered table-striped">
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td class="pl-2">{{ $transaction->outlet->user->nama_user }}</td>
                            <td width="25%" class="text-right">Rp {{ number_format($transaction->total_transaksi, 0, ',', '.') }}</td>
                            <td width="5%" class="px-3">
                                <a href="" title="Detail" class="badge badge-dark">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

