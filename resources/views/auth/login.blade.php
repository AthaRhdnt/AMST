<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Page</title>
    </head>

    <style>
        /* Styling Umum untuk Halaman */
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        /* Kontainer Utama */
        .login-container {
            display: flex;
            height: 100vh;
        }

        /* Bagian Kiri */
        .login-left {
            background-color: #e0e0e0;
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .outlet-image {
            width: 130%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
        }

        /* Bagian Kanan */
        .login-right {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .logo {
            width: 200px;
            margin-bottom: 30px;
        }

        .hashtag {
            font-size: 16px;
            margin-top: 20px;
            color: #888888;
        }

        .login-logo {
            width: 150px;
            margin-bottom: 50px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .login-input {
            width: 200px;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: none;
            background-color: #d8d8d8;
            font-size: 16px;
            color: #555;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .login-button {
            width: 100%;
            margin-bottom: 10px;
            padding: 12px;
            background-color: #8266a9;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .login-button:disabled {
            background-color: #cccccc;
        }

        .login-button:hover {
            background-color: #777777;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>

    <body>
        <div class="login-container">
            <!-- Bagian Kiri -->
            <div class="login-left">
                <img src="{{ asset('image/outlet.png') }}" alt="Outlet STM" class="outlet-image">
            </div>

            <!-- Bagian Kanan -->
            <div class="login-right">
                <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="login-logo" />

                <!-- Laravel form for login -->
                <div class="login-form">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf <!-- Laravel CSRF protection -->
                        <input type="text" name="username" class="login-input" placeholder="Username"
                            value="{{ old('username') }}" required />
                        <input type="password" name="password" class="login-input" placeholder="Password" required />
                        <button type="submit" class="login-button">LOGIN</button>
                    </form>
                    @if (session('error'))
                        <div class="error-message">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <p class="hashtag">#estehkuterbaikuntukmu</p>
            </div>
        </div>
    </body>

</html>
