@extends('layouts.app')

@section('title', 'Laporan Stok')

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
                        <div class="d-flex justify-content-start">
                            <div class="mr-1">
                                <form action="{{ route('laporan.index.stok') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control"
                                        placeholder="Search" value="{{ session('laporan_stok_search', '') }}" />
                                </form>
                            </div>
                            <div class="mx-1">
                                <form method="GET" action="{{ route('laporan.index.stok') }}" id="entries-form" class="d-flex align-items-center">
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('laporan_stok_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('laporan_stok_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('laporan_stok_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('laporan_stok_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('laporan_stok_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="mx-1 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('laporan_stok_search', '') }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <form method="GET" action="{{ route('laporan.index.stok') }}">
                                <div class="row">
                                    <div class="mx-1">
                                        <a href="{{ route('laporan.pdf.stok') }}" class="btn my-btn">
                                            <i class="nav-icon fas fa-print"></i>
                                        </a>
                                    </div>
                                    @if (auth()->user()->role->nama_role == 'Pemilik')
                                        <div class="mx-1">
                                            <!-- Outlet Selection Form -->
                                            <form method="GET" action="{{ route('laporan.index.stok') }}" class="d-flex align-items-center">
                                                <select name="outlet_id" id="outlet_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                                    <option value="">All Outlets</option>
                                                    @foreach($outlets as $data)
                                                        <option value="{{ $data->id_outlet }}" {{ session('outlet_id') == $data->id_outlet ? 'selected' : '' }}>
                                                            {{ $data->user->nama_user }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="search" value="{{ session('laporan_stok_search', '') }}">    
                                                <input type="hidden" name="start_date" value="{{ session('l_stok_start_date', now()->toDateString()) }}">
                                                <input type="hidden" name="end_date" value="{{ session('l_stok_end_date', now()->toDateString()) }}">
                                            </form>
                                        </div>
                                    @endif
                                    <div class="mx-1">
                                        <div class="dropdown user-menu">
                                            <a href="#" class="btn my-btn dropdown-toggle" id="dateRangeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dateRangeDropdown">
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
                                        <input type="date" name="start_date" value="{{ session('l_stok_start_date', now()->toDateString()) }}" class="form-control" placeholder="Start Date" max="{{ session('l_stok_end_date', now()->toDateString()) }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="mx-1">
                                        <input type="date" name="end_date" value="{{ session('l_stok_end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" min="{{ session('l_stok_start_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="mr-1">
                                        <form method="GET" action="{{ route('laporan.index.stok') }}">
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
                            <th>Nama Item</th>
                            <th>Stok Awal</th>
                            <th>Update <i class="far fa-plus-square"></i></th>
                            <th>Update <i class="far fa-minus-square"></i></th>
                            <th>Pembelian</th>
                            <th>Terpakai</th>
                            <th>Stok Akhir</th>
                        </thead>
                        <tbody>
                            @foreach ($stok as $data)
                            {{-- {{$data}} --}}
                                <tr>
                                    <td>{{ $data->nama_barang }}</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_stok_awal : $data->stok_awal}}</td>
                                    <td class="text-center">{{ $data->jumlah_tambah }}</td>
                                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_kurang) }})</td>
                                    <td class="text-center">{{ $data->jumlah_beli }}</td>
                                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_pakai) }})</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_stok_akhir : $data->stok_akhir}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $stok->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loading-overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Apply loading overlay on any change or input event
        const allInputs = document.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            ['change'].forEach(eventType => {
                input.addEventListener(eventType, () => {
                    document.getElementById('loading-overlay').style.display = 'block';
                    const form = input.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            });
        });

        // Apply loading overlay on dropdown link clicks
        const dropdownLinks = document.querySelectorAll('.dropdown-item a.menu-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('loading-overlay').style.display = 'block';
            });
        });

        // Apply loading overlay on pagination link clicks
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent default link behavior
                document.getElementById('loading-overlay').style.display = 'block';
                window.location.href = link.href; // Redirect to the clicked page
            });
        });
    });
</script>
@endsection
