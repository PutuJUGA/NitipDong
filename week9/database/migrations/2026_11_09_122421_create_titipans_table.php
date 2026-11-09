<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('titipans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->string('nama_penitip', 100);
            $table->string('nama_barang', 150);
            $table->string('lokasi_beli', 150);
            $table->unsignedInteger('harga_barang');
            $table->unsignedInteger('biaya_jastip')->default(2000);
            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->unsignedInteger('total_bayar');
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titipans');
    }
};
