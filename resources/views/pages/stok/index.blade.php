@extends('layouts.app')

@section('title', 'Stok')

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
                                <form action="{{ route('stok.index') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control form-control-solid w-250px ps-13"
                                        placeholder="Search" value="{{ session('stok_search', '') }}" />
                                </form>
                            </div>
                            <div class="ml-2">
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
                            <th>Nama Barang</th>
                            <th>Jumlah Barang</th>
                            <th>Terpakai Hari Ini</th>
                            <th>Sisa</th>
                            <th width="15%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($stok as $data)
                                <tr>
                                    <td>{{ ($stok->currentPage() - 1) * $stok->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->nama_barang }}</td>
                                    <td>{{ $data->jumlah_barang }}</td>
                                    <td>{{ $data->used_today }}</td>
                                    <td>{{ $data->remaining_stock }}</td>
                                    <td>
                                        <a href="{{ route('stok.edit', $data->id_barang) }}" class="btn btn-sm btn-outline-warning" style="width: 25%">
                                            <i class="nav-icon fas fa-edit"></i>
                                        </a>
                                        <!-- Form for deletion -->
                                        <form action="{{ route('stok.destroy', $data->id_barang) }}" method="POST" style="display:inline;">
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

<script>
    function confirmDelete(barangId) {
        if (confirm('Are you sure you want to delete this stok?')) {
            const adminPassword = prompt("Please enter your admin password to confirm deletion:");
            if (adminPassword) {
                // Create a hidden input to hold the admin password
                const form = document.querySelector('form[action*="' + barangId + '"]');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'admin_password';
                input.value = adminPassword;
                form.appendChild(input);

                // Submit the form
                form.submit();
            }
        }
    }
</script>
@endsection
