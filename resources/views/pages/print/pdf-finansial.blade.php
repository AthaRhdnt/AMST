@extends('pages.print.base.pdf')

@section('title', 'Laporan Finansial')

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
                <th>Tanggal</th>
                <th>Total Pembelian</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($finansial as $data)
            <tr>
                <td>{{ $data->outlet->user->nama_user }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->format('d-m-Y') }}</>
                <td class="text-right">Rp. {{ number_format($data->total_pembelian) }}</td>
                <td class="text-right">Rp. {{ number_format($data->total_penjualan) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">Total:</td>
                <td class="text-right">Rp. {{ number_format($finansial->sum('total_pembelian')) }}</td>
                <td class="text-right">Rp. {{ number_format($finansial->sum('total_penjualan')) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <p><strong>Total Pembelian:</strong> Rp. {{ number_format($finansial->sum('total_pembelian')) }}</p>
        <p><strong>Total Penjualan:</strong> Rp. {{ number_format($finansial->sum('total_penjualan')) }}</p>
    </div>
</main>
@endsection