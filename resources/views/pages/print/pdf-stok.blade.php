@extends('pages.print.base-pdf')

@section('title', 'Laporan Stok')

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
            {{-- {{$data}} --}}
                <tr>
                    <td>{{ $data->nama_barang }}</td>
                    <td>{{ session('outlet_id') == '' ? $data->sum_stok_awal : $data->stok_awal}}</td>
                    <td>{{ $data->jumlah_tambah }}</td>
                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_kurang) }})</td>
                    <td>{{ $data->jumlah_beli }}</td>
                    <td class="text-center" style="color: red;">({{ abs($data->jumlah_pakai) }})</td>
                    <td>{{ session('outlet_id') == '' ? $data->sum_stok_akhir : $data->stok_akhir}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        
    </div>
</main>
