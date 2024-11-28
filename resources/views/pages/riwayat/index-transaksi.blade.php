@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

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
                            <div class="mr-2">
                                <form action="{{ route('riwayat.index.transaksi') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control"
                                        placeholder="Search" value="{{ session('riwayat_transaksi_search', '') }}" />
                                </form>
                            </div>
                            <div class="mx-2">
                                <form method="GET" action="{{ route('riwayat.index.transaksi') }}" id="entries-form" class="d-flex align-items-center">
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('riwayat_transaksi_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('riwayat_transaksi_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('riwayat_transaksi_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('riwayat_transaksi_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('riwayat_transaksi_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="ml-2 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('riwayat_transaksi_search', '') }}">
                                    <input type="hidden" name="start_date" value="{{ session('start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form method="GET" action="{{ route('riwayat.index.transaksi') }}">
                                <div class="row">
                                    @if (auth()->user()->role->nama_role == 'Pemilik')
                                        <div class="mx-2">
                                            <!-- Outlet Selection Form -->
                                            <form method="GET" action="{{ route('riwayat.index.transaksi') }}" class="d-flex align-items-center">
                                                <select name="outlet_id" id="outlet_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                                    <option value="">All Outlets</option>
                                                    @foreach($outlets as $data)
                                                        <option value="{{ $data->id_outlet }}" {{ session('outlet_id') == $data->id_outlet ? 'selected' : '' }}>
                                                            {{ $data->user->nama_user }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="start_date" value="{{ session('start_date', now()->toDateString()) }}">
                                                <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                                            </form>
                                        </div>
                                    @else
                                        <div class="mx-2">
                                            <!-- Automatically Set Outlet ID -->
                                            <form method="GET" action="{{ route('riwayat.index.transaksi') }}" class="d-flex align-items-center">
                                                <input type="hidden" name="outlet_id" value="{{ session('outlet_id'), auth()->user()->id_outlet }}">
                                                <input type="hidden" name="start_date" value="{{ session('start_date', now()->toDateString()) }}">
                                                <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                                            </form>
                                        </div>
                                    @endif
                                    <div class="mx-2">
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
                                    <div>
                                        <input type="date" name="start_date" value="{{ session('start_date', now()->toDateString()) }}" class="form-control" placeholder="Start Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="mx-2">
                                        <input type="date" name="end_date" value="{{ session('end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="mr-2">
                                        <form method="GET" action="{{ route('riwayat.index.transaksi') }}">
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
                            <th width="10%">Tanggal</th>
                            <th width="10%">Waktu</th>
                            <th>Kode Transaksi</th>
                            <th width="15%">Outlet</th>
                            <th width="15%">Pesanan</th>
                            <th width="6%">Jumlah</th>
                            <th width="10%">Harga</th>
                        </thead>                      
                        <tbody>
                            @foreach ($transaksi as $data)
                            {{-- {{$data}} --}}
                                <tr>
                                    <td class="text-center">{{ ($transaksi->currentPage() - 1) * $transaksi->perPage() + $loop->iteration }}</td>
                                    <td class="text-center">{{ $data->transaksi->tanggal_transaksi->format('d-m-Y') }}</td>
                                    <td class="text-center">{{ Carbon\Carbon::parse($data->created_at)->timezone('Asia/Bangkok')->format('H:i:s') }}</td>
                                    <td>{{ $data->transaksi->kode_transaksi}}</td>
                                    <td>{{ $data->transaksi->outlet->user->nama_user }}</td>
                                    <td>{{ $data->transaksi->riwayatStok->first()->menu->nama_menu }}</td>
                                    <td class="text-center">{{ $data->jumlah }}</td>
                                    <td class="text-right">Rp. {{ number_format($data->subtotal) }}</td>
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
