@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex justify-content-start">
                            <form action="{{ route('menu.index') }}" method="GET">
                                <input type="search" id="search" name="search"
                                    class="form-control form-control-solid w-250px ps-13"
                                    placeholder="Search" value="{{ request('search') }}" />
                            </form>
                        </div>
                        <div class="d-flex justify-content-end place-item-auto">
                            <a href="" class="btn my-btn">
                                <i class="fas fa-plus mr-2"></i> Tambah Menu
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th width="5%">No</th>
                            <th>Nama Menu</th>
                            <th>Bahan</th>
                            <th>Harga</th>
                            <th width="15%">Aksi</th>
                        </thead>
                        <tbody>
                            @foreach ($menu as $data)
                                <tr>
                                    <td>{{ ($menu->currentPage() - 1) * $menu->perPage() + $loop->iteration }}</td>
                                    <td>{{ $data->nama_menu }}</td>
                                    <td>
                                        @foreach ($data->stok as $stok)
                                            {{ $stok->nama_barang }}@if (!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $data->harga_menu }}</td>
                                    <td>
                                        <a href="" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="" class="btn btn-sm btn-danger">Delete</a>
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
@endsection
