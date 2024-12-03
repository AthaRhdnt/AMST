@extends('layouts.app')

@section('title', 'Laporan Finansial')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card x-ovfl-hid">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title') {{ $outletName }}</label>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center justify-content-start">
                            <div class="mr-1">
                                <form method="GET" action="{{ route('laporan.index.finansial') }}" id="entries-form" class="d-flex align-items-center">
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('laporan_finansial_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('laporan_finansial_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('laporan_finansial_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('laporan_finansial_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('laporan_finansial_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="mx-1 mb-0">data</span>
                                    <input type="hidden" name="outlet_id" value="{{ session('outlet_id') }}">
                                    <input type="hidden" name="start_date" value="{{ session('finansial_start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('finansial_end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <form method="GET" action="{{ route('laporan.index.finansial') }}">
                                <div class="row">
                                    <div class="mx-1">
                                        <a href="{{ route('laporan.pdf.finansial') }}" class="btn my-btn">
                                            <i class="nav-icon fas fa-print"></i>
                                        </a>
                                    </div>
                                    @if (auth()->user()->role->nama_role == 'Pemilik')
                                    <div class="mx-1">
                                        <!-- Outlet Selection Form -->
                                        <form method="GET" action="{{ route('laporan.index.finansial') }}" class="d-flex align-items-center">
                                            <select name="outlet_id" id="outlet_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                                <option value="">All Outlets</option>
                                                @foreach($outlets as $data)
                                                    <option value="{{ $data->id_outlet }}" {{ session('outlet_id') == $data->id_outlet ? 'selected' : '' }}>
                                                        {{ $data->user->nama_user }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="start_date" value="{{ session('finansial_start_date', now()->toDateString()) }}">
                                            <input type="hidden" name="end_date" value="{{ session('finansial_end_date', now()->toDateString()) }}">
                                        </form>
                                    </div>
                                    @endif
                                    <div class="mx-1">
                                        <div class="dropdown user-menu">
                                            <a href="#" class="btn my-btn dropdown-toggle" id="dateRangeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="dateRangeDropdown">
                                                <div class="dropdown-item">
                                                    <a class="menu-link" href="#" onclick="setDateRange('today')">Hari Ini</a>
                                                    <a class="menu-link" href="#" onclick="setDateRange('this_month')">Bulan Ini</a>
                                                    <a class="menu-link" href="#" onclick="setDateRange('this_year')">Tahun Ini</a>
                                                    <a class="menu-link" href="#" onclick="setDateRange('last_7_days')">7 Hari Terakhir</a>
                                                    <a class="menu-link" href="#" onclick="setDateRange('last_30_days')">30 Hari Terakhir</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mx-1">
                                        <input type="date" name="start_date" value="{{ session('finansial_start_date', now()->toDateString()) }}" class="form-control" placeholder="Start Date" max="{{ session('finansial_end_date', now()->toDateString()) }}" onchange="this.form.submit()" @if(auth()->user()->role->nama_role == 'Kasir') readonly @endif>
                                    </div>
                                    <div class="mx-1">
                                        <input type="date" name="end_date" value="{{ session('finansial_end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" min="{{ session('finansial_start_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()" @if(auth()->user()->role->nama_role == 'Kasir') readonly @endif>
                                    </div>
                                    <div class="mr-1">
                                        <form method="GET" action="{{ route('laporan.index.finansial') }}">
                                            <button type="submit" name="reset" value="true" class="btn my-btn"><i class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="text-center">
                            <th width="5%">No</th>
                            <th>Outlet</th>
                            <th>Tanggal</th>
                            <th>Total Pembelian</th>
                            <th>Total Penjualan</th>
                        </thead>
                        <tbody>
                            @foreach ($finansial as $data)
                                <tr>
                                    <td class="text-center">{{ ($finansial->currentPage() - 1) * $finansial->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->outlet->user->nama_user }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->format('d-m-Y') }}</td>
                                    <td class="text-right">Rp. {{ number_format($data->total_pembelian) }}</td>
                                    <td class="text-right">Rp. {{ number_format($data->total_penjualan) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <td colspan="3" class="text-right pr-3"> <strong>Total :</strong></td>
                            <td class="text-right">Rp. {{ number_format($finansial->sum('total_pembelian')) }}</td>
                            <td class="text-right">Rp. {{ number_format($finansial->sum('total_penjualan')) }}</td>
                        </tfoot>
                    </table>
                    <div class="mt-3">
                        {{ $finansial->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
