<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Titipan;
use App\Models\Kategori;

/**
 * ============================================================================
 * API ENDPOINTS: LARAVEL ORM & CRUD NITIPDONG (INTEGRASI WEB UTAMA)
 * ============================================================================
 */

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'message' => 'Backend Laravel ORM NitipDong berjalan normal 🍱',
        'time'    => now()->toIso8601String(),
    ]);
});

// Ambil seluruh data titipan beserta relasi ORM kategori & statistik split-bill
Route::get('/titipans', function (Request $request) {
    $query = Titipan::with('kategori')->latest();

    if ($request->filled('status')) {
        $query->where('status_pembayaran', $request->status);
    }

    if ($request->filled('kategori_id')) {
        $query->where('kategori_id', $request->kategori_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama_penitip', 'like', "%{$search}%")
              ->orWhere('nama_barang', 'like', "%{$search}%")
              ->orWhere('lokasi_beli', 'like', "%{$search}%");
        });
    }

    $titipans = $query->get();
    $kategoris = Kategori::withCount('titipans')->get();

    $totalBiaya = (int) Titipan::sum('total_bayar');
    $totalLunas = (int) Titipan::where('status_pembayaran', 'lunas')->sum('total_bayar');
    $sisaUtang  = (int) Titipan::where('status_pembayaran', 'belum_lunas')->sum('total_bayar');
    $persenLunas = $totalBiaya > 0 ? round(($totalLunas / $totalBiaya) * 100) : 0;

    return response()->json([
        'success'   => true,
        'data'      => $titipans,
        'kategoris' => $kategoris,
        'stat'      => [
            'total_biaya'   => $totalBiaya,
            'total_lunas'   => $totalLunas,
            'sisa_utang'    => $sisaUtang,
            'persen_lunas'  => $persenLunas,
            'total_pesanan' => $titipans->count(),
        ],
    ]);
});

// Tambah titipan baru (CRUD Create)
Route::post('/titipans', function (Request $request) {
    $validated = $request->validate([
        'kategori_id'  => 'required|exists:kategoris,id',
        'nama_penitip' => 'required|string|max:100',
        'nama_barang'  => 'required|string|max:150',
        'lokasi_beli'  => 'required|string|max:150',
        'harga_barang' => 'required|integer|min:0',
        'biaya_jastip' => 'nullable|integer|min:0',
        'jumlah'       => 'required|integer|min:1',
        'catatan'      => 'nullable|string|max:500',
    ]);

    $biayaJastip = $validated['biaya_jastip'] ?? 2000;
    $total = ($validated['harga_barang'] * $validated['jumlah']) + $biayaJastip;

    $titipan = Titipan::create([
        'kategori_id'       => $validated['kategori_id'],
        'nama_penitip'      => $validated['nama_penitip'],
        'nama_barang'       => $validated['nama_barang'],
        'lokasi_beli'       => $validated['lokasi_beli'],
        'harga_barang'      => $validated['harga_barang'],
        'biaya_jastip'      => $biayaJastip,
        'jumlah'            => $validated['jumlah'],
        'total_bayar'       => $total,
        'status_pembayaran' => 'belum_lunas',
        'catatan'           => $validated['catatan'] ?? null,
    ]);

    $titipan->load('kategori');

    return response()->json([
        'success' => true,
        'message' => 'Titipan berhasil disimpan ke database!',
        'data'    => $titipan,
    ], 201);
});

// Ubah status pembayaran (CRUD Update / Toggle)
Route::patch('/titipans/{titipan}/toggle', function (Titipan $titipan) {
    $statusBaru = $titipan->status_pembayaran === 'lunas' ? 'belum_lunas' : 'lunas';
    $titipan->update(['status_pembayaran' => $statusBaru]);
    $titipan->load('kategori');

    return response()->json([
        'success' => true,
        'message' => "Status {$titipan->nama_penitip} berhasil diubah jadi {$statusBaru}",
        'data'    => $titipan,
    ]);
});

// Hapus titipan (CRUD Delete)
Route::delete('/titipans/{titipan}', function (Titipan $titipan) {
    $nama = $titipan->nama_penitip;
    $titipan->delete();

    return response()->json([
        'success' => true,
        'message' => "Pesanan {$nama} berhasil dihapus dari database",
    ]);
});

// Daftar Kategori Merchant
Route::get('/kategoris', function () {
    return response()->json([
        'success' => true,
        'data'    => Kategori::withCount('titipans')->get(),
    ]);
});
