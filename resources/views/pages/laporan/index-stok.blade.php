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
                                    <span class="ml-1 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('laporan_stok_search', '') }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <form method="GET" action="{{ route('laporan.index.stok') }}">
                                <div class="row">
                                    <div class="mx-1    ">
                                        <a href="{{ route('laporan.transaksi.pdf') }}" class="btn my-btn">
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
                                                <input type="hidden" name="start_date" value="{{ session('start_date', now()->toDateString()) }}">
                                                <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                                            </form>
                                        </div>
                                    @else
                                        <div class="mx-1">
                                            <!-- Automatically Set Outlet ID -->
                                            <form method="GET" action="{{ route('laporan.index.stok') }}" class="d-flex align-items-center">
                                                <input type="hidden" name="search" value="{{ session('laporan_stok_search', '') }}">    
                                                <input type="hidden" name="outlet_id" value="{{ auth()->user()->id_outlet }}">
                                                <input type="hidden" name="start_date" value="{{ session('start_date', now()->toDateString()) }}">
                                                <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
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
                                        <input type="date" name="start_date" value="{{ session('start_date', now()->toDateString()) }}" class="form-control" placeholder="Start Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="mx-1">
                                        <input type="date" name="end_date" value="{{ session('end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="mr-1">
                                        <a href="{{ route('laporan.reset') }}" class="btn my-btn"><i class="fas fa-times"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped mt-2">
                        <thead class = "text-center">
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
                                <tr>
                                    <td>{{ $data->nama_barang }}</td>
                                    <td class = "text-center">{{ session('outlet_id') == '' ? $data->sum_stok_awal : $data->stok_awal}}</td>
                                    <td class = "text-center">{{ $data->jumlah_tambah }}</td>
                                    <td class = "text-center" style="color: red;">({{ abs($data->jumlah_kurang) }})</td>
                                    <td class = "text-center">{{ $data->jumlah_beli }}</td>
                                    <td class = "text-center" style="color: red;">({{ abs($data->jumlah_pakai) }})</td>
                                    <td class = "text-center">{{ session('outlet_id') == '' ? $data->sum_stok_akhir : $data->stok_akhir}}</td>
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

<script>
    $(function() {
        $('#date-range').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment(), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            }
        });

        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
            $('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
            $(this).closest('form').submit(); // Trigger form submit
        });

        $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
            $('input[name="start_date"]').val('');
            $('input[name="end_date"]').val('');
            $(this).closest('form').submit(); // Trigger form submit
        });
    });

    function setDateRange(range) {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        const today = new Date();
        let startDate;
        let endDate;

        switch (range) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                break;
            case 'this_year':
                startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                break;
            case 'last_7_days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 6);
                startDate = startDate.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_30_days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 29);
                startDate = startDate.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            default:
                return;
        }

        startDateInput.value = startDate;
        endDateInput.value = endDate;
        
        // Construct the new URL with query parameters
        const form = startDateInput.closest('form');
        const url = new URL(form.action); // Get the form action URL

        // Append the date range to the URL
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);

        // Remove existing search and entries parameters if needed
        url.searchParams.delete('search');
        url.searchParams.delete('entries');

        // Redirect to the new URL
        window.location.href = url.toString();
    }
</script>
@endsection
