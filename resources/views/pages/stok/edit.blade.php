@extends('layouts.app')

@section('title', 'Edit Stok')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-outline shadow-sm">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body">
                    <form action="{{ route('stok.update', $stok->id_barang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Stock Name -->
                        <div class="form-group mb-3">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang', $stok->nama_barang) }}" required />
                            @error('nama_barang')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="form-group mb-3">
                            <label>Jumlah Barang per Outlet</label>
                            @foreach($outlets as $outlet)
                                <div class="mb-2">
                                    <label for="jumlah_barang_{{ $outlet->id_outlet }}">{{ $outlet->user->nama_user }} (Outlet)</label>
                                    <input 
                                        type="number" 
                                        name="jumlah_barang[{{ $outlet->id_outlet }}]" 
                                        id="jumlah_barang_{{ $outlet->id_outlet }}" 
                                        class="form-control" 
                                        value="{{ old('jumlah_barang.' . $outlet->id_outlet, optional($stok->stokOutlet->firstWhere('id_outlet', $outlet->id_outlet))->jumlah) }}" 
                                        required 
                                        min="1" 
                                    />
                                    @error("jumlah_barang.{$outlet->id_outlet}")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit and Back Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stok.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Update Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
