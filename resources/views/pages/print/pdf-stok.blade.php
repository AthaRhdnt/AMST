@extends('pages.print.base.pdf')

@section('title', 'Laporan Stok')

@section('content')
<main>
    <div class="report-title">
        <img src="{{ public_path('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <h1>@yield('title') {{ $outletName }}</h1>
        <p>Tanggal {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p>Dicetak pada {{ \Carbon\Carbon::parse(now())->timezone('Asia/Bangkok')->format('d M Y - H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Item</th>
                <th>Stok Awal</th>
                <th>Update <i class="far fa-plus-square"></i></th>
                <th>Update <i class="far fa-minus-square"></i></th>
                <th>Pembelian</th>
                <th>Terpakai</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stok as $data)
                <tr>
                    <td>{{ $data->nama_barang }}</td>
                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_stok_awal : $data->stok_awal}}</td>
                    <td class="text-center">{{ $data->jumlah_tambah }}</td>
                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_kurang) }})</td>
                    <td class="text-center">{{ $data->jumlah_beli }}</td>
                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_pakai) }})</td>
                    <td class="text-center">{{ session('outlet_id') == '' ? $data->sum_stok_akhir : $data->stok_akhir}}</td>
                </tr>
            @endforeach
        </tbody>
</main>
