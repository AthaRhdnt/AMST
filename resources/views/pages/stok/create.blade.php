@extends('layouts.app')

@section('title', 'Tambah Stok')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-outline shadow-sm">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body">
                    <form action="{{ route('stok.store') }}" method="POST">
                        @csrf
                        <!-- Stock Name -->
                        <div class="form-group mb-3">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required />
                            @error('nama_barang')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Minimum -->
                        <div class="form-group mb-3">
                            <label for="minimum">Stok Minimum</label>
                            <input type="number" name="minimum" id="minimum" class="form-control" value="{{ old('minimum') }}" required min="1" />
                            @error('minimum')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Quantity -->
                        <div class="form-group mb-3">
                            <label for="jumlah_barang">Stok Awal</label>
                            <input type="number" name="jumlah_barang" id="jumlah_barang" class="form-control" value="{{ old('jumlah_barang') }}" required min="1" />
                            @error('jumlah_barang')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Submit and Back Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stok.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
