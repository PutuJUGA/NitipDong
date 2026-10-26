<?php
/**
 * ============================================================================
 * CONTROLLER: TitipanController.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Task 02: Build the Controller)
 * ============================================================================
 * ALUR ARSITEKTUR SESUAI SLIDE PERTEMUAN 7:
 *   USER -> REQUEST -> CONTROLLER (index(), show(), process()) -> PROCESS -> VIEW
 *
 * TANGGUNG JAWAB CONTROLLER:
 * 1. Menerima request HTTP dari pengguna melalui router / dispatcher (handleRequest).
 * 2. Menjalankan method Controller spesifik:
 *    - index()   : Menampilkan daftar seluruh titipan pesanan & ringkasan finansial split bill.
 *    - show()    : Menampilkan detail rincian satu pesanan titipan berdasarkan ID.
 *    - process() : Memproses mutasi data (tambah titipan, toggle status lunas/utang, hapus, reset).
 * 3. Memanggil Model (Titipan.php) untuk query & manipulasi data pesanan sirkel.
 * 4. Melakukan formatting data presentasi (format rupiah, status badge, teks WhatsApp).
 * 5. Meneruskan data terstruktur ke View (titipan-page.php & titipan-detail.php) untuk dirender.
 * ============================================================================
 */

require_once __DIR__ . '/../Model/Titipan.php';

class TitipanController
{
    /**
     * Instance Model Titipan
     */
    private Titipan $model;

    /**
     * Inisialisasi Controller & Session
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new Titipan();
    }

    /**
     * Helper Format Rupiah (Rp XX.XXX)
     */
    public function formatRupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Helper Generator Teks Tagihan WhatsApp Kolektif Sirkel
     * (Fitur Inti NitipDong: Sekali klik salin rekap rapi ke grup WhatsApp tongkrongan)
     */
    public function generateWhatsAppText(array $room, array $pesananList, array $stat): string
    {
        $text  = "🍱 *REKAP PESANAN MAKANAN - {$room['namaRoom']}*\n";
        $text .= "👤 Host / Pemesan : {$room['host']}\n";
        $text .= "📍 Resto Tujuan   : {$room['targetResto']}\n";
        $text .= "💳 Rekening/QRIS  : {$room['rekeningHost']}\n";
        $text .= "⏰ Batas Transfer : {$room['deadlineOrder']}\n";
        $text .= "-------------------------------------------\n";

        $i = 1;
        foreach ($pesananList as $item) {
            $statusIcon = $item['isLunas'] ? '✅ [LUNAS]' : '⏳ [BELUM BAYAR / UTANG]';
            $hargaStr   = $this->formatRupiah($item['harga']);
            $text .= "{$i}. *{$item['namaPenitip']}* - {$item['menu']} ({$hargaStr})\n";
            $text .= "   Catatan : {$item['catatan']}\n";
            $text .= "   Status  : {$statusIcon}\n";
            $i++;
        }

        $text .= "-------------------------------------------\n";
        $text .= "📊 *TOTAL TAGIHAN :* " . $this->formatRupiah($stat['totalTagihan']) . "\n";
        $text .= "✅ *Terkumpul     :* " . $this->formatRupiah($stat['totalLunas']) . " ({$stat['jumlahLunas']} orang)\n";
        $text .= "⚠️ *Sisa Utang     :* " . $this->formatRupiah($stat['totalBelumLunas']) . " ({$stat['jumlahBelumLunas']} orang)\n";
        $text .= "-------------------------------------------\n";
        $text .= "Mohon teman-teman yang statusnya masih belum bayar segera transfer ya, terima kasih! 🙏🍱";

        return $text;
    }

    /**
     * Helper Generator Teks Reminder WhatsApp Personal
     * (Digunakan saat membuka rincian pesanan via show())
     */
    public function generatePersonalWhatsAppReminder(array $room, array $item): string
    {
        $statusStr = $item['isLunas'] ? 'Lunas' : 'Belum Lunas';
        $nominal = $this->formatRupiah($item['harga']);

        $msg  = "Halo {$item['namaPenitip']}! 👋\n\n";
        $msg .= "Berikut rincian pesanan titipan kamu di *NitipDong*:\n";
        $msg .= "🍽️ *Menu:* {$item['menu']}\n";
        $msg .= "📍 *Resto:* {$item['resto']}\n";
        $msg .= "💰 *Total:* {$nominal}\n";
        $msg .= "📌 *Status:* {$statusStr}\n\n";

        if (!$item['isLunas']) {
            $msg .= "Bisa tolong ditransfer ke:\n";
            $msg .= "💳 *{$room['rekeningHost']}*\n";
            $msg .= "Batas waktu: *{$room['deadlineOrder']}*.\n\n";
            $msg .= "Kalau sudah transfer, kabari ya biar langsung ditandai lunas. Terima kasih! 🙏";
        } else {
            $msg .= "Pembayaran kamu sudah terverifikasi *LUNAS*. Pesananmu sedang diproses oleh host. Terima kasih! ✨";
        }

        return $msg;
    }

    /**
     * ========================================================================
     * REQUEST HANDLER / DISPATCHER
     * Menerima request pengguna dan mengarahkan proses ke Method Controller yang sesuai
     * ========================================================================
     */
    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            // Method 1: show() -> Menampilkan rincian pesanan tertentu
            case 'show':
                $id = (int) ($_GET['id'] ?? 0);
                $this->show($id);
                break;

            // Method 2: process() -> Memproses request mutasi data
            case 'process':
                $subAction = $_POST['sub_action'] ?? $_GET['sub_action'] ?? null;
                $this->process($subAction);
                break;

            // Pintasan aksi langsung (routed ke process())
            case 'tambah':
            case 'toggle-bayar':
            case 'hapus':
            case 'reset':
                $this->process($action);
                break;

            // Method 3: index() -> Menampilkan halaman dashboard utama titipan
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    /**
     * ========================================================================
     * METHOD CONTROLLER 1: index()
     * Menangani request tampilan utama daftar titipan & ringkasan split bill
     * ========================================================================
     */
    public function index(): void
    {
        // 1. Memanggil Model untuk mengambil data room, daftar pesanan, dan statistik keuangan
        $roomInfo  = $this->model->getRoomInfo();
        $daftarRaw = $this->model->getAllPesanan();
        $statRaw   = $this->model->getStatistik();

        // 2. Filter opsional berdasarkan status bayar jika ada parameter query
        $filter = $_GET['filter'] ?? 'all';
        $daftarFiltered = [];

        foreach ($daftarRaw as $item) {
            if ($filter === 'lunas' && !$item['isLunas']) {
                continue;
            }
            if ($filter === 'belum' && $item['isLunas']) {
                continue;
            }
            $daftarFiltered[] = $item;
        }

        // 3. Formatting data presentasi untuk View
        $formattedPesanan = [];
        $no = 1;
        foreach ($daftarFiltered as $item) {
            $item['nomor']       = '#' . str_pad((string)$no, 2, '0', STR_PAD_LEFT);
            $item['hargaFormat'] = $this->formatRupiah($item['harga']);
            $item['statusBadge'] = $item['isLunas']
                ? [
                    'label' => 'LUNAS',
                    'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                    'icon'  => '✅'
                ]
                : [
                    'label' => 'BELUM BAYAR',
                    'class' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                    'icon'  => '⏳'
                ];
            $formattedPesanan[] = $item;
            $no++;
        }

        // Format angka statistik
        $statistik = [
            'totalPesanan'     => $statRaw['totalPesanan'],
            'totalTagihan'     => $this->formatRupiah($statRaw['totalTagihan']),
            'totalLunas'       => $this->formatRupiah($statRaw['totalLunas']),
            'totalBelumLunas'  => $this->formatRupiah($statRaw['totalBelumLunas']),
            'jumlahLunas'      => $statRaw['jumlahLunas'],
            'jumlahBelumLunas' => $statRaw['jumlahBelumLunas'],
            'persentaseLunas'  => $statRaw['persentaseLunas'],
            'rataRataPerOrang' => $this->formatRupiah($statRaw['rataRataPerOrang'])
        ];

        // Format pesan WhatsApp kolektif
        $waText = $this->generateWhatsAppText($roomInfo, $daftarRaw, $statRaw);

        // Flash message notifikasi
        $pesanNotif = $_SESSION['flash_notif'] ?? null;
        unset($_SESSION['flash_notif']);

        // 4. Meneruskan data terstruktur ke View
        $viewData = [
            'room'        => $roomInfo,
            'pesanan'     => $formattedPesanan,
            'stat'        => $statistik,
            'waText'      => $waText,
            'filterAktif' => $filter,
            'pesanNotif'  => $pesanNotif,
            'controller'  => $this
        ];

        $this->render('titipan-page', $viewData);
    }

    /**
     * ========================================================================
     * METHOD CONTROLLER 2: show($id)
     * Menangani request untuk menampilkan rincian / detail pesanan tertentu
     * ========================================================================
     */
    public function show(int $id): void
    {
        if ($id <= 0) {
            $_SESSION['flash_notif'] = [
                'tipe' => 'error',
                'teks' => 'ID Pesanan tidak valid!'
            ];
            header('Location: index.php');
            exit;
        }

        // 1. Memanggil Model untuk mendapatkan detail pesanan berdasarkan ID
        $item = $this->model->getPesananById($id);
        $roomInfo = $this->model->getRoomInfo();

        if (!$item) {
            $_SESSION['flash_notif'] = [
                'tipe' => 'error',
                'teks' => "Pesanan dengan ID #{$id} tidak ditemukan dalam daftar!"
            ];
            header('Location: index.php');
            exit;
        }

        // 2. Format data presentasi detail pesanan
        $item['hargaFormat'] = $this->formatRupiah($item['harga']);
        $item['statusBadge'] = $item['isLunas']
            ? [
                'label' => 'LUNAS',
                'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                'icon'  => '✅'
            ]
            : [
                'label' => 'BELUM BAYAR (UTANG)',
                'class' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                'icon'  => '⏳'
            ];

        // Buat teks reminder WhatsApp personal
        $personalWaText = $this->generatePersonalWhatsAppReminder($roomInfo, $item);

        $pesanNotif = $_SESSION['flash_notif'] ?? null;
        unset($_SESSION['flash_notif']);

        // 3. Meneruskan data ke View detail
        $viewData = [
            'item'           => $item,
            'room'           => $roomInfo,
            'personalWaText' => $personalWaText,
            'pesanNotif'     => $pesanNotif,
            'controller'     => $this
        ];

        $this->render('titipan-detail', $viewData);
    }

    /**
     * ========================================================================
     * METHOD CONTROLLER 3: process($action)
     * Menangani request aksi / mutasi data sesuai alur:
     * REQUEST -> CONTROLLER (process()) -> PROCESS -> VIEW / REDIRECT
     * ========================================================================
     */
    public function process(?string $action = null): void
    {
        $action = $action ?? $_POST['sub_action'] ?? $_GET['sub_action'] ?? 'index';

        switch ($action) {
            case 'tambah':
                $this->processTambahPesanan();
                break;

            case 'toggle-bayar':
                $this->processToggleBayar();
                break;

            case 'hapus':
                $this->processHapusPesanan();
                break;

            case 'reset':
                $this->processResetData();
                break;

            default:
                header('Location: index.php');
                exit;
        }
    }

    /**
     * Sub-Proses 1: Validasi form & simpan pesanan baru melalui Model
     */
    private function processTambahPesanan(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama    = trim($_POST['namaPenitip'] ?? '');
            $menu    = trim($_POST['menu'] ?? '');
            $resto   = trim($_POST['resto'] ?? '');
            $harga   = (int) ($_POST['harga'] ?? 0);
            $catatan = trim($_POST['catatan'] ?? '');

            // Validasi input
            if (!empty($nama) && !empty($menu) && $harga > 0) {
                $this->model->tambahPesanan($nama, $menu, $resto, $harga, $catatan);
                $_SESSION['flash_notif'] = [
                    'tipe' => 'success',
                    'teks' => "Titipan untuk <strong>" . htmlspecialchars($nama) . "</strong> (" . htmlspecialchars($menu) . ") berhasil ditambahkan!"
                ];
            } else {
                $_SESSION['flash_notif'] = [
                    'tipe' => 'error',
                    'teks' => 'Gagal menambah pesanan! Pastikan Nama Penitip, Menu, dan Nominal Harga terisi dengan benar.'
                ];
            }
        }

        header('Location: index.php');
        exit;
    }

    /**
     * Sub-Proses 2: Ubah status pelunasan (Lunas / Utang) melalui Model
     */
    private function processToggleBayar(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $redirectShow = isset($_GET['from_show']) && $_GET['from_show'] == '1';

        if ($id > 0) {
            $berhasil = $this->model->toggleStatusBayar($id);
            if ($berhasil) {
                $_SESSION['flash_notif'] = [
                    'tipe' => 'info',
                    'teks' => "Status pembayaran pesanan #{$id} berhasil diperbarui!"
                ];
            } else {
                $_SESSION['flash_notif'] = [
                    'tipe' => 'error',
                    'teks' => "Gagal memperbarui status pesanan #{$id}."
                ];
            }
        }

        if ($redirectShow) {
            header("Location: index.php?action=show&id={$id}");
        } else {
            header('Location: index.php');
        }
        exit;
    }

    /**
     * Sub-Proses 3: Hapus / batalkan pesanan melalui Model
     */
    private function processHapusPesanan(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id > 0) {
            $berhasil = $this->model->hapusPesanan($id);
            if ($berhasil) {
                $_SESSION['flash_notif'] = [
                    'tipe' => 'info',
                    'teks' => "Pesanan #{$id} berhasil dibatalkan dan dihapus dari rekap."
                ];
            }
        }

        header('Location: index.php');
        exit;
    }

    /**
     * Sub-Proses 4: Reset data kembali ke demo awal melalui Model
     */
    private function processResetData(): void
    {
        $this->model->resetData();
        $_SESSION['flash_notif'] = [
            'tipe' => 'info',
            'teks' => 'Seluruh data pesanan sirkel berhasil dikembalikan ke data default NitipDong.'
        ];
        header('Location: index.php');
        exit;
    }

    /**
     * ========================================================================
     * METHOD RENDER VIEW
     * Mengekstrak payload data dan memuat berkas View yang sesuai
     * ========================================================================
     */
    private function render(string $viewName, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../View/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            http_response_code(404);
            echo "<div style='font-family:sans-serif;padding:2rem;color:#b91c1c;'>";
            echo "<h2>Error MVC View</h2>";
            echo "<p>Berkas View <strong>{$viewName}.php</strong> tidak ditemukan di direktori <code>{$viewPath}</code>.</p>";
            echo "</div>";
        }
    }
}
