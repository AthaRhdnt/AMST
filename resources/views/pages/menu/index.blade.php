@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card x-ovfl-hid">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex justify-content-start">
                            <div class="mr-2">
                                <form action="{{ route('menu.index') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control form-control-solid w-250px ps-13"
                                        placeholder="Search" value="{{ session('menu_search', '') }}" />
                                </form>
                            </div>
                            <div class="ml-2">
                                <form method="GET" action="{{ route('menu.index') }}" id="entries-form" class="d-flex align-items-center">
                                    <label for="entries" class="mr-2 mb-0 fw-normal">Menampilkan</label>
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('menu_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('menu_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('menu_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('menu_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('menu_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="ml-2 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('menu_search', '') }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end place-item-auto">
                            <a href="{{ route('menu.create') }}" class="btn my-btn">
                                <i class="fas fa-plus mr-2"></i> Tambah Menu
                            </a>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th width="5%">No</th>
                            <th width="15%">Nama Menu</th>
                            <th width="15%">Kategori</th>
                            <th>Bahan</th>
                            <th width="5%">Harga</th>
                            <th width="15%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($menu as $data)
                                <tr>
                                    <td>{{ ($menu->currentPage() - 1) * $menu->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->nama_menu }}</td>
                                    <td>{{ $data->kategori->nama_kategori }}</td>
                                    <td>
                                        @foreach ($data->stok as $stok)
                                            {{ $stok->nama_barang }} ({{ $stok->pivot->jumlah }})@if (!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $data->harga_menu }}</td>
                                    <td>
                                        <a href="{{ route('menu.edit', $data->id_menu) }}" class="btn btn-sm btn-outline-warning" style="width: 25%">
                                            <i class="nav-icon fas fa-edit"></i>
                                        </a>
                                        <!-- Form for deletion -->
                                        <form id="deleteForm{{ $data->id_menu }}" action="{{ route('menu.destroy', $data->id_menu) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                        
                                            <button type="button" class="btn btn-sm btn-outline-danger" style="width: 25%" onclick="confirmDelete({{ $data->id_menu }})">
                                                <i class="nav-icon fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $menu->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
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
            <p class="card-text text-center">Are you sure you want to delete this Menu?</p>

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
