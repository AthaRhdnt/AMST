@extends('layouts.app')

@section('title', 'Reset Password')

@section('reset')
<div class="login-container">
    <div class="row justify-content-center align-items-center w-100 h-100">
        <!-- Left Side - Outlet Image (Optional) -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <img src="{{ asset('image/outlet.png') }}" alt="Outlet STM" class="outlet-image">
        </div>
        <!-- Right Side - Reset Password Card -->
        <div class="col-lg-6 col-md-8 col-sm-10 d-flex justify-content-center align-items-center">
            <div class="w-50">
                <div class="text-center">
                    <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="login-logo" />
                </div>
                <div class="card">
                    <div class="card-header my-bg text-white">
                        <label class="my-0 fw-bold">@yield('title')</label>
                    </div>
                    <div class="card-body">
                        <div class="reset-password-form">
                            <form method="POST" action="{{ route('password.reset.submit') }}">
                                @csrf
                                <input type="hidden" name="username" value="{{ $username }}">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username_display" class="form-control" value="{{ $username }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="new_password_reset">New Password</label>
                                    <input type="password" id="new_password_reset" name="new_password_reset" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password_reset_confirmation">Confirm New Password</label>
                                    <input type="password" id="new_password_reset_confirmation" name="new_password_reset_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn my-btn btn-block">Reset Password</button>
                            </form>
                            <div class="mt-3">
                                @if ($errors->any())
                                    <div class="text-danger">
                                        {{ $errors->first() }}
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3 text-center">
                                <a href="{{ route('login') }}" class="nav-link">Back to Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

