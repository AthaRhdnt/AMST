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
                                    <input type="hidden" name="start_date" value="{{ session('start_date') }}">
                                    <input type="hidden" name="end_date" value="{{ session('end_date', now()->toDateString()) }}">
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
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th width="5%">No</th>
                            <th>Nama Outlet</th>
                            <th>Nama Item</th>
                            <th>Stok Awal</th>
                            <th>Pembelian</th>
                            <th>Stok Terpakai</th>
                            <th>Stok Akhir</th>
                            <th width="15%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($stok as $data)
                                <tr>
                                    <td>{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                    <td>Outlet {{ $data->outlet->user->nama_user }}</td>
                                    <td>{{ $data->stok->nama_barang }}</td>
                                    <td>{{ $data->stok_awal }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-normal">{{ $data->jumlah_pembelian }}</span>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openPembelianModal({{ $data->id_barang }}, '{{ $data->nama_barang }}', {{ $data->price }})">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $data->used_today }}</td> <!-- Stock used today (Stok Terpakai) -->
                                    <td>{{ $data->stok_akhir }}</td> <!-- Remaining stock (Stok Akhir) -->
                                    <td>
                                        <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning" style="width: 25%">
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
        <div class="modal-content">
            <form id="pembelianForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="pembelianModalLabel">Add Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalItemName">Item Name</label>
                        <input type="text" id="modalItemName" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modalQuantity">Quantity</label>
                        <input type="number" id="modalQuantity" name="quantity" class="form-control" value="1" min="1" required onchange="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="modalPrice">Price (per item)</label>
                        <input type="number" id="modalPrice" name="visiblePrice" class="form-control" value="100.00" step="0.01" onchange="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="modalTotalHarga">Total Price</label>
                        <input type="number" id="modalTotalHarga" name="total_harga" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <!-- Hidden input for outlet_id, to be set dynamically via JS -->
                <input type="hidden" id="outletIdInput" name="outlet_id" value="">
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
                <button id="confirmBtn" class="btn btn-danger">Confirm</button>
                <button id="cancelBtn" class="btn btn-secondary ml-2">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openPembelianModal(id_barang, nama_barang, price) {
    // Ensure outletId is passed correctly from the Blade view
    const outletId = @json($outletId);  // Blade directive to safely pass PHP data to JS

    console.log("Outlet ID:", outletId);  // Log to ensure the value is correct

    // Check if outletId is empty (this should not be the case if the session is set correctly)
    if (!outletId) {
        console.error("Outlet ID is not set in session!");
        alert("Outlet ID is missing. Please ensure session is set.");
        return; // Prevent further execution if the outlet ID is missing
    }

    // Set the form action URL for the specific item
    document.getElementById('pembelianForm').action = `/stok/${id_barang}/pembelian`;

    // Set the outlet ID in the hidden input field
    document.getElementById('outletIdInput').value = outletId;

    // Set the item name and price in the modal
    document.getElementById('modalItemName').value = nama_barang;  // Dynamically set item name
    document.getElementById('modalPrice').value = price;        // Dynamically set price

    // Reset the quantity and total price when the modal is opened
    document.getElementById('modalQuantity').value = 1;
    calculateTotal();

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
