@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="flexColumn">
        <button class="btnLoginPemilik">
            <!-- TODO -->
            Login as Pemilik
        </button>
        <button class="btnLoginOutlet">
            <!-- TODO -->
            Login as Outlet
        </button>
    </div>
    @include('layouts.partials.script')
@endsection
