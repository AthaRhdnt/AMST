@extends('layouts.app')

@section('title', 'Login')

@section('login')
<div class="login-container">
    <div class="row justify-content-center align-items-center w-100 h-100">
        <!-- Left Side - Outlet Image -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <img src="{{ asset('image/outlet.png') }}" alt="Outlet STM" class="outlet-image">
        </div>
        <!-- Right Side - Login Card -->
        <div class="col-lg-6 col-md-8 col-sm-10  d-flex justify-content-center align-items-center">
            <div class="w-50">
                <div class="text-center">
                    <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="login-logo" />
                </div>
                <div class="login-form">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Username Field -->
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="login-input" placeholder="Username" value="{{ old('username') }}" required autofocus>
                        </div>
                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="login-input" placeholder="Password" required>
                        </div>
                        <button type="submit" class="login-btn my-btn btn-block">LOGIN</button>
                    </form>
                    <!-- Error Messages -->
                    <div class="mt-3">
                        @if ($errors->any())
                            <div class="text-center text-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <div class="mt-3 text-center">
                            <a href="{{ route('password.reset') }}" class="nav-link">Forget Password</a>
                        </div>
                    <p class="text-center hashtag">#estehkuterbaikuntukmu</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- 
<script>
    function confirmPassword??(outletId) {
        if (confirm('Are you sure you want to delete this outlet?')) {
            const adminPassword = prompt("Please enter your admin password to confirm deletion:");
            if (adminPassword) {
                // Create a hidden input to hold the admin password
                const form = document.querySelector('form[action*="' + outletId + '"]');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'admin_password';
                input.value = adminPassword;
                form.appendChild(input);

                // Submit the form
                form.submit();
            }
        }
    }
</script> --}}
@endsection


