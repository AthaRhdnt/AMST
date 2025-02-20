<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <style>
        p {
            display: block;
            margin: 3px;
        }
        .separator-struk {
            border: 0;
            border-top: 1px solid black;
            margin: 0 1rem;
        }
        .logo {
            display: block;
            margin: 0 auto;
            width: auto;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .pad {
            padding-top: 20px
        }
        @media print {
            @page {
                margin: 0;
                size: 58mm auto;  /* Lebar tetap 58mm, tinggi otomatis */
            }
            html, body {
                width: 58mm;
                overflow: hidden;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    @yield('receipt')
</body>
</html>