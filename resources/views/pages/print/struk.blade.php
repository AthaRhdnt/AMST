@extends('pages.print.base.struk')

@section('title', 'Struk Transaksi')

@section('receipt')
<div>
    <div class="text-center">
        <img src="{{ asset('image/logo.png') }}" alt="STM Esteh Manis Logo" class="logo">
        <p>{{ $transaksi->outlet->alamat_outlet }}</p>
    </div>
    <p class="text-center">~o~</p>
    <div class="text-center">
        <p>Outlet {{ $transaksi->outlet->user->nama_user }}</p>
        <p>{{ \Carbon\Carbon::parse($transaksi->created_at)->timezone('Asia/Bangkok')->format('d-m-Y H:i:s') }}</p>
        <p>{{ $transaksi->kode_transaksi }}</p>
    </div>
    {{-- <p class="text-center">------------------------------</p> --}}
    <div class="separator-struk"></div>
    <table width="100%" style="border: 0;">
        @foreach($transaksi->detailTransaksi as $detail)
            <tr>
                <td colspan="3">{{ $detail->menu->nama_menu }}</td>
            </tr>
            <tr>
                <td>{{ $detail->jumlah }} x {{ number_format($detail->menu->harga_menu) }}</td>
                <td></td>
                <td class="text-right">Rp. {{ number_format($detail->subtotal) }}</td>
            </tr>
        @endforeach
    </table>
    <table width="100%" style="border: 0;">
        <tr>
            <td><strong>Total:</strong></td>
            <td class="text-right"><strong>Rp. {{ number_format($transaksi->total_transaksi) }}</strong></td>
        </tr>
    </table>
    {{-- <p class="text-center">------------------------------</p> --}}
    <div class="separator-struk"></div>
    <p class="text-center">~~ TERIMA KASIH ~~</p>
    <p class="text-center pad">.</p>
</div>

<script>
    window.onload = function() {
        window.print();
    };
    window.onafterprint = function() {
        window.close();
    };
</script>
@endsection
