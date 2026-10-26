<?php
/**
 * ============================================================================
 * MODEL: Titipan.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Arsitektur MVC)
 * ============================================================================
 * TANGGUNG JAWAB MODEL:
 * 1. Mengelola data entitas Room Titipan dan daftar pesanan rekan sirkel.
 * 2. Menyediakan logika bisnis: kalkulasi total tagihan, penghitungan jumlah lunas/utang,
 *    dan persentase progres pembayaran split bill.
 * 3. Menyediakan method manipulasi data (tambah pesanan, update status lunas, hapus pesanan, reset data).
 * 4. Menyimpan data interaktif ke dalam $_SESSION agar mutasi data berjalan nyata dan persisten antar request.
 * 5. Model murni mengelola data tanpa kode presentasi HTML/CSS (Separation of Concerns).
 * ============================================================================
 */

class Titipan
{
    /**
     * Informasi Room Titipan
     */
    private array $roomInfo;

    /**
     * Koleksi Data Pesanan Teman
     */
    private array $daftarPesanan;

    public function __construct()
    {
        // 1. Inisialisasi Data Informasi Room Titipan
        $this->roomInfo = [
            'idRoom'        => 'ROOM-NITIP-07',
            'namaRoom'      => 'Makan Siang Sirkel Kampus & Kantor 🍱',
            'host'          => 'Budi Pratama (Koordinator Nitip)',
            'kontakHost'    => '0812-3456-7890',
            'rekeningHost'  => 'BCA 8271-992-019 a.n Budi Pratama / QRIS NitipDong',
            'targetResto'   => 'Kantin Pusat, Tahu Telor Budhe Yuni, & DIMSUM TITI',
            'deadlineOrder' => '11:45 WIB (Order Segera Ditutup)',
            'statusRoom'    => 'Menerima Pesanan (Open)',
            'tagline'       => 'Gak Pusing Lagi Ngurusin Titipan Makanan Temen!'
        ];

        // 2. Inisialisasi Session untuk persistensi data interaktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['nitipdong_pesanan']) && is_array($_SESSION['nitipdong_pesanan'])) {
            $this->daftarPesanan = $_SESSION['nitipdong_pesanan'];
        } else {
            // Data Default Daftar Titipan Rekan Sirkel
            $this->daftarPesanan = [
                [
                    'id'          => 1,
                    'namaPenitip' => 'Dimas Kurnia',
                    'menu'        => 'Tahu Telor Bumbu Kacang Petis',
                    'resto'       => 'Tahu Telor Budhe Yuni',
                    'catatan'     => 'Bumbu kacang pedas sedang, kerupuk dipisah',
                    'harga'       => 20000,
                    'isLunas'     => true,
                    'metodeBayar' => 'Transfer BCA',
                    'waktuInput'  => '11:15 WIB',
                    'avatar'      => '👨‍💻'
                ],
                [
                    'id'          => 2,
                    'namaPenitip' => 'Sarah Amalia',
                    'menu'        => 'Dimsum Mentai Mozzarella (4 pcs)',
                    'resto'       => 'DIMSUM TITI Premium',
                    'catatan'     => 'Saus mentai bakar ekstra, sendok plastik ya',
                    'harga'       => 26000,
                    'isLunas'     => false,
                    'metodeBayar' => 'Belum Bayar (Utang)',
                    'waktuInput'  => '11:20 WIB',
                    'avatar'      => '👩‍🎨'
                ],
                [
                    'id'          => 3,
                    'namaPenitip' => 'Rian Hidayat',
                    'menu'        => 'Paket Ayam Geprek Sambal Matah',
                    'resto'       => 'Ayam Geprek Bensu',
                    'catatan'     => 'Level 5 cabai rawit, nasi porsi normal',
                    'harga'       => 28000,
                    'isLunas'     => true,
                    'metodeBayar' => 'QRIS GoPay',
                    'waktuInput'  => '11:24 WIB',
                    'avatar'      => '🧑‍💻'
                ],
                [
                    'id'          => 4,
                    'namaPenitip' => 'Nadia Putri',
                    'menu'        => 'Kopi Susu Gula Aren (Large)',
                    'resto'       => 'Kopi Kenangan',
                    'catatan'     => 'Less sugar (50%), less ice',
                    'harga'       => 22000,
                    'isLunas'     => false,
                    'metodeBayar' => 'Belum Bayar (Utang)',
                    'waktuInput'  => '11:28 WIB',
                    'avatar'      => '👩‍💼'
                ],
                [
                    'id'          => 5,
                    'namaPenitip' => 'Kevin Sanjaya',
                    'menu'        => 'Bebek Goreng Bumbu Hitam Madura',
                    'resto'       => 'Bebek Sinjay & Rempah',
                    'catatan'     => 'Bumbu hitam serundeng dobel, sambal pencit',
                    'harga'       => 38000,
                    'isLunas'     => true,
                    'metodeBayar' => 'ShopeePay',
                    'waktuInput'  => '11:32 WIB',
                    'avatar'      => '🏃‍♂️'
                ],
                [
                    'id'          => 6,
                    'namaPenitip' => 'Fikri Haikal',
                    'menu'        => 'Croffle Salted Caramel Crunchy',
                    'resto'       => 'Sweet Bites & Toast Bar',
                    'catatan'     => 'Saus karamel banyakin buat camilan sore',
                    'harga'       => 24000,
                    'isLunas'     => false,
                    'metodeBayar' => 'Belum Bayar (Utang)',
                    'waktuInput'  => '11:36 WIB',
                    'avatar'      => '🎧'
                ]
            ];
            $this->simpanKeSession();
        }
    }

    /**
     * Menyimpan mutasi data ke Session PHP
     */
    private function simpanKeSession(): void
    {
        $_SESSION['nitipdong_pesanan'] = $this->daftarPesanan;
    }

    /**
     * Mengambil data profil Room Titipan
     */
    public function getRoomInfo(): array
    {
        return $this->roomInfo;
    }

    /**
     * Mengambil seluruh koleksi pesanan titipan
     */
    public function getAllPesanan(): array
    {
        return $this->daftarPesanan;
    }

    /**
     * Mengambil satu data pesanan berdasarkan ID
     */
    public function getPesananById(int $id): ?array
    {
        foreach ($this->daftarPesanan as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Menambahkan pesanan baru ke dalam daftar
     */
    public function tambahPesanan(string $nama, string $menu, string $resto, int $harga, string $catatan = ''): bool
    {
        $newId = count($this->daftarPesanan) > 0 ? max(array_column($this->daftarPesanan, 'id')) + 1 : 1;
        $waktu = date('H:i') . ' WIB';

        // Pilihan emoji avatar acak yang relevan
        $avatars = ['🍱', '🍔', '🍜', '🍕', '🥗', '☕', '🥐', '🍗'];
        $avatar  = $avatars[array_rand($avatars)];

        $this->daftarPesanan[] = [
            'id'          => $newId,
            'namaPenitip' => trim($nama),
            'menu'        => trim($menu),
            'resto'       => trim($resto ?: 'Resto Sekitar'),
            'catatan'     => trim($catatan ?: 'Pesanan standar'),
            'harga'       => max(0, $harga),
            'isLunas'     => false,
            'metodeBayar' => 'Belum Bayar (Utang)',
            'waktuInput'  => $waktu,
            'avatar'      => $avatar
        ];

        $this->simpanKeSession();
        return true;
    }

    /**
     * Mengubah status pembayaran (Lunas <-> Belum Bayar)
     */
    public function toggleStatusBayar(int $id): bool
    {
        foreach ($this->daftarPesanan as &$item) {
            if ($item['id'] === $id) {
                $item['isLunas'] = !$item['isLunas'];
                $item['metodeBayar'] = $item['isLunas'] ? 'Transfer Lunas' : 'Belum Bayar (Utang)';
                $this->simpanKeSession();
                return true;
            }
        }
        return false;
    }

    /**
     * Menghapus / membatalkan pesanan berdasarkan ID
     */
    public function hapusPesanan(int $id): bool
    {
        foreach ($this->daftarPesanan as $key => $item) {
            if ($item['id'] === $id) {
                unset($this->daftarPesanan[$key]);
                $this->daftarPesanan = array_values($this->daftarPesanan);
                $this->simpanKeSession();
                return true;
            }
        }
        return false;
    }

    /**
     * Mereset seluruh data pesanan ke default awal
     */
    public function resetData(): void
    {
        unset($_SESSION['nitipdong_pesanan']);
    }

    /**
     * Logika Bisnis: Menghitung metrik finansial split bill sirkel
     */
    public function getStatistik(): array
    {
        $totalPesanan    = count($this->daftarPesanan);
        $totalTagihan    = 0;
        $totalLunas      = 0;
        $totalBelumLunas = 0;
        $jumlahLunas     = 0;
        $jumlahBelum     = 0;

        foreach ($this->daftarPesanan as $item) {
            $totalTagihan += $item['harga'];
            if (!empty($item['isLunas'])) {
                $totalLunas += $item['harga'];
                $jumlahLunas++;
            } else {
                $totalBelumLunas += $item['harga'];
                $jumlahBelum++;
            }
        }

        $persentaseLunas  = $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 1) : 0;
        $rataRataPerOrang = $totalPesanan > 0 ? (int) round($totalTagihan / $totalPesanan) : 0;

        return [
            'totalPesanan'     => $totalPesanan,
            'totalTagihan'     => $totalTagihan,
            'totalLunas'       => $totalLunas,
            'totalBelumLunas'  => $totalBelumLunas,
            'jumlahLunas'      => $jumlahLunas,
            'jumlahBelumLunas' => $jumlahBelum,
            'persentaseLunas'  => $persentaseLunas,
            'rataRataPerOrang' => $rataRataPerOrang
        ];
    }
}
