<?php
/**
 * ============================================================================
 * CONTROLLER: TitipanController.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel)
 * Modul: Pertemuan 7 - Web Programming: Task 01 MVC Structure
 * ============================================================================
 * TANGGUNG JAWAB CONTROLLER:
 * 1. Menangani request pengguna (routing aksi: index, tambah, toggle-bayar, reset).
 * 2. Memanggil Model (Titipan.php) untuk mengambil atau memanipulasi data pesanan.
 * 3. Memproses transformasi data presentasi (format rupiah, teks generator WhatsApp, badge).
 * 4. Meneruskan data terstruktur ke View (titipan-page.php) untuk dirender.
 * ============================================================================
 */

require_once __DIR__ . '/../Model/Titipan.php';

class TitipanController
{
    private Titipan $model;

    public function __construct()
    {
        $this->model = new Titipan();
    }

    /**
     * Helper Format Rupiah
     */
    public function formatRupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Helper Generator Teks Tagihan WhatsApp Otomatis
     * (Fitur Inti NitipDong: Sekali klik salin rekap rapi ke grup WhatsApp)
     */
    public function generateWhatsAppText(array $room, array $pesananList, array $stat): string
    {
        $text = "🍱 *REKAP TITIPAN MAKANAN - {$room['namaRoom']}*\n";
        $text .= "👤 Host/Pemesan: {$room['host']}\n";
        $text .= "📍 Resto: {$room['targetResto']}\n";
        $text .= "💳 Rekening: {$room['rekeningHost']}\n";
        $text .= "⏰ Batas Bayar: {$room['deadlineOrder']}\n";
        $text .= "-------------------------------------------\n";

        $i = 1;
        foreach ($pesananList as $item) {
            $statusIcon = $item['isLunas'] ? '✅ [LUNAS]' : '⏳ [BELUM BAYAR]';
            $hargaStr = $this->formatRupiah($item['harga']);
            $text .= "{$i}. *{$item['namaPenitip']}* - {$item['menu']} ({$hargaStr})\n";
            $text .= "   Catatan: {$item['catatan']}\n";
            $text .= "   Status: {$statusIcon}\n";
            $i++;
        }

        $text .= "-------------------------------------------\n";
        $text .= "📊 *TOTAL TAGIHAN:* " . $this->formatRupiah($stat['totalTagihan']) . "\n";
        $text .= "✅ *Terkumpul:* " . $this->formatRupiah($stat['totalLunas']) . " ({$stat['jumlahLunas']} orang)\n";
        $text .= "⚠️ *Sisa Belum Bayar:* " . $this->formatRupiah($stat['totalBelumLunas']) . " ({$stat['jumlahBelumLunas']} orang)\n";
        $text .= "\nMohon yang statusnya masih belum bayar segera transfer ya, terima kasih! 🙏";

        return $text;
    }

    /**
     * Dispatcher Utama Alur Request
     */
    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'tambah':
                $this->tambahPesanan();
                break;
            case 'toggle-bayar':
                $this->toggleStatusBayar();
                break;
            case 'reset':
                $this->resetData();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    /**
     * Action Index: Menampilkan Dashboard Titipan
     */
    public function index(): void
    {
        // 1. Ambil data mentah dari Model
        $roomInfo    = $this->model->getRoomInfo();
        $daftarRaw   = $this->model->getAllPesanan();
        $statRaw     = $this->model->getStatistik();

        // 2. Format data untuk kebutuhan View
        $formattedPesanan = [];
        $no = 1;
        foreach ($daftarRaw as $item) {
            $item['nomor']       = '#' . str_pad((string)$no, 2, '0', STR_PAD_LEFT);
            $item['hargaFormat'] = $this->formatRupiah($item['harga']);
            $item['statusBadge'] = $item['isLunas']
                ? ['label' => 'LUNAS', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800', 'icon' => '✅']
                : ['label' => 'BELUM BAYAR', 'class' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800', 'icon' => '⏳'];
            $formattedPesanan[]  = $item;
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

        // Buat teks WhatsApp siap copy
        $waText = $this->generateWhatsAppText($roomInfo, $daftarRaw, $statRaw);

        // Flash Message jika ada
        $pesanNotif = $_SESSION['flash_notif'] ?? null;
        unset($_SESSION['flash_notif']);

        // Payload data untuk View
        $viewData = [
            'room'        => $roomInfo,
            'pesanan'     => $formattedPesanan,
            'stat'        => $statistik,
            'waText'      => $waText,
            'pesanNotif'  => $pesanNotif,
            'controller'  => $this
        ];

        // 3. Render View
        $this->render('titipan-page', $viewData);
    }

    /**
     * Action Tambah Pesanan Baru
     */
    public function tambahPesanan(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama    = $_POST['namaPenitip'] ?? '';
            $menu    = $_POST['menu'] ?? '';
            $resto   = $_POST['resto'] ?? '';
            $harga   = (int) ($_POST['harga'] ?? 0);
            $catatan = $_POST['catatan'] ?? '';

            if (!empty($nama) && !empty($menu) && $harga > 0) {
                $this->model->tambahPesanan($nama, $menu, $resto, $harga, $catatan);
                $_SESSION['flash_notif'] = [
                    'tipe'  => 'success',
                    'teks'  => "Titipan untuk <strong>" . htmlspecialchars($nama) . "</strong> berhasil ditambahkan!"
                ];
            } else {
                $_SESSION['flash_notif'] = [
                    'tipe'  => 'error',
                    'teks'  => "Mohon lengkapi Nama Penitip, Menu, dan Nominal Harga!"
                ];
            }
        }
        header('Location: index.php');
        exit;
    }

    /**
     * Action Toggle Status Lunas / Utang
     */
    public function toggleStatusBayar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->toggleStatusBayar($id);
            $_SESSION['flash_notif'] = [
                'tipe'  => 'info',
                'teks'  => "Status pembayaran pesanan #{$id} berhasil diperbarui."
            ];
        }
        header('Location: index.php');
        exit;
    }

    /**
     * Action Reset Data Demo
     */
    public function resetData(): void
    {
        $this->model->resetData();
        $_SESSION['flash_notif'] = [
            'tipe'  => 'info',
            'teks'  => "Data pesanan berhasil dikembalikan ke status awal."
        ];
        header('Location: index.php');
        exit;
    }

    /**
     * Merender file View dengan passing variabel
     */
    private function render(string $viewName, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../View/' . $viewName . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Error: Berkas View [{$viewName}] tidak ditemukan di {$viewPath}";
        }
    }
}
