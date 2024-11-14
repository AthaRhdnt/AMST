<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        /* General Layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /* Title */
        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h2 {
            font-size: 24px;
            color: #2c3e50;
        }

        /* Logo */
        .logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 150px; /* Adjust logo size */
        }

        /* Date Range */
        .date-range {
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        /* Table */
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transaction-table th, .transaction-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        .transaction-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .right-align {
            text-align: right;
        }

        /* Total Row */
        .total {
            font-weight: bold;
            margin-top: 20px;
        }

        .total p {
            font-size: 16px;
        }

        /* Page Break (optional) */
        .page-break {
            page-break-before: always;
        }

        /* Footer */
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

    <!-- Title and Logo Section -->
    <div class="title">
        <img src="{{ public_path('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <h2>Transaction Receipt</h2>
    </div>

    <!-- Date Range Section -->
    <p class="date-range">Tanggal: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>

    <!-- Transaction Table -->
    <table class="transaction-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Outlet</th>
                <th>Total Pembelian</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($query as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td>{{ $item->outlet->user->nama_user }}</td>
                    <td class="right-align">{{ number_format($item->total_pembelian, 2) }}</td>
                    <td class="right-align">{{ number_format($item->total_penjualan, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Row -->
    <div class="total">
        <p>Total Penjualan: {{ number_format($query->sum('total_penjualan'), 2) }}</p>
        <p>Total Pembelian: {{ number_format($query->sum('total_pembelian'), 2) }}</p>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ \Carbon\Carbon::now()->year }} STM Esteh Manis. All Rights Reserved.</p>
    </footer>

</body>
</html>
