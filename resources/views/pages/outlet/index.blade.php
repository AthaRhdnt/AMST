@extends('layouts.app')

@section('title', 'Outlet')

{{-- @section('breadcrumb')
    @parent
    <li class="breadcrumb-item">Outlet</li>
@endsection --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex justify-content-start">
                            <form action="{{ route('outlet.index') }}" method="GET">
                                <input type="search" id="search" name="search"
                                    class="form-control form-control-solid w-250px ps-13"
                                    placeholder="Search"  value="{{ request('search') }}" />
                            </form>
                        </div>
                        <div class="d-flex justify-content-end place-item-auto">
                            <a href="" class="btn my-btn">
                                <i class="fas fa-plus-circle"></i> Tambah Outlet
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body scrollable-card">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th width="5%">No</th>
                            <th>Nama Outlet</th>
                            {{-- <th>Password</th> --}}
                            <th>Alamat</th>
                            {{-- @if (auth()->user()->role == 'Pemilik Toko') --}}
                                <th width="15%">Aksi</th>
                            {{-- @endif --}}
                        </thead>
                        <tbody>
                            @foreach ($outlets as $data)
                                <tr>
                                    <td>{{ ($outlets->currentPage() - 1) * $outlets->perPage() + $loop->iteration }}</td>
                                    <td>Outlet {{ $data->user->nama_user }}</td>
                                    {{-- @php
                                        $password = $data->user->password; // Assume this is the unhashed password
                                        $maskedPassword = strlen($password) > 0 ? $password[0] . str_repeat('*', max(0, strlen($password) - 1)) : '';
                                    @endphp
                                    <td>{{ $maskedPassword }}</td> --}}
                                    <td>{{ $data->alamat_outlet }}</td>
                                    <td>
                                        <a href="" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="" class="btn btn-sm btn-danger">Delete</a>
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
@endsection
