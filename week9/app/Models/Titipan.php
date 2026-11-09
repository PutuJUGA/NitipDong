<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Titipan extends Model
{
    use HasFactory;

    protected $table = 'titipans';

    protected $fillable = [
        'kategori_id',
        'nama_penitip',
        'nama_barang',
        'lokasi_beli',
        'harga_barang',
        'biaya_jastip',
        'jumlah',
        'total_bayar',
        'status_pembayaran',
        'catatan',
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'harga_barang' => 'integer',
            'biaya_jastip' => 'integer',
            'jumlah' => 'integer',
            'total_bayar' => 'integer',
        ];
    }

    /**
     * Relasi Eloquent ORM: Titipan dimiliki oleh satu Kategori (N:1)
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Helper formatting Rupiah
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_barang, 0, ',', '.');
    }

    public function getFormattedJastipAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_jastip, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Status boolean helper
     */
    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }
}
