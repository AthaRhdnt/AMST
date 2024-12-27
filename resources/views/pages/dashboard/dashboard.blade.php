@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body text-center py-4">
                    <h2 class="mb-3">
                        Selamat Datang, 
                        @if (auth()->user()->role->nama_role == 'Pemilik')
                            Owner STM!
                        @else
                            Outlet {{ $outletName }}
                        @endif
                    </h2>
                    <h4>Anda login sebagai <span class="badge badge-dark">{{ auth()->user()->role->nama_role }}</span></h4>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role->nama_role == 'Pemilik')

    <div class="row text-center">
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Penjualan Tahun Ini
                </div>
                <div class="card-body">
                    <h3>Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <div class="text-center px-3">
                        <a  href="{{ route('laporan.index.transaksi', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')]) }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Penjualan Bulan Ini
                </div>
                <div class="card-body">
					<h3>Rp {{ number_format($transactionsThisMonth, 0, ',', '.') }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('laporan.index.transaksi', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}"  title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    Stok Hampir Habis
                </div>
                <div class="card-body">
                    <h3>{{ $lowStockCount }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('stok.index') }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6 ">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Outlet Aktif
                </div>
                <div class="card-body">
                    <h3>{{ $totalOutlets }}</h3>
                    <div class="text-center px-3">
                        <a href="{{ route('outlets.index') }}" title="Detail" class="badge badge-dark">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    Best Seller Menu
                </div>
                <div class="card-body scrollable-dashboard p-2">
                    <table class="table table-sm table-bordered table-striped" >
                        @foreach($topSellingItems as $item)
                            <tr>
                                <td class="pl-2">{{ $item->nama_menu }}</td>
                                <td width="10%" class="text-center">{{ $item->sales_count }}</td>
                                <td width="5%" class="px-3">
                                    <a href="{{ route('riwayat.index.transaksi', ['search' => $item->nama_menu, 'start_date' => $firstTransactionDate, 'end_date' => now()->today()->format('Y-m-d')]) }}" title="Detail" class="badge badge-dark">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
					{{-- <div class="mt-3">
                        {{ $topSellingItems->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white text-center">
                    Penjualan Hari Ini
                </div>
                <div class="card-body scrollable-dashboard p-2">
                    <table class="table table-sm table-bordered table-striped">
                        @foreach($recentTransactions as $item)
                        <tr>
                            <td class="pl-2">{{ $item->outlet->user->nama_user }}</td>
                            <td width="25%" class="text-right">Rp {{ number_format($item->total_today, 0, ',', '.') }}</td>
                            <td width="5%" class="px-3">
                                <a href="{{ route('laporan.index.transaksi', ['outlet_id' => $item->id_outlet, 'start_date' => now()->today()->format('Y-m-d'), 'end_date' => now()->today()->format('Y-m-d'), 'kode_transaksi' => 'ORD-']) }}" title="Detail" class="badge badge-dark">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
					{{-- <div class="mt-3">
                        {{ $recentTransactions->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    @else

    <div class="row mb-0">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="row">
                <div class="col">
                    <div class="card large-card">
                        <div class="card-header bg-primary text-white text-center">
                            Pesanan Hari Ini
                        </div>
                        <div class="card-body scrollable-dashboard p-2">
                            <table class="table table-sm table-bordered table-striped" >
                                @foreach($todayTransactions as $item)
                                    <tr>
                                        <td width="50%">{{ $item->kode_transaksi }}</td>
                                        <td width="15%" class="text-wrap">{{ $item->detailPelanggan->nama_pelanggan ?? '' }}</td>
                                        <td width="5%" class="text-center">
                                            <form action="{{ route('transaksi.status', ['transaksi' => $item->id_transaksi]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" title="Status" style="border: none; background: none;">
                                                    @if($item->status == 'proses')
                                                        <i class="fas fa-check-circle fa-lg text-yellow"></i>
                                                    @else
                                                        <i class="fas fa-check-circle fa-lg text-green"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td width="5%" class="text-center">
                                            <a href="#" title="Detail" class="view-details" data-id="{{ $item->id_transaksi }}">
                                                <i class="fas fa-info-circle fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            {{-- <div class="mt-3">
                                {{ $todayTransactions->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card mini-card">
                        <div class="card-header bg-dark text-white text-center">
                            Best Seller Menu
                        </div>
                        <div class="card-body scrollable-dashboard p-2">
                            <table class="table table-sm table-bordered table-striped" >
                                @foreach($topSellingItems as $item)
                                    <tr>
                                        <td class="pl-2">{{ $item->nama_menu }}</td>
                                        <td width="10%" class="text-center">{{ $item->sales_count }}</td>
                                        <td width="5%" class="px-3">
                                            <a href="{{ route('riwayat.index.transaksi', ['search' => $item->nama_menu, 'start_date' => null, 'end_date' => now()->today()->format('Y-m-d')]) }}" title="Detail" class="badge badge-dark">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            {{-- <div class="mt-3">
                                {{ $topSellingItems->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="row">
                <div class="col">
                    <div class="card order-card x-ovfl-hid">
                        <div class="card-header my-bg text-white text-center">
                            Order
                            <button type="button" class="close text-white" id="clear-cart">
                                <span aria-hidden="true"><i class="fas fa-trash-alt"></i></span>
                            </button>
                        </div>
                        <div class="card-body scrollable-card py-2">
                                <div id="order-items"></div>
                        </div>
                        <div id="cart-separator" class="separator" style="display: none;"></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>Rp <span id="total">0</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="row">
                <div class="col">
                    <div class="card large-card">
                        <div class="card-header bg-warning text-dark text-center">
                            Stok Hampir Habis
                        </div>
                        <div class="card-body scrollable-dashboard p-2">
                            <table class="table table-sm table-bordered table-striped table-valign-middle">
                                @if ($lowStock->isNotEmpty())
                                    @foreach($lowStock as $item)
                                        <tr>
                                            <td>{{ $item->stok->nama_barang }}</td>
                                            <td width="15%" class="text-center">{{ $item->jumlah }}</td>
                                            <td width="15%" class="text-center">
                                                <a href="{{ route('stok.index', ['search' => $item->stok->nama_barang]) }}" type="button" title="Detail">
                                                    <i class="fas fa-exclamation-circle fa-lg text-yellow"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">Tidak Ada</td>
                                        <td width="15%" class="text-center"><i class="fas fa-check-circle fa-lg text-green"></i></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card mini-card">
                        <div class="card-header bg-secondary text-white text-center">
                            Penjualan Hari Ini
                        </div>
                        <div class="card-body scrollable-dashboard p-2">
                            <table class="table table-sm table-bordered table-striped">
                                @foreach($recentTransactions as $item)
                                <tr>
                                    <td class="pl-2">{{ $item->outlet->user->nama_user }}</td>
                                    <td width="35%" class="text-right">Rp {{ number_format($item->total_today, 0, ',', '.') }}</td>
                                    <td width="5%" class="px-3">
                                        <a href="{{ route('laporan.index.transaksi', ['outlet_id' => $item->id_outlet, 'start_date' => now()->today()->format('Y-m-d'), 'end_date' => now()->today()->format('Y-m-d'), 'kode_transaksi' => 'ORD-']) }}" title="Detail" class="badge badge-dark">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                            {{-- <div class="mt-3">
                                {{ $recentTransactions->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>

<script>
    document.querySelectorAll('.view-details').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();  // Prevent default anchor behavior
            
            const transactionId = this.getAttribute('data-id');
            loadTransaksiDetails(transactionId);  // Call function to load order details
        });
    });

    function loadTransaksiDetails(transactionId) {
        console.log('Fetching details for Transaction ID:', transactionId);  // Debug log

        fetch(`/transaksi/${transactionId}`)  // Using the route with model binding
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response Data:', data);  // Log the response data
                if (data.success) {
                    const orderItemsContainer = document.getElementById('order-items');
                    orderItemsContainer.innerHTML = ''; // Clear previous 
                    
                    let subtotal = 0;
                    
                    // Loop through the order items and display them
                    data.orderItems.forEach(item => {
                        subtotal += parseFloat(item.subtotal);

                        orderItemsContainer.innerHTML += `
                            <div class="order-item">
                                <div class="item-details">
                                    <strong>${item.name}</strong><br>
                                    <small class="text-muted">Rp ${item.price.toLocaleString()}</small><br>
                                    <small>Jumlah: <span class="text-success">${item.quantity}</span></small>
                                </div>
                                <div class="item-subtotal">
                                    <span class="text-success">Rp ${item.subtotal.toLocaleString()}</span>
                                </div>
                            </div>
                        `;
                    });

                    const cartSeparator = document.getElementById('cart-separator'); // Ensure there's a separator element in your HTML
                    if (cartSeparator) {
                        cartSeparator.style.display = data.orderItems.length > 0 ? 'block' : 'none';
                    }

                    // Display the subtotal, total, and pay-total
                    // document.getElementById('subtotal').innerText = subtotal.toLocaleString();
                    // const total = subtotal;  // You can add additional charges to `total` if needed
                    document.getElementById('total').innerText = subtotal.toLocaleString();
                } else {
                    alert('Failed to load order details.');
                }
            })
            .catch(error => {
                console.error('Error fetching order details:', error);  // Log error
                alert('Error fetching order details.');
            });
    }

    document.getElementById('clear-cart').addEventListener('click', function () {
        // Clear the cart data from localStorage
        localStorage.removeItem('cart'); // Assuming 'cart' is the key for cart data

        // Clear the cart UI
        const orderItemsContainer = document.getElementById('order-items');
        orderItemsContainer.innerHTML = ''; // Remove all order items from the display

        // Hide the cart separator if it exists
        const cartSeparator = document.getElementById('cart-separator');
        if (cartSeparator) {
            cartSeparator.style.display = 'none';
        }

        // Reset totals
        document.getElementById('subtotal').innerText = '0';
        document.getElementById('total').innerText = '0';
        document.getElementById('pay-total').innerText = '0';

        // Optionally, display a message to confirm the action
        alert('Cart has been cleared.');
    });
</script>
@endsection

