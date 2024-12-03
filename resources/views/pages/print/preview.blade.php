@extends('pages.print.base.struk')

@section('title', 'Struk Transaksi')

@section('receipt')
<div class="receipt">
    <div class="receipt-header">
        <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <h2>Struk Transaksi</h2>
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
@endsection
