@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Home</h1>
    {{-- <h2>Welcome, {{Auth::user()->name}}. You are {{Auth::user()->role->name}}</h2> --}}

    {{-- <x-alert message='This is home page' type='success' /> --}}
    @include('layouts.partials.script')
@endsection
