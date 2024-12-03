@extends('layouts.app')

@section('title', 'Laporan Transaksi')

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
                                <form method="GET" action="{{ route('laporan.index.transaksi') }}" class="d-flex align-items-center">
                                    <select name="kode_transaksi" id="kode_transaksi" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="ORD-" {{ session('kode_transaksi') == 'ORD-' ? 'selected' : '' }}>Jual</option>
                                        <option value="BUY-" {{ session('kode_transaksi') == 'BUY-' ? 'selected' : '' }}>Beli</option>
                                    </select>
                                    <input type="hidden" name="outlet_id" value="{{ session('outlet_id') }}">
                                    <input type="hidden" name="start_date" value="{{ session('transaksi_start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('transaksi_end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                            <div class="mx-1">
                                <form method="GET" action="{{ route('laporan.index.transaksi') }}" id="entries-form" class="d-flex align-items-center">
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('laporan_transaksi_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('laporan_transaksi_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('laporan_transaksi_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('laporan_transaksi_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('laporan_transaksi_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="mx-1 mb-0">data</span>
                                    <input type="hidden" name="outlet_id" value="{{ session('outlet_id') }}">
                                    <input type="hidden" name="kode_transaksi" value="{{ session('kode_transaksi') }}">
                                    <input type="hidden" name="start_date" value="{{ session('transaksi_start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('transaksi_end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <form method="GET" action="{{ route('laporan.index.transaksi') }}">
                                <div class="row">
                                    <div class="mx-1">
                                        <a href="{{ route('laporan.pdf.transaksi') }}" class="btn my-btn">
                                            <i class="nav-icon fas fa-print"></i>
                                        </a>
                                    </div>
                                    @if (auth()->user()->role->nama_role == 'Pemilik')
                                    <div class="mx-1">
                                        <!-- Outlet Selection Form -->
                                        <form method="GET" action="{{ route('laporan.index.transaksi') }}" class="d-flex align-items-center">
                                            <select name="outlet_id" id="outlet_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                                <option value="">All Outlets</option>
                                                @foreach($outlets as $data)
                                                    <option value="{{ $data->id_outlet }}" {{ session('outlet_id') == $data->id_outlet ? 'selected' : '' }}>
                                                        {{ $data->user->nama_user }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="kode_transaksi" value="{{ session('kode_transaksi') }}">
                                            <input type="hidden" name="start_date" value="{{ session('transaksi_start_date', now()->toDateString()) }}">
                                            <input type="hidden" name="end_date" value="{{ session('transaksi_end_date', now()->toDateString()) }}">
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
                                        <input type="date" name="start_date" value="{{ session('transaksi_start_date', now()->toDateString()) }}" class="form-control" placeholder="Start Date" max="{{ session('transaksi_end_date', now()->toDateString()) }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="mx-1">
                                        <input type="date" name="end_date" value="{{ session('transaksi_end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" min="{{ session('transaksi_start_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="mr-1">
                                        <form method="GET" action="{{ route('laporan.index.transaksi') }}">
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
                    <table class="table table-sm table-bordered table-striped" style="border-radius: 0.85rem">
                        <thead class="text-center">
                            <th width="5%">No</th>
                            <th width="15%">Outlet</th>
                            <th width="17%">Kode Transaksi</th>
                            <th width="10%">Tanggal</th>
                            <th width="5%">Waktu</th>
                            <th>Pesanan</th>
                            <th width="10%">Total</th>
                            <th width="5%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $data)
                                <tr>
                                    <td class="text-center">{{ ($transaksi->currentPage() - 1) * $transaksi->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->outlet->user->nama_user }}</td>
                                    <td>{{ $data->kode_transaksi }}</td>
                                    <td class="text-center">{{ $data->tanggal_transaksi->format('d-m-Y') }}</td>
                                    <td class="text-center">{{ Carbon\Carbon::parse($data->created_at)->timezone('Asia/Bangkok')->format('H:i:s') }}</td>
                                    <td>
                                        @if ($data->detailTransaksi && $data->detailTransaksi->isNotEmpty())
                                            @foreach ($data->detailTransaksi as $detil)
                                                {{ $detil->menu->nama_menu }} ({{ $detil->jumlah }}){{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        @elseif ($data->detailPembelian && $data->detailPembelian->isNotEmpty())
                                            @foreach ($data->detailPembelian as $detil)
                                                {{ $detil->stok->nama_barang }} ({{ $detil->jumlah }}){{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-right">Rp. {{ number_format($data->total_transaksi) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openPreviewModal({{ $data->id_transaksi }})">
                                            <i class="nav-icon fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $transaksi->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <div class="card-header my-bg text-white">
                <label class="my-0 fw-bold">Preview</label>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body scrollable-card p-1" id="previewContent">
                <!-- Preview content will be injected here -->
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="printBtn">Cetak</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
