@extends('layouts.app')

@section('title', 'Dashboard')

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

    <div class="row text-center">
        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Penjualan Tahun Ini
                </div>
                <div class="card-body">
                    <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <div class="text-center px-3">
                        <a  href="{{ route('laporan.index.transaksi', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')]) }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Penjualan Bulan Ini
                </div>
                <div class="card-body">
					<h3>Rp {{ number_format($transactionsThisMonth, 0, ',', '.') }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('laporan.index.transaksi', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}"  title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-{{ auth()->user()->role->nama_role == 'Pemilik' ? '3' : '4' }} col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    Stok Hampir Habis
                </div>
                <div class="card-body">
                    <h3>{{ $lowStockCount }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('stok.index') }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->role->nama_role == 'Pemilik')
        <div class="col-lg-3 col-md-4 col-sm-6 ">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Outlet Aktif
                </div>
                <div class="card-body">
                    <h3>{{ $totalOutlets }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('outlets.index') }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row mb-0">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Best Seller
                </div>
                <div class="card-body scrollable-dashboard p-2">
                    <table class="table table-sm table-bordered table-striped" >
                        @foreach($topSellingItems as $item)
                            <tr>
                                <td class="pl-2">{{ $item->nama_menu }}</td>
                                <td width="10%" class="text-center">{{ $item->sales_count }}</td>
                                <td width="5%" class="px-3">
                                    <a href="{{ route('riwayat.index.transaksi', ['search' => $item->nama_menu]) }}" title="Detail" class="badge badge-dark">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
					<div class="mt-3">
                        {{ $topSellingItems->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    Penjualan Hari Ini
                </div>
                <div class="card-body scrollable-dashboard p-2">
                    <table class="table table-sm table-bordered table-striped">
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td class="pl-2">{{ $transaction->outlet->user->nama_user }}</td>
                            <td width="25%" class="text-right">Rp {{ number_format($transaction->total_today, 0, ',', '.') }}</td>
                            <td width="5%" class="px-3">
                                <a href="{{ route('laporan.index.transaksi', ['outlet_id' => $transaction->id_outlet, 'start_date' => now()->today()->format('Y-m-d'), 'end_date' => now()->today()->format('Y-m-d')]) }}" title="Detail" class="badge badge-dark">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
					<div class="mt-3">
                        {{ $recentTransactions->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

