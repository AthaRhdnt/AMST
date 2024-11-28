@extends('pages.print.base-pdf')

@section('title', 'Laporan Transaksi')

@section('content')
<main>
    <div class="report-title">
        <img src="{{ public_path('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <h1>@yield('title')</h1>
        <p>Date Range: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Outlet</th>
                <th>Tanggal</th>
                <th>Pesanan</th>
                <th>Stok Beli</th>
                <th>Menu Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $data)
                @foreach ($data->detailPembelian as $detil)
                    <tr>
                        {{-- Outlet User Name --}}
                        <td>{{ $data->outlet->user->nama_user }}</td>

                        {{-- Transaction Date --}}
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->format('d-m-Y') }}</td>

                        {{-- Stock Name and Quantity --}}
                        <td>
                            {{ $detil->stok->nama_barang }} ({{ $detil->jumlah }}){{ !$loop->last ? ',' : '' }}
                        </td>

                        {{-- Quantity in Right-Aligned Format --}}
                        <td class="text-right">{{ number_format($detil->jumlah) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</main>
