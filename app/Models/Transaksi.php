<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = ['id_outlet', 'kode_transaksi', 'tanggal_transaksi', 'total_transaksi', 'created_at'];
    protected $casts = [
        'tanggal_transaksi' => 'date',
    ];

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'id_transaksi');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_transaksi');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'id_transaksi');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlets::class, 'id_outlet');
    }

    public static function getTransactionTimestamp()
    {
        // return Carbon::create(2024, 11, 30, 00, 00, 15);
        return now();
    }

    public static function transactionExistsForToday($id_outlet, $timestamp)
    {
        return self::where('id_outlet', $id_outlet)
                    ->whereDate('tanggal_transaksi', $timestamp->toDateString())
                    ->exists();
    }

    public static function transactionExistsBetween($id_outlet, $startDate, $endDate)
    {
        return self::where('id_outlet', $id_outlet)
                    ->whereDate('tanggal_transaksi', [$startDate, $endDate])
                    ->exists();
    }

    public static function getLastTransaction($id_outlet)
    {
        return self::where('id_outlet', $id_outlet)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->first();
    }

    public static function createSystemTransaction($request, $timestamp, $hexTimestamp, $id_outlet)
    {
        $adjustedSystemTimestamp = now()->subSeconds(1);

        $outletList = Outlets::all();

        foreach ($outletList as $outlet) {
            $record = self::create([
                'id_outlet' => $outlet->id_outlet,
                'kode_transaksi' => 'SYS-' . $hexTimestamp,
                'tanggal_transaksi' => $timestamp->getTimestamp(),
                'total_transaksi' => 0,
                'created_at' => $adjustedSystemTimestamp ,
            ]);
    
            // Loop through stok items and create RiwayatStok records
            $stokList = StokOutlet::where('id_outlet', $id_outlet)->get();
            foreach ($stokList as $stokItem) {
                $previousRiwayatStok = RiwayatStok::where('id_barang', $stokItem->id_barang)
                    ->whereHas('transaksi', function ($query) use ($record) {
                        $query->where('id_outlet', $record->id_outlet)
                            ->whereDate('tanggal_transaksi', '<', $record->tanggal_transaksi);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                // Determine stok_awal and stok_akhir
                $stokAwal = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($record->tanggal_transaksi)
                    ? $previousRiwayatStok->stok_awal
                    : ($previousRiwayatStok->stok_akhir ?? $stokItem->jumlah);
    
                $stokAkhir = $previousRiwayatStok && $previousRiwayatStok->transaksi->tanggal_transaksi->isSameDay($record->tanggal_transaksi)
                    ? $previousRiwayatStok->stok_akhir
                    : ($previousRiwayatStok->stok_akhir ?? $stokItem->jumlah); // Use the current stock amount
    
                // Create RiwayatStok record
                RiwayatStok::create([
                    'id_transaksi' => $record->id_transaksi,
                    'id_menu' => 97, // Adjust this to the correct menu ID
                    'id_barang' => $stokItem->id_barang,
                    'stok_awal' => $stokAwal,
                    'jumlah_pakai' => 0,
                    'stok_akhir' => $stokAkhir,
                    'keterangan' => null,
                    'created_at' => $adjustedSystemTimestamp ,
                ]);
            }
        }

        return $record;
    }
}
