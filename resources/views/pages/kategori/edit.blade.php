@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-outline shadow-sm">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body">
                    <form action="{{ route('kategori.update', $kategori->id_kategori) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required />
                        </div>
                        <!-- Submit and Back Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Update Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
