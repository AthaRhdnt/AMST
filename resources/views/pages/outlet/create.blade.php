@extends('layouts.app')

@section('title', 'Tambah Outlet')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-outline shadow-sm">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body">
                    <form action="{{ route('outlets.store') }}" method="POST">
                        @csrf
                        <!-- User Name -->
                        <div class="form-group mb-3">
                            <label for="nama_user" class="form-label">Nama Outlet</label>
                            <input 
                                type="text" 
                                class="form-control @error('nama_user') is-invalid @enderror" 
                                id="nama_user" 
                                name="nama_user" 
                                placeholder="Masukkan nama outlet" 
                                value="{{ old('nama_user') }}" 
                                required
                            >
                            @error('nama_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input 
                                type="text" 
                                class="form-control @error('username') is-invalid @enderror" 
                                id="username" 
                                name="username" 
                                placeholder="Masukkan username" 
                                value="{{ old('username') }}" 
                                required
                            >
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="Masukkan password" 
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Outlet Address -->
                        <div class="form-group mb-3">
                            <label for="alamat_outlet" class="form-label">Alamat Outlet</label>
                            <input 
                                type="text" 
                                class="form-control @error('alamat_outlet') is-invalid @enderror" 
                                id="alamat_outlet" 
                                name="alamat_outlet" 
                                placeholder="Masukkan alamat outlet" 
                                value="{{ old('alamat_outlet') }}" 
                                required
                            >
                            @error('alamat_outlet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Admin Password Confirmation -->
                        <div class="form-group mb-4">
                            <label for="admin_password" class="form-label">Konfirmasi Password Admin</label>
                            <input 
                                type="password" 
                                class="form-control @error('admin_password') is-invalid @enderror" 
                                id="admin_password" 
                                name="admin_password" 
                                placeholder="Masukkan password admin untuk konfirmasi" 
                                required
                            >
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit and Back Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('outlets.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Tambah Outlet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
