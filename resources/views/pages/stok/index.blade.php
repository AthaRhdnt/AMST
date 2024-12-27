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
                            @if (auth()->user()->role->nama_role == 'Pemilik')
                                <div class="mr-2">
                                    <form action="{{ route('stok.index') }}" method="GET" class="d-flex align-items-center">
                                        <label for="status" class="mr-2 mb-0 fw-normal">Status</label>
                                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                            <option value="active" {{ session('stok_status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ session('stok_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </form>                                
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('stok.create') }}" class="btn my-btn">
                                        <i class="fas fa-plus mr-2"></i> Tambah Stok
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
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
                                <tr>
                                    <td class="text-center">{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->stok->nama_barang }}</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->total_minimum : $data->stok->minimum}}</td>
                                    <td class="text-center">{{ session('outlet_id') == '' ? $data->total_jumlah : $data->jumlah}}</td>
                                    <td class="text-center">
                                        @if($data->status == 'Habis')
                                            <i class="fas fa-times-circle fa-lg text-red"></i>
                                        @elseif($data->status == 'Sekarat')
                                            <i class="fas fa-exclamation-circle fa-lg text-yellow"></i>
                                        @else
                                            <i class="fas fa-check-circle fa-lg text-green"></i>
                                        @endif
                                    </td>
                                    @if (session('outlet_id') == '')
                                        <td class="text-center">
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                            @if ($data->stok->status == 'active')
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="openDeleteModal({{ $data->stok->id_barang }}, '{{ $data->stok->nama_barang }}')">
                                                    <i class="nav-icon fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary" title="Beli" onclick="openPembelianModal({{ session('outlet_id') }}, {{ $data->id_barang }}, '{{ $data->stok->nama_barang }}', {{ $data->price }})">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="nav-icon fas fa-edit"></i>
                                            </a>
                                            @if (auth()->user()->role->nama_role == 'Pemilik' && $data->stok->status == 'active')
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="openDeleteModal({{ $data->stok->id_barang }}, '{{ $data->stok->nama_barang }}')">
                                                    <i class="nav-icon fas fa-trash"></i>
                                                </button>
                                            @endif
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

<!-- Pembelian Modal-->
<div class="modal fade" id="pembelianModal" tabindex="-1" aria-labelledby="pembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <form id="pembelianForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">Pembelian</label>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <input type="text" id="modalTotalHarga" name="total_harga" class="form-control" readonly>
                        <input type="hidden" id="totalHarga" name="total_harga" class="form-control" readonly>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Beli</button>
                    </div>
                </div>
                <input type="hidden" id="outletIdInput" name="id_outlet">
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <form id="deleteForm" action="#" method="POST">
                @csrf
                @method('DELETE')
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">Konfirmasi</label>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-center">Konfirmasi Penghapusan</h5>
                    <p id="itemName" class="card-text text-center mb-3"></p>
        
                    <!-- Error message for invalid password -->
                    @if ($errors->has('admin_password'))
                        <div class="text-center text-danger mb-3" id="adminPasswordError">
                            {{ $errors->first('admin_password') }}
                        </div>
                    @endif
        
                    <input id="adminPassword" type="password" class="form-control mb-3" placeholder="Enter admin password" required>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
                <input type="hidden" id="outletIdInput" name="id_outlet">
            </form>
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

        let formattedTotal = 'Rp. ' + new Intl.NumberFormat('en-US').format(total);

        document.getElementById('modalTotalHarga').value = formattedTotal;
        document.getElementById('totalHarga').value = total.toFixed(2);
    }

    function openDeleteModal(id_barang, nama_barang) {
        document.getElementById('deleteForm').action = `/stok/${id_barang}`;
        
        document.getElementById('itemName').innerText = 'Apakah anda yakin ingin menghapus '+ nama_barang +'?';

        // Show the modal
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Handle form submission and include admin password as a hidden field
    document.getElementById('deleteForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default submission

        const adminPassword = document.getElementById('adminPassword').value;

        if (adminPassword) {
            // Create a hidden input to pass the password
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'admin_password';
            passwordInput.value = adminPassword;
            this.appendChild(passwordInput);

            // Submit the form
            this.submit();
        } else {
            alert('Please enter the admin password.');
        }
    });

    // Automatically show the modal again if there are validation errors
    document.addEventListener('DOMContentLoaded', function () {
        const idBarang = "{{ session('id_barang') }}"; // Get the id_menu value from the session
        const namaBarang = "{{ session('nama_barang') }}";

        // If the modal was triggered by a validation error, show it again
        if (document.getElementById('adminPasswordError')) {
            openDeleteModal(idBarang, namaBarang);
        }
    });
</script>
@endsection
