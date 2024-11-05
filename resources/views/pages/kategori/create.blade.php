@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-outline shadow-sm">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf

                        <!-- Submit and Back Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Tambah Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection