<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        footer {
            text-align: center;
            background-color: #f4f4f4;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
        }
        footer {
            display: block;
        }
        .main-footer {
            bottom: 0;
            left: 0;
            position: fixed;
            right: 0;
            z-index: 1032;
        }
        main {
            margin: 80px 20px 60px;
        }

        /* Title Section */
        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title h1 {
            font-size: 20px;
            margin: 0;
            color: #444;
        }
        .report-title p {
            font-size: 12px;
            color: #666;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tfoot td {
            font-weight: bold;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f4f4f4;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }

        /* Summary Section */
        .summary {
            margin-top: 20px;
        }
        .summary p {
            margin: 5px 0;
        }
        .summary strong {
            font-size: 13px;
        }

        /* Page Numbers */
        .page-number:after {
            content: "Page " counter(page);
        }

        @page {
            margin: 20mm;
            counter-increment: page;
        }
    </style>
</head>
<body>
    @yield('content')

    <footer class="main-footer">
        <p>&copy; {{ \Carbon\Carbon::now()->year }} STM Esteh Manis. All Rights Reserved.</p>
    </footer>
</body>
</html>
