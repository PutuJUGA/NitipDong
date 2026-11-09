<?php

namespace App\Http\Controllers;

use App\Models\Titipan;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TitipanController extends Controller
{
    /**
     * LANGKAH 5: Method index()
     * Menampilkan daftar pesanan titipan barang/makanan dengan relasi ORM Kategori,
     * filter status/kategori, serta ringkasan metrik split-bill sirkel NitipDong.
     */
    public function index(Request $request): View
    {
        // Query builder dengan Eager Loading Eloquent ORM (relasi 'kategori')
        $query = Titipan::with('kategori')->latest();

        // Filter berdasarkan status pembayaran jika dipilih
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter berdasarkan kategori jika dipilih
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Search berdasarkan nama penitip atau nama barang
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

        // Kalkulasi Statistik Keuangan Split Bill menggunakan ORM Aggregation
        $totalBiaya = Titipan::sum('total_bayar');
        $totalLunas = Titipan::where('status_pembayaran', 'lunas')->sum('total_bayar');
        $sisaUtang  = Titipan::where('status_pembayaran', 'belum_lunas')->sum('total_bayar');
        $persenLunas = $totalBiaya > 0 ? round(($totalLunas / $totalBiaya) * 100) : 0;

        return view('titipan.index', compact(
            'titipans',
            'kategoris',
            'totalBiaya',
            'totalLunas',
            'sisaUtang',
            'persenLunas'
        ));
    }

    /**
     * Simpan data titipan baru (Create - CRUD)
     */
    public function store(Request $request): RedirectResponse
    {
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

        Titipan::create([
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

        return redirect()->route('titipan.index')->with('success', 'Titipan baru berhasil ditambahkan! 🍱');
    }

    /**
     * Toggle status pembayaran (Update - CRUD)
     */
    public function toggleStatus(Titipan $titipan): RedirectResponse
    {
        $statusBaru = $titipan->status_pembayaran === 'lunas' ? 'belum_lunas' : 'lunas';
        $titipan->update(['status_pembayaran' => $statusBaru]);

        $pesan = $statusBaru === 'lunas' 
            ? "Status titipan {$titipan->nama_penitip} berhasil diubah jadi LUNAS! ✓" 
            : "Status titipan {$titipan->nama_penitip} dikembalikan ke BELUM LUNAS.";

        return redirect()->route('titipan.index')->with('success', $pesan);
    }

    /**
     * Hapus titipan (Delete - CRUD)
     */
    public function destroy(Titipan $titipan): RedirectResponse
    {
        $namaPenitip = $titipan->nama_penitip;
        $titipan->delete();

        return redirect()->route('titipan.index')->with('success', "Pesanan titipan {$namaPenitip} berhasil dihapus.");
    }
}

