@extends('pages.print.base.pdf')

@section('title', 'Laporan Transaksi')

@section('content')
<main>
    <div class="report-title">
        <img src="{{ public_path('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <h1>@yield('title')</h1>
        <p>Tanggal {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p>Dicetak pada {{ \Carbon\Carbon::parse(now())->timezone('Asia/Bangkok')->format('d M Y - H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Outlet</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Pesanan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $data)
                <tr>
                    <td>{{ $data->outlet->user->nama_user }}</td>
                    <td>{{ $data->kode_transaksi }}</td>
                    <td class="text-center">{{ $data->tanggal_transaksi->format('d-m-Y') }}</td>
                    <td class="text-center">{{ Carbon\Carbon::parse($data->created_at)->timezone('Asia/Bangkok')->format('H:i:s') }}</td>
                    <td>
                        @if ($data->detailTransaksi && $data->detailTransaksi->isNotEmpty())
                            @foreach ($data->detailTransaksi as $detil)
                                {{ $detil->menu->nama_menu }} ({{ $detil->jumlah }}){{ !$loop->last ? ',' : '' }}
                            @endforeach
                        @elseif ($data->detailPembelian && $data->detailPembelian->isNotEmpty())
                            @foreach ($data->detailPembelian as $detil)
                                {{ $detil->stok->nama_barang }} ({{ $detil->jumlah }}){{ !$loop->last ? ',' : '' }}
                            @endforeach
                        @endif
                    </td>
                    <td class="text-right">Rp. {{ number_format($data->total_transaksi) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</main>
