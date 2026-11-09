<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'icon',
        'deskripsi',
    ];

    /**
     * Relasi Eloquent ORM: Satu kategori memiliki banyak pesanan titipan (1:N)
     */
    public function titipans(): HasMany
    {
        return $this->hasMany(Titipan::class, 'kategori_id');
    }
}
