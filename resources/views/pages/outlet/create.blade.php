@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Outlet</h1>
    <form action="{{ route('outlets.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="id_user">Nama Outlet</label>
            <input type="text" class="form-control" id="id_user" name="id_user" required>
        </div>
        <div class="form-group">
            <label for="alamat_outlet">Alamat Outlet</label>
            <input type="text" class="form-control" id="alamat_outlet" name="alamat_outlet" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('outlet.index') }}" class="btn btn-secondary" style="margin-top: 10px;">Back</a>
        </form>
</div>
@endsection
