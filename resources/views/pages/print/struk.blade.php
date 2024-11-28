<!-- Print Receipt Page (resources/views/transaksi/print.blade.php) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            border: 1px solid #000;
            padding: 10px;
            max-width: 400px;
            margin: 0 auto;
        }
        .receipt h2 {
            text-align: center;
        }
        .receipt ul {
            list-style-type: none;
            padding-left: 0;
        }
        .receipt li {
            margin-bottom: 5px;
        }
        .receipt .logo {
            display: block;
            margin: 0 auto 20px; /* Center the logo and give it some bottom margin */
            max-width: 150px; /* Adjust logo size */
        }
        .receipt .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
            <h2>Transaction Receipt</h2>
        </div>
        <p><strong>Transaction Code:</strong> {{ $transaksi->kode_transaksi }}</p>
        <p><strong>Outlet:</strong> {{ $transaksi->outlet->user->nama_user }}</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaksi->created_at)->timezone('Asia/Bangkok')->format('d-m-Y H:i:s') }}</p>
        <p><strong>Total:</strong> Rp {{ number_format($transaksi->total_transaksi, 0, ',', '.') }}</p>

        <h3>Details:</h3>
        <ul>
            @foreach($transaksi->detailTransaksi as $detail)
                <li>
                    <strong>Menu:</strong> {{ $detail->menu->nama_menu }}<br>
                    <strong>Quantity:</strong> {{ $detail->jumlah }}<br>
                    <strong>Subtotal:</strong> Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </li>
            @endforeach
        </ul>
        
        <p><strong>Thank you for your purchase!</strong></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.location.href = "{{ route('laporan.index.transaksi') }}";
        };
    </script>
</body>
</html>
