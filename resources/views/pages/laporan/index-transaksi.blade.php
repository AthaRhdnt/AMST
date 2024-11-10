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
                        <form method="GET" action="{{ route('laporan.index.transaksi') }}" id="entries-form" class="d-flex align-items-center">
                            <label for="entries" class="mr-2 mb-0 fw-normal">Menampilkan</label>
                            <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                <option value="5" {{ session('lapooran_transaksi_entries') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ session('lapooran_transaksi_entries') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ session('lapooran_transaksi_entries') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ session('lapooran_transaksi_entries') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ session('lapooran_transaksi_entries') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="ml-2 mb-0">data</span>
        
                            <input type="hidden" name="outlet_id" value="{{ session('outlet_id') }}">
                            <input type="hidden" name="start_date" value="{{ session('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                        </form>
                        <div class="d-flex align-items-center justify-content-end">
                            <form method="GET" action="{{ route('laporan.index.transaksi') }}">
                                <div class="row">
                                    @if (auth()->user()->role->nama_role == 'Pemilik')
                                    <div class="col">
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
                                            <input type="hidden" name="start_date" value="{{ session('start_date') }}">
                                            <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
                                        </form>
                                    </div>
                                    @endif
                                    <div class="col">
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
                                    <div class="col">
                                        <input type="date" name="start_date" value="{{ session('start_date') }}" class="form-control" placeholder="Start Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="col">
                                        <input type="date" name="end_date" value="{{ session('end_date', now()->toDateString()) }}" class="form-control" placeholder="End Date" onchange="this.form.submit()">
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('laporan.reset') }}" class="btn my-btn"><i class="fas fa-times"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th width="5%">No</th>
                            <th>Outlet</th>
                            <th>Tanggal</th>
                            <th>Total Pembelian</th>
                            <th>Total Penjualan</th>
                        </thead>
                        <tbody>
                            @foreach ($transaksi as $data)
                                <tr>
                                    <td>{{ ($transaksi->currentPage() - 1) * $transaksi->perPage() + $loop->iteration }}</td>
                                    <td>Outlet {{ $data->outlet->user->nama_user }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ number_format($data->total_pembelian, 2) }}</td>
                                    <td>{{ number_format($data->total_penjualan, 2) }}</td>
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

<!-- Confirmation modal -->
{{-- <div id="deleteConfirmCard" 
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
        background-color: rgba(0, 0, 0, 0.5); z-index: 1000; display: none; 
        justify-content: center; align-items: center; pointer-events: all;">
    <div class="card" style="width: 300px; z-index: 1010; pointer-events: all;">
        <div class="card-body">
            <h5 class="card-title text-center">Confirm Deletion</h5>
            <p class="card-text text-center">Are you sure you want to delete this kategori?</p>

            <!-- Error message for invalid password -->
            @if ($errors->has('admin_password'))
                <div class="text-center text-danger mb-3">
                    {{ $errors->first('admin_password') }}
                </div>
            @endif

            <input id="adminPassword" 
                    type="password" 
                    class="form-control mb-3" 
                    placeholder="Enter admin password" required>
            <div class="text-center">
                <button id="confirmBtn" class="btn btn-danger">Confirm</button>
                <button id="cancelBtn" class="btn btn-secondary ml-2">Cancel</button>
            </div>
        </div>
    </div>
</div> --}}

{{-- <script>
    function confirmDelete(id) {
        // Show the confirmation modal
        document.getElementById('deleteConfirmCard').style.display = 'flex';

        // Set up the confirmation button
        document.getElementById('confirmBtn').onclick = function() {
            var adminPassword = document.getElementById('adminPassword').value;

            if (adminPassword) {
                // Create a hidden input to pass the password in the form
                var form = document.getElementById('deleteForm' + id);
                var passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'admin_password';
                passwordInput.value = adminPassword;
                form.appendChild(passwordInput);

                // Submit the form
                form.submit();
            } else {
                alert('Please enter the admin password.');
            }
        };

        // Cancel button logic
        document.getElementById('cancelBtn').onclick = function() {
            // Hide the modal
            document.getElementById('deleteConfirmCard').style.display = 'none';
        };
    }

    // Reopen modal if there was a validation error
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->has('admin_password'))
            document.getElementById('deleteConfirmCard').style.display = 'flex';
        @endif
    });
</script> --}}
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
