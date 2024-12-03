@extends('layouts.app')

@section('title', 'Outlet')

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
                                <form action="{{ route('outlets.index') }}" method="GET">
                                    <input type="search" id="search" name="search"
                                        class="form-control form-control-solid w-250px ps-13"
                                        placeholder="Search" value="{{ session('outlet_search', '') }}" />
                                </form>
                            </div>
                            <div class="ml-2">
                                <form method="GET" action="{{ route('outlets.index') }}" id="entries-form" class="d-flex align-items-center">
                                    <label for="entries" class="mr-2 mb-0 fw-normal">Menampilkan</label>
                                    <select name="entries" id="entries" class="form-control" style="width: auto;" onchange="document.getElementById('entries-form').submit();">
                                        <option value="5" {{ session('outlet_entries') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ session('outlet_entries') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ session('outlet_entries') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ session('outlet_entries') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ session('outlet_entries') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="ml-2 mb-0">data</span>

                                    <input type="hidden" name="search" value="{{ session('outlet_entries', '') }}">
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end place-item-auto">
                            <div class="mr-2">
                                <form action="{{ route('outlets.index') }}" method="GET" class="d-flex align-items-center">
                                    <label for="status" class="mr-2 mb-0 fw-normal">Status</label>
                                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                        <option value="active" {{ session('outlet_status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ session('outlet_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </form>                                
                            </div>
                            <div class="ml-2">
                                <a href="{{ route('outlets.create') }}" class="btn my-btn">
                                    <i class="fas fa-plus-circle"></i> Tambah Outlet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="text-center">
                            <th width="5%">No</th>
                            <th>Nama Outlet</th>
                            {{-- <th>Password</th> --}}
                            <th>Alamat</th>
                            <th width="20%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($outlets as $data)
                                <tr>
                                    <td class="text-center">{{ ($outlets->currentPage() - 1) * $outlets->perPage() + $loop->iteration }}</td>
                                    <td>Outlet {{ $data->user->nama_user }}</td>
                                    <td>{{ $data->alamat_outlet }}</td>
                                    <td class="text-center">
                                        <!-- Form for reset -->
                                        <form id="resetForm{{ $data->id_outlet }}" action="{{ route('outlets.reset', $data->id_outlet) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Reset Password" onclick="openModal('reset', {{ $data->id_outlet }}, '{{ $data->user->nama_user }}')">
                                                <i class="nav-icon fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('outlets.edit', $data->id_outlet) }}" title="Edit" class="btn btn-sm btn-outline-warning">
                                            <i class="nav-icon fas fa-edit"></i>
                                        </a>
                                        <!-- Form for deletion -->
                                        <form id="deleteForm{{ $data->id_outlet }}" action="{{ route('outlets.destroy', $data->id_outlet) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="openModal('delete', {{ $data->id_outlet }}, '{{ $data->user->nama_user }}')">
                                                <i class="nav-icon fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $outlets->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <div class="card-header my-bg text-white">
                <label class="my-0 fw-bold">Konfirmasi</label>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <h5 class="card-title text-center" id="modalTitle"></h5>
                <p class="card-text text-center" id="modalMessage"></p>
    
                <!-- Error message -->
                @if ($errors->has('admin_password'))
                    <div class="text-center text-danger mb-3">
                        {{ $errors->first('admin_password') }}
                    </div>
                @endif
    
                <input id="adminPassword" type="password" class="form-control mb-3" placeholder="Enter admin password" required>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="confirmBtn" type="button" class="btn btn-danger">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAction = null;
    let currentFormId = null;

    function openModal(action, id, nama_user = '') {
        currentAction = action;
        currentFormId = `${action}Form${id}`;

        // Update modal content
        const modalTitle = action === 'delete' ? 'Konfirmasi Penghapusan' : 'Konfirmasi Reset Password';
        const modalMessage = `Apakah anda yakin ingin ${action === 'delete' ? 'menghapus' : 'mereset password'} Outlet ${nama_user ? nama_user : 'Outlet ini'}?`;

        document.getElementById('modalTitle').textContent = modalTitle;
        document.getElementById('modalMessage').textContent = modalMessage;
        document.getElementById('confirmBtn').textContent = action === 'delete' ? 'Hapus' : 'Reset';

        // Reset password input
        const passwordField = document.getElementById('adminPassword');
        passwordField.value = '';
        passwordField.focus();

        // Show modal using Bootstrap
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    document.getElementById('confirmBtn').addEventListener('click', () => {
        const adminPassword = document.getElementById('adminPassword').value;

        if (!adminPassword) {
            alert('Please enter the admin password.');
            return;
        }

        const form = document.getElementById(currentFormId);
        if (!form) {
            console.error('Form not found:', currentFormId);
            return;
        }

        const passwordInput = document.createElement('input');
        passwordInput.type = 'hidden';
        passwordInput.name = 'admin_password';
        passwordInput.value = adminPassword;
        form.appendChild(passwordInput);

        form.submit();
    });

    // Reopen modal if validation error occurs
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->has('admin_password'))
            const action = "{{ session('action') }}";
            const id = "{{ session('id') }}";
            openModal(action, id);
        @endif
    });
</script>
@endsection
