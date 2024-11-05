@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row dashboard-grid">
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
                        @if (auth()->user()->role->nama_role == 'Pemilik')
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('outlet.index') }}" class="nav-link nav-dash">
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
                                        <i class="nav-icon fas fa-folder-open fa-2x"></i>
                                        <p class="mt-3 ml-2">Menu</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-file-invoice fa-2x"></i>
                                        <p class="mt-3 ml-2">Struk</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-money-check-alt fa-2x"></i>
                                        <p class="mt-3 ml-2">Transaksi</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card dashboard-item">
                                <div class="card-body text-center">
                                    <a href="{{ route('transaksi.index') }}" class="nav-link nav-dash">
                                        <i class="nav-icon fas fa-chart-line fa-2x"></i>
                                        <p class="mt-3 ml-2">Laporan</p>
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
@endsection
