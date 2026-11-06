<?php
/**
 * ============================================================================
 * CLASS: Titipan.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Jasa Titip Sirkel Kampus/Kantor)
 * Modul: Pertemuan 6 - Pemrograman Berorientasi Objek (OOP) PHP
 * ============================================================================
 * PENJELASAN KONSEP OOP PADA BERKAS INI:
 * 1. CLASS:
 *    `Titipan` bertindak sebagai blueprint / cetak biru untuk setiap entitas
 *    pesanan atau barang titipan di aplikasi NitipDong.
 * 
 * 2. ENKAPSULASI (ENCAPSULATION):
 *    Semua atribut/data krusial dibungkus menggunakan access modifier `private`
 *    dan `protected`. Hal ini mencegah modifikasi sembarangan dari luar class
 *    dan menjaga integritas data (data integrity).
 * 
 * 3. CONSTRUCTOR (__construct):
 *    Method ajaib (magic method) yang secara otomatis dijalankan saat objek baru
 *    diciptakan (`new Titipan(...)`) untuk menginisialisasi nilai awal properti.
 * 
 * 4. METHOD:
 *    Fungsi internal objek untuk mengeksekusi logika bisnis, seperti:
 *    - `getDetailTitipan()`: Merangkum seluruh rincian barang titipan.
 *    - `hitungBiayaTotal()`: Mengkalkulasi akumulasi harga barang & jasa titip.
 *    - Getter & Setter: Jalur resmi membaca dan memperbarui nilai properti private.
 * ============================================================================
 */

class Titipan
{
    // ========================================================================
    // 1. PROPERTY DENGAN ENKAPSULASI (PRIVATE & PROTECTED)
    // ========================================================================
    
    /** @var string ID Unik Pesanan Titipan */
    private string $id;

    /** @var string Nama Makanan / Minuman / Barang yang dititipkan */
    private string $namaBarang;

    /** @var string Nama teman atau penitip (pengirim permintaan titipan) */
    private string $pengirim;

    /** @var string Lokasi Resto / Kantin / Toko tempat pembelian */
    private string $lokasiBeli;

    /** @var int Harga asli barang dari resto / toko */
    private int $hargaBarang;

    /** @var int Biaya jasa titip (fee jastip) per satuan barang */
    private int $biayaTitip;

    /** @var int Jumlah / kuantitas barang yang dititipkan */
    private int $jumlah;

    /** @var string Status pesanan titipan saat ini */
    private string $status;

    /** @var string Catatan khusus pesanan (misal: level pedas, jangan pakai seledri) */
    private string $catatan;

    /** @var string Kategori barang (Makanan Berat, Minuman, Cemilan, ATK/Lainnya) */
    private string $kategori;

    /** @var string Waktu / jam pesanan titipan dibuat */
    private string $waktuPesan;

    /** 
     * @var string Avatar emoji penitip
     * Menggunakan modifier 'protected' sebagai contoh bahwa properti ini
     * dapat diwariskan ke subclass jika kelak class Titipan diturunkan (Inheritance).
     */
    protected string $avatar;

    // ========================================================================
    // 2. CONSTRUCTOR (__construct)
    // ========================================================================

    /**
     * Menginisialisasi objek Titipan baru
     * 
     * @param string $id          ID Unik titipan (misal: 'NTP-001')
     * @param string $namaBarang   Nama barang / menu pesanan
     * @param string $pengirim     Nama rekan sirkel yang menitip
     * @param string $lokasiBeli   Nama resto, warung, atau kantin
     * @param int    $hargaBarang  Harga asli barang (IDR)
     * @param int    $biayaTitip   Ongkos jasa titip (default: 3000)
     * @param int    $jumlah       Kuantitas pesanan (default: 1)
     * @param string $status       Status titipan (default: 'Menunggu Dibeli')
     * @param string $catatan      Catatan permintaan khusus
     * @param string $kategori     Kategori titipan
     * @param string $avatar       Emoji avatar pengguna
     * @param string|null $waktuPesan Jam input (otomatis WIB jika null)
     */
    public function __construct(
        string $id,
        string $namaBarang,
        string $pengirim,
        string $lokasiBeli,
        int $hargaBarang,
        int $biayaTitip = 3000,
        int $jumlah = 1,
        string $status = 'Menunggu Dibeli',
        string $catatan = '-',
        string $kategori = 'Makanan',
        string $avatar = '🍱',
        ?string $waktuPesan = null
    ) {
        $this->id          = $id;
        $this->namaBarang  = trim($namaBarang);
        $this->pengirim    = trim($pengirim);
        $this->lokasiBeli  = trim($lokasiBeli);
        $this->hargaBarang = max(0, $hargaBarang);
        $this->biayaTitip  = max(0, $biayaTitip);
        $this->jumlah      = max(1, $jumlah);
        $this->status      = $status;
        $this->catatan     = !empty(trim($catatan)) ? trim($catatan) : 'Tidak ada catatan khusus';
        $this->kategori    = $kategori;
        $this->avatar      = $avatar;
        $this->waktuPesan  = $waktuPesan ?? (date('H:i') . ' WIB');
    }

    // ========================================================================
    // 3. METHOD UTAMA (BUSINESS LOGIC)
    // ========================================================================

    /**
     * METHOD 1: hitungBiayaTotal()
     * Memproses perhitungan biaya atau estimasi layanan titipan.
     * 
     * Rumus:
     * Total = (Harga Barang * Jumlah) + (Biaya Titip * Jumlah) + Patungan Ongkir - Diskon
     * 
     * @param int $diskon     Potongan promo / diskon per pesanan (default: 0)
     * @param int $ongkirBagi Porsi patungan ongkir resto (default: 0)
     * @return int Nominal total tagihan titipan yang harus dibayar
     */
    public function hitungBiayaTotal(int $diskon = 0, int $ongkirBagi = 0): int
    {
        $subtotalBarang = $this->hargaBarang * $this->jumlah;
        $totalJasaTitip = $this->biayaTitip * $this->jumlah;
        $total = ($subtotalBarang + $totalJasaTitip + $ongkirBagi) - $diskon;

        return max(0, $total);
    }

    /**
     * METHOD 2: getDetailTitipan()
     * Mengembalikan ringkasan komprehensif seluruh data objek titipan
     * dalam bentuk array asosiatif terformat yang siap dikonsumsi oleh view HTML.
     * 
     * @return array
     */
    public function getDetailTitipan(): array
    {
        return [
            'id'                => $this->id,
            'namaBarang'        => $this->namaBarang,
            'pengirim'          => $this->pengirim,
            'lokasiBeli'        => $this->lokasiBeli,
            'kategori'          => $this->kategori,
            'hargaBarang'       => $this->hargaBarang,
            'hargaBarangFmt'    => $this->formatRupiah($this->hargaBarang),
            'biayaTitip'        => $this->biayaTitip,
            'biayaTitipFmt'     => $this->formatRupiah($this->biayaTitip),
            'jumlah'            => $this->jumlah,
            'subtotalBarang'    => $this->getSubtotalBarang(),
            'subtotalBarangFmt' => $this->formatRupiah($this->getSubtotalBarang()),
            'totalJasaTitip'    => $this->getTotalJasaTitip(),
            'totalJasaTitipFmt' => $this->formatRupiah($this->getTotalJasaTitip()),
            'biayaTotal'        => $this->hitungBiayaTotal(),
            'biayaTotalFmt'     => $this->formatRupiah($this->hitungBiayaTotal()),
            'status'            => $this->status,
            'statusBadgeClass'  => $this->getStatusBadgeClass(),
            'statusIcon'        => $this->getStatusIcon(),
            'catatan'           => $this->catatan,
            'waktuPesan'        => $this->waktuPesan,
            'avatar'            => $this->avatar,
        ];
    }

    /**
     * Menghitung subtotal murni barang (harga * jumlah)
     * @return int
     */
    public function getSubtotalBarang(): int
    {
        return $this->hargaBarang * $this->jumlah;
    }

    /**
     * Menghitung total biaya jasa titip (biaya titip * jumlah)
     * @return int
     */
    public function getTotalJasaTitip(): int
    {
        return $this->biayaTitip * $this->jumlah;
    }

    /**
     * METHOD: generatePesanWA()
     * Menghasilkan teks invoice/tagihan personal yang rapi untuk dikirimkan
     * ke nomor WhatsApp penitip.
     */
    public function generatePesanWA(): string
    {
        $totalFmt = $this->formatRupiah($this->hitungBiayaTotal());
        $subtotalFmt = $this->formatRupiah($this->getSubtotalBarang());
        $feeFmt = $this->formatRupiah($this->getTotalJasaTitip());

        $pesan = "Halo kak *{$this->pengirim}*! 🍱\n";
        $pesan .= "Rincian titipan kamu di *{$this->lokasiBeli}*:\n";
        $pesan .= "• {$this->namaBarang} ({$this->jumlah}x): {$subtotalFmt}\n";
        $pesan .= "• Jasa Titip (Jastip): {$feeFmt}\n";
        $pesan .= "• *Total Tagihan: {$totalFmt}*\n";
        $pesan .= "• Status: {$this->status}\n";
        $pesan .= "• Catatan: {$this->catatan}\n\n";
        $pesan .= "Terima kasih sudah menitip di NitipDong! 🙏";

        return $pesan;
    }

    /**
     * METHOD STATIC: generateRekapSemuaWA()
     * Menggabungkan seluruh objek titipan menjadi satu ringkasan pesan WhatsApp
     * kolektif siap kirim ke grup sirkel tongkrongan.
     * 
     * @param Titipan[] $daftarObjek
     */
    public static function generateRekapSemuaWA(array $daftarObjek): string
    {
        $totalSemua = 0;
        $pesan = "🍱 *REKAP TITIPAN SIRKEL - NITIPDONG* 🍱\n";
        $pesan .= "Tanggal: " . date('d M Y') . "\n";
        $pesan .= "----------------------------------------\n\n";

        foreach ($daftarObjek as $index => $item) {
            $totalItem = $item->hitungBiayaTotal();
            $totalSemua += $totalItem;
            $nomor = $index + 1;
            $statusSimbol = ($item->getStatus() === 'Selesai & Lunas') ? '✅ Lunas' : '⏳ Belum Bayar';

            $pesan .= "{$nomor}. *{$item->getPengirim()}* ({$statusSimbol})\n";
            $pesan .= "   Menu: {$item->getNamaBarang()} ({$item->getJumlah()}x)\n";
            $pesan .= "   Tempat: {$item->getLokasiBeli()}\n";
            $pesan .= "   Tagihan: {$item->formatRupiah($totalItem)}\n\n";
        }

        $helper = new self('TMP', '', '', '', 0);
        $pesan .= "----------------------------------------\n";
        $pesan .= "💰 *TOTAL SEMUA: " . $helper->formatRupiah($totalSemua) . "*\n";
        $pesan .= "Mohon segera transfer ke koordinator ya teman-teman. Makasih! 🙏";

        return $pesan;
    }

    // ========================================================================
    // 4. GETTER METHODS (MEMBACA PROPERTI PRIVATE SECARA AMAN)
    // ========================================================================

    public function getId(): string
    {
        return $this->id;
    }

    public function getNamaBarang(): string
    {
        return $this->namaBarang;
    }

    public function getPengirim(): string
    {
        return $this->pengirim;
    }

    public function getLokasiBeli(): string
    {
        return $this->lokasiBeli;
    }

    public function getHargaBarang(): int
    {
        return $this->hargaBarang;
    }

    public function getBiayaTitip(): int
    {
        return $this->biayaTitip;
    }

    public function getJumlah(): int
    {
        return $this->jumlah;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCatatan(): string
    {
        return $this->catatan;
    }

    public function getKategori(): string
    {
        return $this->kategori;
    }

    public function getWaktuPesan(): string
    {
        return $this->waktuPesan;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    // ========================================================================
    // 5. SETTER METHODS (MEMODIFIKASI PROPERTI DENGAN VALIDASI)
    // ========================================================================

    public function setNamaBarang(string $namaBarang): void
    {
        if (empty(trim($namaBarang))) {
            throw new InvalidArgumentException("Nama barang tidak boleh kosong.");
        }
        $this->namaBarang = trim($namaBarang);
    }

    public function setPengirim(string $pengirim): void
    {
        if (empty(trim($pengirim))) {
            throw new InvalidArgumentException("Nama pengirim tidak boleh kosong.");
        }
        $this->pengirim = trim($pengirim);
    }

    public function setLokasiBeli(string $lokasiBeli): void
    {
        $this->lokasiBeli = trim($lokasiBeli);
    }

    public function setHargaBarang(int $hargaBarang): void
    {
        if ($hargaBarang < 0) {
            throw new InvalidArgumentException("Harga barang tidak boleh bernilai negatif.");
        }
        $this->hargaBarang = $hargaBarang;
    }

    public function setBiayaTitip(int $biayaTitip): void
    {
        if ($biayaTitip < 0) {
            throw new InvalidArgumentException("Biaya jasa titip tidak boleh negatif.");
        }
        $this->biayaTitip = $biayaTitip;
    }

    public function setJumlah(int $jumlah): void
    {
        $this->jumlah = max(1, $jumlah);
    }

    public function setStatus(string $status): void
    {
        $allowedStatuses = [
            'Menunggu Dibeli',
            'Sedang Dibelikan',
            'Siap Diambil',
            'Selesai & Lunas',
            'Dibatalkan'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $this->status = 'Menunggu Dibeli';
            return;
        }

        $this->status = $status;
    }

    public function setCatatan(string $catatan): void
    {
        $this->catatan = !empty(trim($catatan)) ? trim($catatan) : 'Tidak ada catatan khusus';
    }

    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    // ========================================================================
    // 6. HELPER PRESENTASI & FORMATTING
    // ========================================================================

    /**
     * Memformat angka integer menjadi format mata uang Rupiah
     * Contoh: 25000 -> Rp 25.000
     */
    public function formatRupiah(int $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    /**
     * Menghasilkan kelas CSS Tailwind badge berdasarkan status pesanan
     */
    public function getStatusBadgeClass(): string
    {
        switch ($this->status) {
            case 'Sedang Dibelikan':
                return 'bg-blue-50 text-blue-700 border-blue-200 ring-1 ring-blue-500/20';
            case 'Siap Diambil':
                return 'bg-amber-50 text-amber-700 border-amber-200 ring-1 ring-amber-500/20';
            case 'Selesai & Lunas':
                return 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-1 ring-emerald-500/20';
            case 'Dibatalkan':
                return 'bg-rose-50 text-rose-700 border-rose-200 ring-1 ring-rose-500/20';
            case 'Menunggu Dibeli':
            default:
                return 'bg-orange-50 text-orange-700 border-orange-200 ring-1 ring-orange-500/20';
        }
    }

    /**
     * Menghasilkan icon status untuk antarmuka visual
     */
    public function getStatusIcon(): string
    {
        switch ($this->status) {
            case 'Sedang Dibelikan':
                return '🛵';
            case 'Siap Diambil':
                return '🛍️';
            case 'Selesai & Lunas':
                return '✅';
            case 'Dibatalkan':
                return '❌';
            case 'Menunggu Dibeli':
            default:
                return '⏳';
        }
    }

    /**
     * Mengubah data objek menjadi array biasa (berguna untuk session serialization)
     */
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'namaBarang'  => $this->namaBarang,
            'pengirim'    => $this->pengirim,
            'lokasiBeli'  => $this->lokasiBeli,
            'hargaBarang' => $this->hargaBarang,
            'biayaTitip'  => $this->biayaTitip,
            'jumlah'      => $this->jumlah,
            'status'      => $this->status,
            'catatan'     => $this->catatan,
            'kategori'    => $this->kategori,
            'avatar'      => $this->avatar,
            'waktuPesan'  => $this->waktuPesan,
        ];
    }

    /**
     * Merekonstruksi objek Titipan dari array data (factory method)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? ('NTP-' . time()),
            $data['namaBarang'] ?? 'Barang Titipan',
            $data['pengirim'] ?? 'Teman Sirkel',
            $data['lokasiBeli'] ?? 'Kantin Kampus',
            (int)($data['hargaBarang'] ?? 0),
            (int)($data['biayaTitip'] ?? 3000),
            (int)($data['jumlah'] ?? 1),
            $data['status'] ?? 'Menunggu Dibeli',
            $data['catatan'] ?? '-',
            $data['kategori'] ?? 'Makanan',
            $data['avatar'] ?? '🍱',
            $data['waktuPesan'] ?? null
        );
    }
}

