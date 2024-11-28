@extends('layouts.app')

@section('title', 'Stok')

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
                                <form action="{{ route('stok.index') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control form-control-solid w-250px ps-13"
                                        placeholder="Search" value="{{ session('stok_search', '') }}" />
                                </form>

                            </div>
                            <div class="mx-2">
                                <form method="GET" action="{{ route('stok.index') }}" id="entries-form" class="d-flex align-items-center">
                                    <label for="entries" class="mr-2 mb-0 fw-normal">Menampilkan</label>
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('stok_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('stok_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('stok_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('stok_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('stok_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="ml-2 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('stok_search', '') }}">
                                    <input type="hidden" name="start_date" value="{{ session('stok_start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('stok_end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                            @if (auth()->user()->role->nama_role == 'Pemilik')
                            <div class="ml-2">
                                <!-- Outlet Selection Form -->
                                <form method="GET" action="{{ route('stok.index') }}" class="d-flex align-items-center">
                                    <select name="outlet_id" id="outlet_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                        <option value="">All Outlets</option>
                                        @foreach($outlets as $data)
                                            <option value="{{ $data->id_outlet }}" {{ session('outlet_id') == $data->id_outlet ? 'selected' : '' }}>
                                                {{ $data->user->nama_user }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="search" value="{{ session('stok_search', '') }}">
                                    <input type="hidden" name="start_date" value="{{ session('stok_start_date', now()->toDateString()) }}">
                                    <input type="hidden" name="end_date" value="{{ session('stok_end_date', now()->toDateString()) }}">
                                </form>
                            </div>
                            @else
                                <div class="mx-1">
                                    <!-- Automatically Set Outlet ID -->
                                    <form method="GET" action="{{ route('laporan.index.stok') }}" class="d-flex align-items-center">
                                        <input type="hidden" name="outlet_id" value="{{ session('outlet_id'), auth()->user()->id_outlet }}">
                                        <input type="hidden" name="search" value="{{ session('laporan_stok_search', '') }}">    
                                        <input type="hidden" name="start_date" value="{{ session('stok_start_date', now()->toDateString()) }}">
                                        <input type="hidden" name="end_date" value="{{ session('stok_end_date', now()->toDateString()) }}">
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end place-item-auto">
                            <a href="{{ route('stok.create') }}" class="btn my-btn">
                                <i class="fas fa-plus mr-2"></i> Tambah Stok
                            </a>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped mt-2">
                        <thead class="text-center">
                            <th width="5%">No</th>
                            <th>Nama Barang</th>
                            <th width="9%">Minimum</th>
                            <th width="9%">Stok Akhir</th>
                            <th width="7%">Info</th>
                            <th width="15%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($stok as $data)
                            {{-- {{$data->outlet->transaksi->last()->riwayatStok->last()}} --}}
                                {{-- <tr>
                                    <td class="text-center">{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->stok->nama_barang }}</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_minimum : $data->minimum}}</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_stok_akhir : $data->stok_akhir}}</td>
                                    <td class="text-center">
                                        @if ($data->stok_akhir == 0)
                                            <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                        @elseif ($data->stok_akhir > 0 && $data->stok_akhir <= $data->minimum)
                                            <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                        @else
                                            <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                        @endif
                                    </td>
                                    @if (session('outlet_id') == '')
                                        <td class="text-center">
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openPembelianModal({{ session('outlet_id') }}, {{ $data->id_barang }}, '{{ $data->stok->nama_barang }}', {{ $data->price }})">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                            <!-- Form for deletion -->
                                            <form id="deleteForm{{ $data->id_barang }}" action="{{ route('stok.destroy', $data->id_barang) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger" style="width: 25%" onclick="confirmDelete({{ $data->id_barang }})">
                                                    <i class="nav-icon fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr> --}}
                                <tr>
                                    <td class="text-center">{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->stok->nama_barang }}</td>
                                    <td>{{ session('outlet_id') == '' ? $data->total_minimum : $data->stok->minimum}}</td>
                                    <td>{{ session('outlet_id') == '' ? $data->total_jumlah : $data->jumlah}}</td>
                                    <td class="text-center">
                                        {{-- @if (session('outlet_id') == '')
                                            @if ($data->total_jumlah == 0)
                                                <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                            @elseif ($data->total_jumlah > 0 && $data->jumlah <= $data->stok->minimum)
                                                <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                            @else
                                                <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                            @endif
                                        @else
                                            @if ($data->jumlah == 0)
                                                <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                            @elseif ($data->jumlah > 0 && $data->jumlah <= $data->stok->minimum)
                                                <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                            @else
                                                <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                            @endif
                                        @endif --}}
                                        @if($data->status == 'Grave')
                                            <i class="fas fa-dungeon fa-2x" style="color: darkgrey"></i>
                                        @elseif($data->status == 'Death')
                                            <i class="fas fa-skull-crossbones fa-2x" style="color: black"></i>
                                        @elseif($data->status == 'Habis')
                                            <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                        @elseif($data->status == 'Sekarat')
                                            <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                        @else
                                            <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                        @endif
                                        {{-- @if (session('outlet_id') == '')

                                            @php
                                                $info_all = 'green'; // Default status
                                            @endphp

                                            @foreach ($outlets as $outlet)
                                                @php
                                                    $info = 'green'; // Default to green
                                                    $data1 = $stok->firstWhere('outlet.id_outlet', $outlet->id_outlet); // Get the data for each outlet
                                                    if ($data1) {
                                                        if ($data1->jumlah == 0) {
                                                            $info = 'red';
                                                        } elseif ($data1->jumlah > 0 && $data1->jumlah <= $data1->stok->minimum) {
                                                            $info = 'yellow';
                                                        } else {
                                                            $info = 'green';
                                                        }

                                                        // If any outlet has a red or yellow status, set info_all to red
                                                        if ($info == 'red' || $info == 'yellow') {
                                                            $info_all = 'red';
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                            @endforeach

                                            @if ($info_all == 'red')
                                                <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                            @elseif ($info_all == 'yellow')
                                                <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                            @else
                                                <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                            @endif

                                        @else
                                            @php
                                                $data = $stok->firstWhere('outlet.id_outlet', session('outlet_id'));
                                            @endphp
                                            @if ($data && $data->jumlah == 0)
                                                <i class="fas fa-times-circle fa-2x" style="color: red"></i>
                                            @elseif ($data && $data->jumlah > 0 && $data->jumlah <= $data->stok->minimum)
                                                <i class="fas fa-exclamation-circle fa-2x" style="color: orange"></i>
                                            @else
                                                <i class="fas fa-check-circle fa-2x" style="color: green"></i>
                                            @endif
                                        @endif --}}
                                    </td>
                                    @if (session('outlet_id') == '')
                                        <td class="text-center">
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openPembelianModal({{ session('outlet_id') }}, {{ $data->id_barang }}, '{{ $data->stok->nama_barang }}', {{ $data->price }})">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                            <!-- Form for deletion -->
                                            <form id="deleteForm{{ $data->id_barang }}" action="{{ route('stok.destroy', $data->id_barang) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger" style="width: 25%" onclick="confirmDelete({{ $data->id_barang }})">
                                                    <i class="nav-icon fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
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

<!-- Modal for Pembelian Form -->
<div class="modal fade" id="pembelianModal" tabindex="-1" aria-labelledby="pembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <form id="pembelianForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">Pembelian</label>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="modalItemName">Nama Barang</label>
                        <input type="text" id="modalItemName" class="form-control" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="modalQuantity">Jumlah</label>
                        <input type="number" id="modalQuantity" name="quantity" class="form-control" value="1" min="1" required onchange="calculateTotal()">
                    </div>
                    <div class="form-group mb-3">
                        <label for="modalPrice">Harga (per barang)</label>
                        <input type="number" id="modalPrice" name="visiblePrice" class="form-control" value="100.00" step="0.01" onchange="calculateTotal()">
                    </div>
                    <div class="form-group mb-3">
                        <label for="modalTotalHarga">Total</label>
                        <input type="number" id="modalTotalHarga" name="total_harga" class="form-control" readonly>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <input type="hidden" id="outletIdInput" name="id_outlet">
            </form>
        </div>
    </div>
</div>

<!-- Confirmation modal -->
<div id="deleteConfirmCard" 
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
        background-color: rgba(0, 0, 0, 0.5); z-index: 1000; display: none; 
        justify-content: center; align-items: center; pointer-events: all;">
    <div class="card" style="width: 300px; z-index: 1010; pointer-events: all;">
        <div class="card-body">
            <h5 class="card-title text-center">Confirm Deletion</h5>
            <p class="card-text text-center">Are you sure you want to delete this Stok?</p>

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
                <button id="cancelBtn" class="btn btn-secondary ml-2">Cancel</button>
                <button id="confirmBtn" class="btn btn-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openPembelianModal(id_outlet, id_barang, nama_barang, price) {
        // Set the form action URL for this specific item
        document.getElementById('pembelianForm').action = `/stok/${id_barang}/pembelian`;
        
        // Set the outlet ID and item details in the form
        document.getElementById('outletIdInput').value = id_outlet;
        document.getElementById('modalItemName').value = nama_barang;
        document.getElementById('modalPrice').value = price;

        // Reset quantity and total price for a new purchase
        document.getElementById('modalQuantity').value = 1;
        calculateTotal();

        // Attach event listeners for real-time total price calculation
        document.getElementById('modalQuantity').addEventListener('input', calculateTotal);
        document.getElementById('modalPrice').addEventListener('input', calculateTotal);

        // Show the modal
        new bootstrap.Modal(document.getElementById('pembelianModal')).show();
    }

    function calculateTotal() {
        let quantity = document.getElementById('modalQuantity').value;
        let price = document.getElementById('modalPrice').value;
        let total = quantity * price;
        document.getElementById('modalTotalHarga').value = total.toFixed(2);
    }

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
</script>
@endsection
