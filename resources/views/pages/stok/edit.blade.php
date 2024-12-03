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
                <div class="card-body scrollable-card">
                    <form action="{{ route('stok.update', $stok->id_barang) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col">
                            <!-- Stock Name -->
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="nama_barang">Nama Barang</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang', $stok->nama_barang) }}" required />
                                    </div>
                                </div>
                                @error('nama_barang')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Minimum -->
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="minimum">Stok Minimum</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="number" name="minimum" id="minimum" class="form-control" value="{{ old('minimum', $stok->minimum) }}" required min="1" />
                                    </div>
                                </div>
                                @error('minimum')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Quantity -->
                            <div class="form-group mb-3">
                                <label>Stok Akhir</label>
                                @foreach($outlets as $outlet)
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="jumlah_barang_{{ $outlet->id_outlet }}">{{ $outlet->user->nama_user }}</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input 
                                                type="number" 
                                                name="jumlah_barang[{{ $outlet->id_outlet }}]" 
                                                id="jumlah_barang_{{ $outlet->id_outlet }}" 
                                                class="form-control" 
                                                value="{{ old('jumlah_barang.' . $outlet->id_outlet, optional($stok->stokOutlet->firstWhere('id_outlet', $outlet->id_outlet))->jumlah) }}" 
                                                required 
                                                min="1" 
                                                @if(session('outlet_id') && session('outlet_id') != $outlet->id_outlet) readonly @endif
                                            />
                                            </div>
                                        </div>
                                        @error("jumlah_barang.{$outlet->id_outlet}")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stok.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
