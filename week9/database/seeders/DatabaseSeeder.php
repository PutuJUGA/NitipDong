<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Titipan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Kategori (Relasi Induk ORM)
        $katMakanan = Kategori::create([
            'nama_kategori' => 'Makanan Berat',
            'slug'          => 'makanan-berat',
            'icon'          => '🍛',
            'deskripsi'     => 'Nasi ayam, bebek, soto, mie, dan menu porsi kenyang sirkel kampus.',
        ]);

        $katMinuman = Kategori::create([
            'nama_kategori' => 'Minuman & Kopi',
            'slug'          => 'minuman-kopi',
            'icon'          => '☕',
            'deskripsi'     => 'Kopi susu, es teh jumbo, matcha latte, jus segar pereda dahaga.',
        ]);

        $katSnack = Kategori::create([
            'nama_kategori' => 'Snack & Camilan',
            'slug'          => 'snack-camilan',
            'icon'          => '🍟',
            'deskripsi'     => 'Gorengan renyah, pastry, risoles mayo, dan jajanan sirkel nongkrong.',
        ]);

        $katMinimarket = Kategori::create([
            'nama_kategori' => 'Jastip Minimarket',
            'slug'          => 'jastip-minimarket',
            'icon'          => '🏪',
            'deskripsi'     => 'Air mineral galon/botol, biskuit, rokok, hingga alat tulis koperasi.',
        ]);

        // 2. Seed Titipan (Relasi Anak ORM BelongsTo Kategori)
        Titipan::create([
            'kategori_id'       => $katMakanan->id,
            'nama_penitip'      => 'Syauqi Faradiz',
            'nama_barang'       => 'Ayam Geprek Sambal Korek Level 3 + Nasi',
            'lokasi_beli'       => 'Kantin Teknik Lt. 1',
            'harga_barang'      => 16000,
            'biaya_jastip'      => 2000,
            'jumlah'            => 1,
            'total_bayar'       => 18000,
            'status_pembayaran' => 'lunas',
            'catatan'           => 'Sambal dipisah ya, timun dibanyakin.',
        ]);

        Titipan::create([
            'kategori_id'       => $katMinuman->id,
            'nama_penitip'      => 'Lalu Fathan',
            'nama_barang'       => 'Es Kopi Susu Gula Aren Double Shot',
            'lokasi_beli'       => 'Kopi Kenangan Fakultas',
            'harga_barang'      => 18000,
            'biaya_jastip'      => 2000,
            'jumlah'            => 1,
            'total_bayar'       => 20000,
            'status_pembayaran' => 'belum_lunas',
            'catatan'           => 'Less sugar 50%, less ice.',
        ]);

        Titipan::create([
            'kategori_id'       => $katMakanan->id,
            'nama_penitip'      => 'Putu Oka',
            'nama_barang'       => 'Nasi Padang Rendang Daging + Perkedel',
            'lokasi_beli'       => 'RM Minang Saiyo',
            'harga_barang'      => 24000,
            'biaya_jastip'      => 3000,
            'jumlah'            => 1,
            'total_bayar'       => 27000,
            'status_pembayaran' => 'lunas',
            'catatan'           => 'Kuah gulai dan daun singkong banjir.',
        ]);

        Titipan::create([
            'kategori_id'       => $katSnack->id,
            'nama_penitip'      => 'Nadia Putri',
            'nama_barang'       => 'Croissant Cokelat Keju & Matcha Latte',
            'lokasi_beli'       => 'Bakery Corner Gedung A',
            'harga_barang'      => 25000,
            'biaya_jastip'      => 2500,
            'jumlah'            => 1,
            'total_bayar'       => 27500,
            'status_pembayaran' => 'belum_lunas',
            'catatan'           => 'Minta dipanaskan sebentar pas diambil.',
        ]);

        Titipan::create([
            'kategori_id'       => $katMinimarket->id,
            'nama_penitip'      => 'Dewi Lestari',
            'nama_barang'       => 'Air Mineral 1.5L + Sticky Notes Pastel',
            'lokasi_beli'       => 'Koperasi Mahasiswa',
            'harga_barang'      => 11000,
            'biaya_jastip'      => 1500,
            'jumlah'            => 1,
            'total_bayar'       => 12500,
            'status_pembayaran' => 'belum_lunas',
            'catatan'           => 'Sticky notes yang warna ungu atau pink.',
        ]);
    }
}
