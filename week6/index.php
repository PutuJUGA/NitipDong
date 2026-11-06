<?php
/**
 * ============================================================================
 * INDEX.PHP - DYNAMIC HTML OUTPUT BERBASIS OOP PHP
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Jasa Titip Sirkel Kampus/Kantor)
 * Modul: Pertemuan 6 - Pemrograman Berorientasi Objek (OOP) PHP
 * ============================================================================
 * PEMENUHAN KETENTUAN TUGAS OOP PHP:
 * 1. CLASS & ENKAPSULASI:
 *    Menggunakan class Titipan dengan properti private/protected
 *    ($namaBarang, $pengirim, $biayaTitip, $status, dll).
 * 2. CONSTRUCTOR (__construct):
 *    Inisialisasi objek titipan secara otomatis saat pemanggilan `new Titipan(...)`.
 * 3. METHOD UTAMA:
 *    - $objek->getDetailTitipan() : Menampilkan rincian lengkap barang titipan.
 *    - $objek->hitungBiayaTotal() : Memproses kalkulasi total biaya & fee jastip.
 * 4. DYNAMIC HTML OUTPUT:
 *    Data pesanan pada web NitipDong tampil secara otomatis dan dinamis
 *    melalui pemanggilan method-method objek di dalam tag HTML PHP.
 * ============================================================================
 */

require_once __DIR__ . '/Titipan.php';

// Inisialisasi session untuk persistensi mutasi objek antarmuka
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flash notification message
$flashMessage = null;
$flashType = 'info';

// ============================================================================
// LOGIKA PERSISTENSI & PENANGANAN AKSI PENGGUNA (ACTION HANDLER)
// ============================================================================

// Data default awal jika session belum ada
$dataAwal = [
    [
        'id'          => 'NTP-001',
        'namaBarang'  => 'Tahu Telor Bumbu Kacang Petis',
        'pengirim'    => 'Dimas Kurnia',
        'lokasiBeli'  => 'Tahu Telor Budhe Yuni',
        'hargaBarang' => 20000,
        'biayaTitip'  => 3000,
        'jumlah'      => 1,
        'status'      => 'Sedang Dibelikan',
        'catatan'     => 'Bumbu kacang pedas sedang, kerupuk dipisah ya',
        'kategori'    => 'Makanan Berat',
        'avatar'      => '👨‍💻',
        'waktuPesan'  => '11:15 WIB'
    ],
    [
        'id'          => 'NTP-002',
        'namaBarang'  => 'Dimsum Mentai Mozzarella (4 pcs)',
        'pengirim'    => 'Sarah Amalia',
        'lokasiBeli'  => 'DIMSUM TITI Premium',
        'hargaBarang' => 26000,
        'biayaTitip'  => 4000,
        'jumlah'      => 2,
        'status'      => 'Menunggu Dibeli',
        'catatan'     => 'Saus mentai bakar ekstra pedas, garpu plastik 2',
        'kategori'    => 'Cemilan & Dimsum',
        'avatar'      => '👩‍🎨',
        'waktuPesan'  => '11:20 WIB'
    ],
    [
        'id'          => 'NTP-003',
        'namaBarang'  => 'Paket Ayam Geprek Sambal Matah Level 5',
        'pengirim'    => 'Rian Hidayat',
        'lokasiBeli'  => 'Ayam Geprek Bensu',
        'hargaBarang' => 28000,
        'biayaTitip'  => 3000,
        'jumlah'      => 1,
        'status'      => 'Selesai & Lunas',
        'catatan'     => 'Nasi porsi normal, sambal matah banyakin bawang',
        'kategori'    => 'Makanan Berat',
        'avatar'      => '🧑‍💼',
        'waktuPesan'  => '11:25 WIB'
    ],
    [
        'id'          => 'NTP-004',
        'namaBarang'  => 'Kopi Susu Gula Aren (Large)',
        'pengirim'    => 'Nadia Putri',
        'lokasiBeli'  => 'Kopi Kenangan Kampus',
        'hargaBarang' => 22000,
        'biayaTitip'  => 2500,
        'jumlah'      => 1,
        'status'      => 'Siap Diambil',
        'catatan'     => 'Less ice (50%), less sugar manis tipis',
        'kategori'    => 'Minuman Segar',
        'avatar'      => '👩‍💻',
        'waktuPesan'  => '11:30 WIB'
    ],
    [
        'id'          => 'NTP-005',
        'namaBarang'  => 'Paket Nasi Padang Rendang Daging',
        'pengirim'    => 'Budi Pratama',
        'lokasiBeli'  => 'RM Sinar Minang Jaya',
        'hargaBarang' => 25000,
        'biayaTitip'  => 3000,
        'jumlah'      => 1,
        'status'      => 'Menunggu Dibeli',
        'catatan'     => 'Kuah gulai & kuah rendang banjir, daun singkong banyak',
        'kategori'    => 'Makanan Berat',
        'avatar'      => '😎',
        'waktuPesan'  => '11:35 WIB'
    ]
];

// Inisialisasi daftar data di session
if (!isset($_SESSION['nitipdong_oop_list']) || !is_array($_SESSION['nitipdong_oop_list'])) {
    $_SESSION['nitipdong_oop_list'] = $dataAwal;
}

// Reset data ke default demo
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    $_SESSION['nitipdong_oop_list'] = $dataAwal;
    header("Location: index.php?msg=reset_success");
    exit;
}

// Update status pesanan via Setter method
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id']) && isset($_GET['new_status'])) {
    $targetId = $_GET['id'];
    $newStatus = $_GET['new_status'];
    
    foreach ($_SESSION['nitipdong_oop_list'] as &$itemData) {
        if ($itemData['id'] === $targetId) {
            // Bukti implementasi enkapsulasi: Ubah data melalui rekonstruksi objek dan setter
            $objTemp = Titipan::fromArray($itemData);
            $objTemp->setStatus($newStatus); // Memanggil Setter dengan validasi internal
            $itemData['status'] = $objTemp->getStatus(); // Memanggil Getter
            break;
        }
    }
    unset($itemData);
    header("Location: index.php?msg=status_updated");
    exit;
}

// Tambah pesanan titipan baru melalui formulir input (Object Instantiation via Constructor)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $namaBarang  = trim($_POST['nama_barang'] ?? '');
    $pengirim    = trim($_POST['pengirim'] ?? '');
    $lokasiBeli  = trim($_POST['lokasi_beli'] ?? '');
    $hargaBarang = (int)($_POST['harga_barang'] ?? 0);
    $biayaTitip  = (int)($_POST['biaya_titip'] ?? 3000);
    $jumlah      = max(1, (int)($_POST['jumlah'] ?? 1));
    $kategori    = $_POST['kategori'] ?? 'Makanan Berat';
    $catatan     = trim($_POST['catatan'] ?? '-');
    $avatarList  = ['🍱', '🍜', '☕', '🍗', '🥪', '🥟', '🥤', '🍔', '🥗', '🍩'];
    $randomAvatar= $avatarList[array_rand($avatarList)];

    if (!empty($namaBarang) && !empty($pengirim) && $hargaBarang > 0) {
        $newId = 'NTP-' . str_pad((string)(count($_SESSION['nitipdong_oop_list']) + 1), 3, '0', STR_PAD_LEFT);
        
        // --------------------------------------------------------------------
        // BUKTI OOP: Instansiasi Objek Baru menggunakan Constructor (__construct)
        // --------------------------------------------------------------------
        $titipanBaru = new Titipan(
            $newId,
            $namaBarang,
            $pengirim,
            $lokasiBeli,
            $hargaBarang,
            $biayaTitip,
            $jumlah,
            'Menunggu Dibeli',
            $catatan,
            $kategori,
            $randomAvatar,
            date('H:i') . ' WIB'
        );

        // Simpan objek ke array session via toArray()
        $_SESSION['nitipdong_oop_list'][] = $titipanBaru->toArray();
        header("Location: index.php?msg=created_success");
        exit;
    } else {
        $flashMessage = "Mohon lengkapi nama barang, pengirim, dan harga yang valid!";
        $flashType = 'danger';
    }
}

// Pesan notifikasi dari parameter query
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'created_success') {
        $flashMessage = "Berhasil membuat Objek Titipan baru via Constructor (__construct)!";
        $flashType = 'success';
    } elseif ($_GET['msg'] === 'status_updated') {
        $flashMessage = "Status objek berhasil diperbarui secara aman menggunakan Setter Method!";
        $flashType = 'success';
    } elseif ($_GET['msg'] === 'reset_success') {
        $flashMessage = "Data titipan berhasil di-reset ke daftar bawaan awal.";
        $flashType = 'info';
    }
}

// ============================================================================
// 4. INSTANSIASI KOLEKSI OBJEK DARI DATA
// Di tahap ini, seluruh elemen data diubah menjadi Objek Asli (Instance of Titipan)
// ============================================================================
/** @var Titipan[] $daftarObjekTitipan */
$daftarObjekTitipan = [];

foreach ($_SESSION['nitipdong_oop_list'] as $data) {
    // Membangun Objek nyata dari Class Titipan melalui Constructor
    $daftarObjekTitipan[] = new Titipan(
        $data['id'],
        $data['namaBarang'],
        $data['pengirim'],
        $data['lokasiBeli'],
        (int)$data['hargaBarang'],
        (int)$data['biayaTitip'],
        (int)$data['jumlah'],
        $data['status'],
        $data['catatan'],
        $data['kategori'],
        $data['avatar'],
        $data['waktuPesan']
    );
}

// ============================================================================
// 5. KALKULASI STATISTIK RINGKASAN MENGGUNAKAN PEMANGGILAN METHOD OBJEK
// ============================================================================
$totalPesanan       = count($daftarObjekTitipan);
$totalEstimasiBiaya = 0;
$totalJasaTitip     = 0;
$totalBarangSelesai = 0;
$totalMenunggu      = 0;

foreach ($daftarObjekTitipan as $objTitipan) {
    // Memanggil method objek: hitungBiayaTotal() dan getTotalJasaTitip()
    $totalEstimasiBiaya += $objTitipan->hitungBiayaTotal();
    $totalJasaTitip     += $objTitipan->getTotalJasaTitip();

    if ($objTitipan->getStatus() === 'Selesai & Lunas') {
        $totalBarangSelesai++;
    } elseif ($objTitipan->getStatus() === 'Menunggu Dibeli') {
        $totalMenunggu++;
    }
}

$helperTitipan = new Titipan('TMP', 'Helper', 'System', 'None', 0);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NitipDong 🍱 - Modul Week 6: Pemrograman Berorientasi Objek (OOP PHP)</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-orange-500 selection:text-white">

    <!-- ====================================================================== -->
    <!-- NAVBAR HEADER KHAS NITIPDONG -->
    <!-- ====================================================================== -->
    <header class="bg-white/90 backdrop-blur-md border-b border-orange-100 sticky top-0 z-40 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-orange-500/20">
                    🍱
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">
                            Nitip<span class="text-orange-500">Dong</span>
                        </h1>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 border border-orange-200">
                            Week 6 • OOP PHP
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        Object-Oriented Programming: Class, Object, Encapsulation & Dynamic HTML Output
                    </p>
                </div>
            </div>

            <!-- BADGE PEMENUHAN TUGAS PRAKTIKUM -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 border border-orange-200 flex items-center gap-1">
                    <span>✓</span> Class & Object
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                    <span>✓</span> Encapsulation (Private)
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1">
                    <span>✓</span> Constructor (__construct)
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                    <span>✓</span> Method & Dynamic HTML
                </span>
            </div>
        </div>
    </header>

    <!-- NOTIFIKASI FLASH -->
    <?php if ($flashMessage): ?>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-4 w-full">
        <div class="p-4 rounded-2xl text-sm font-semibold flex items-center justify-between shadow-xs border <?= $flashType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($flashType === 'danger' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-blue-50 text-blue-800 border-blue-200') ?>">
            <div class="flex items-center gap-2.5">
                <span class="text-lg"><?= $flashType === 'success' ? '🎉' : ($flashType === 'danger' ? '⚠️' : 'ℹ️') ?></span>
                <span><?= htmlspecialchars($flashMessage); ?></span>
            </div>
            <a href="index.php" class="text-xs opacity-60 hover:opacity-100 font-bold underline">Tutup</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- ====================================================================== -->
    <!-- MAIN CONTENT -->
    <!-- ====================================================================== -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full space-y-8">

        <!-- HERO SECTION: ROOM TITIPAN & MATERI OOP HIGHLIGHT -->
        <section class="bg-gradient-to-br from-orange-500 via-amber-500 to-amber-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-orange-500/15 relative overflow-hidden">
            <!-- Background Decorative Pattern -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-12 top-6 text-7xl opacity-15 select-none pointer-events-none">📦</div>

            <div class="relative z-10 space-y-4 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold text-white border border-white/20">
                    <span>🏛️</span> Modul Praktikum: Pemrograman Berorientasi Objek (OOP)
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                    Kelola Titipan Sirkel Kampus dengan Arsitektur OOP PHP
                </h2>
                <p class="text-white/90 text-sm sm:text-base leading-relaxed">
                    Setiap pesanan titipan di bawah ini merupakan <strong>instance (objek)</strong> nyata yang dibentuk dari <strong>Class Titipan</strong> melalui <strong>Constructor</strong>. Seluruh properti data dilindungi secara <strong>enkapsulasi (private/protected)</strong> dan nilai-nilainya dipanggil secara <strong>dinamis ke HTML</strong> melalui method <code class="bg-white/25 px-1.5 py-0.5 rounded font-mono text-xs">hitungBiayaTotal()</code> dan <code class="bg-white/25 px-1.5 py-0.5 rounded font-mono text-xs">getDetailTitipan()</code>.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="#form-titipan" class="px-5 py-2.5 rounded-xl bg-white text-orange-600 hover:bg-orange-50 font-bold text-sm shadow-md transition-all inline-flex items-center gap-2">
                        <span>➕</span> Tambah Objek Titipan Baru
                    </a>
                    <a href="index.php?action=reset" onclick="return confirm('Kembalikan data titipan ke daftar demo bawaan?')" class="px-4 py-2.5 rounded-xl bg-black/20 hover:bg-black/30 text-white font-semibold text-xs border border-white/20 transition-all inline-flex items-center gap-1.5">
                        <span>🔄</span> Reset Data Demo
                    </a>
                </div>
            </div>
        </section>

        <!-- ================================================================== -->
        <!-- METRIK RINGKASAN: DIHITUNG DARI PEMANGGILAN METHOD OBJEK -->
        <!-- ================================================================== -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Card 1: Total Objek -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Objek Titipan</span>
                    <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">📦</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    <?= $totalPesanan; ?> <span class="text-sm font-semibold text-slate-400">item</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Dikelola dalam array of objects</p>
            </div>

            <!-- Card 2: Total Biaya Layanan (via hitungBiayaTotal) -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Estimasi Belanja</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">💰</span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600">
                    <?= $helperTitipan->formatRupiah($totalEstimasiBiaya); ?>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Akumulasi <code class="font-mono text-[10px]">hitungBiayaTotal()</code></p>
            </div>

            <!-- Card 3: Jasa Titip Terkumpul (via getTotalJasaTitip) -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Fee Jastip</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm">🎁</span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-purple-600">
                    <?= $helperTitipan->formatRupiah($totalJasaTitip); ?>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Hasil kalkulasi method class</p>
            </div>

            <!-- Card 4: Progres Selesai -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Selesai</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">✅</span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    <?= $totalBarangSelesai; ?> <span class="text-sm font-semibold text-slate-400">/ <?= $totalPesanan; ?></span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1"><?= $totalMenunggu; ?> pesanan menunggu dibeli</p>
            </div>
        </section>

        <!-- ================================================================== -->
        <!-- GRID DAFTAR TITIPAN SECARA DINAMIS (DYNAMIC HTML OUTPUT DARI OBJEK) -->
        <!-- ================================================================== -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span>🍱</span> Daftar Titipan Sirkel Aktif
                    </h3>
                    <p class="text-xs text-slate-500">
                        Setiap kartu di bawah ini dirender secara dinamis melalui pemanggilan method dari masing-masing <strong>Objek Titipan</strong>.
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-500">
                    Total: <span class="text-orange-600 font-bold"><?= count($daftarObjekTitipan); ?> Objek</span> di memori
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($daftarObjekTitipan as $index => $item): ?>
                    <?php
                        // ====================================================
                        // PEMANGGILAN METHOD DARI OBJEK TITIPAN (OOP METHOD INVOCATION)
                        // ====================================================
                        $detail = $item->getDetailTitipan(); // Method getDetailTitipan()
                        $biayaTotal = $item->hitungBiayaTotal(); // Method hitungBiayaTotal()
                    ?>
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-orange-300 transition-all flex flex-col justify-between overflow-hidden group">
                        
                        <!-- CARD HEADER -->
                        <div class="p-5 pb-3 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-2xl shadow-xs">
                                        <?= htmlspecialchars($item->getAvatar()); ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-slate-900 text-base leading-tight">
                                                <?= htmlspecialchars($item->getPengirim()); ?>
                                            </h4>
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                                <?= htmlspecialchars($item->getId()); ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                            <span>🕒</span> <?= htmlspecialchars($item->getWaktuPesan()); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- BADGE STATUS DARI METHOD getStatusBadgeClass() -->
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-xl border flex items-center gap-1 shrink-0 <?= $item->getStatusBadgeClass(); ?>">
                                    <span><?= $item->getStatusIcon(); ?></span>
                                    <span><?= htmlspecialchars($item->getStatus()); ?></span>
                                </span>
                            </div>

                            <!-- MENU & RESTO -->
                            <div class="bg-slate-50/80 rounded-2xl p-3.5 border border-slate-100 space-y-1.5">
                                <div class="text-xs font-semibold text-orange-600 flex items-center gap-1">
                                    <span>🏪</span> <?= htmlspecialchars($item->getLokasiBeli()); ?>
                                </div>
                                <h5 class="font-extrabold text-slate-800 text-sm leading-snug">
                                    <?= htmlspecialchars($item->getNamaBarang()); ?>
                                </h5>
                                <div class="text-xs text-slate-500 flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span>Kuantitas: <strong class="text-slate-700"><?= $item->getJumlah(); ?>x</strong></span>
                                    <span>Kategori: <strong class="text-slate-700"><?= htmlspecialchars($item->getKategori()); ?></strong></span>
                                </div>
                            </div>

                            <!-- CATATAN KHUSUS -->
                            <div class="text-xs text-slate-600 bg-amber-50/50 border border-amber-100 rounded-xl p-2.5 flex items-start gap-2">
                                <span class="text-amber-500 shrink-0">📝</span>
                                <span class="italic leading-relaxed"><?= htmlspecialchars($item->getCatatan()); ?></span>
                            </div>
                        </div>

                        <!-- RINCIAN BIAYA DARI METHOD hitungBiayaTotal() & getDetailTitipan() -->
                        <div class="bg-slate-50/50 p-5 pt-3 border-t border-slate-100 space-y-3">
                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Harga Barang (<?= $item->getJumlah(); ?>x)</span>
                                    <span class="font-semibold text-slate-700"><?= $detail['subtotalBarangFmt']; ?></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Fee Jastip</span>
                                    <span class="font-semibold text-purple-600"><?= $detail['totalJasaTitipFmt']; ?></span>
                                </div>
                                <div class="flex items-center justify-between text-sm font-extrabold text-slate-900 pt-1.5 border-t border-dashed border-slate-200">
                                    <span class="flex items-center gap-1">
                                        <span>Total Tagihan</span>
                                        <span class="text-[9px] font-mono px-1 rounded bg-orange-100 text-orange-700">Method</span>
                                    </span>
                                    <span class="text-orange-600 text-base"><?= $item->formatRupiah($biayaTotal); ?></span>
                                </div>
                            </div>

                            <!-- CONTROLLER / STATUS SWITCHER (MENUNJUKKAN MUTASI VIA SETTER METHOD) -->
                            <div class="pt-2 border-t border-slate-200/70">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                    Ubah Status (Setter Method):
                                </p>
                                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px]">
                                    <a href="index.php?action=update_status&id=<?= urlencode($item->getId()); ?>&new_status=<?= urlencode('Sedang Dibelikan'); ?>"
                                       class="px-2 py-1 rounded-lg border text-xs font-semibold transition-all <?= $item->getStatus() === 'Sedang Dibelikan' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200' ?>">
                                        🛵 Belikan
                                    </a>
                                    <a href="index.php?action=update_status&id=<?= urlencode($item->getId()); ?>&new_status=<?= urlencode('Siap Diambil'); ?>"
                                       class="px-2 py-1 rounded-lg border text-xs font-semibold transition-all <?= $item->getStatus() === 'Siap Diambil' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200' ?>">
                                        🛍️ Siap
                                    </a>
                                    <a href="index.php?action=update_status&id=<?= urlencode($item->getId()); ?>&new_status=<?= urlencode('Selesai & Lunas'); ?>"
                                       class="px-2 py-1 rounded-lg border text-xs font-semibold transition-all <?= $item->getStatus() === 'Selesai & Lunas' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200' ?>">
                                        ✅ Lunas
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ================================================================== -->
        <!-- TABEL RINCIAN OUTPUT METHOD getDetailTitipan() -->
        <!-- ================================================================== -->
        <section class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden space-y-4 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📋</span> Rincian Rekapitulasi Titipan (Data Method <code class="text-xs bg-slate-100 text-orange-600 px-1.5 py-0.5 rounded font-mono">getDetailTitipan()</code>)
                    </h3>
                    <p class="text-xs text-slate-500">
                        Output array asosiatif yang dikembalikan oleh method objek <code class="font-mono text-orange-600">getDetailTitipan()</code> dirender secara dinamis ke dalam tabel.
                    </p>
                </div>
                <div class="text-xs font-medium text-slate-400">
                    Live Server-Side Table
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="py-3 px-3">ID & Penitip</th>
                            <th class="py-3 px-3">Barang & Resto</th>
                            <th class="py-3 px-3 text-center">Qty</th>
                            <th class="py-3 px-3 text-right">Harga Pokok</th>
                            <th class="py-3 px-3 text-right">Fee Jastip</th>
                            <th class="py-3 px-3 text-right">Total Tagihan</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($daftarObjekTitipan as $obj): ?>
                            <?php $info = $obj->getDetailTitipan(); ?>
                            <tr class="hover:bg-orange-50/40 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                        <span><?= $info['avatar']; ?></span>
                                        <span><?= htmlspecialchars($info['pengirim']); ?></span>
                                    </div>
                                    <span class="text-[11px] font-mono text-slate-400"><?= $info['id']; ?> • <?= $info['waktuPesan']; ?></span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($info['namaBarang']); ?></div>
                                    <div class="text-xs text-orange-600 font-medium">📍 <?= htmlspecialchars($info['lokasiBeli']); ?></div>
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-slate-700">
                                    <?= $info['jumlah']; ?>
                                </td>
                                <td class="py-3.5 px-3 text-right font-medium text-slate-600">
                                    <?= $info['subtotalBarangFmt']; ?>
                                </td>
                                <td class="py-3.5 px-3 text-right font-medium text-purple-600">
                                    <?= $info['totalJasaTitipFmt']; ?>
                                </td>
                                <td class="py-3.5 px-3 text-right font-extrabold text-orange-600">
                                    <?= $info['biayaTotalFmt']; ?>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-lg border <?= $info['statusBadgeClass']; ?>">
                                        <span><?= $info['statusIcon']; ?></span>
                                        <span><?= htmlspecialchars($info['status']); ?></span>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/90 font-extrabold text-slate-900 border-t-2 border-slate-200">
                            <td colspan="5" class="py-3.5 px-3 text-right text-xs uppercase tracking-wider text-slate-600">
                                Total Keseluruhan Tagihan Titipan Sirkel:
                            </td>
                            <td class="py-3.5 px-3 text-right text-base text-orange-600">
                                <?= $helperTitipan->formatRupiah($totalEstimasiBiaya); ?>
                            </td>
                            <td class="py-3.5 px-3 text-center text-xs text-slate-400">
                                <?= $totalPesanan; ?> Pesanan
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- ================================================================== -->
        <!-- FORM INTERAKTIF: INSTANSIASI OBJEK VIA CONSTRUCTOR -->
        <!-- ================================================================== -->
        <section id="form-titipan" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold mb-2">
                    <span>⚡</span> Form Tambah Objek Titipan Baru
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">
                    Instansiasi Objek Baru Melalui Constructor (__construct)
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Form ini mengirimkan data melalui method <code class="bg-slate-100 px-1 py-0.5 rounded font-mono text-slate-700">POST</code>, kemudian dieksekusi secara instan menggunakan perintah <code class="bg-slate-100 px-1 py-0.5 rounded font-mono text-orange-600">$titipanBaru = new Titipan(...)</code>.
                </p>
            </div>

            <form action="index.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="tambah">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Nama Penitip -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Penitip (Teman Sirkel) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pengirim" required placeholder="Contoh: Dimas, Nadia, atau Rian"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                    </div>

                    <!-- Nama Barang / Menu -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Barang / Menu Makanan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" required placeholder="Contoh: Nasi Goreng Spesial Telur"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                    </div>

                    <!-- Lokasi Beli / Resto -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Lokasi / Resto / Kantin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lokasi_beli" required placeholder="Contoh: Kantin Teknik / Warung Bu Joko"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                    </div>

                    <!-- Harga Pokok Barang -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Harga Pokok Barang (IDR) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="harga_barang" required min="1000" step="500" placeholder="20000"
                                   class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                        </div>
                    </div>

                    <!-- Biaya Jasa Titip (Jastip Fee) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Biaya Jasa Titip (Jastip)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="biaya_titip" value="3000" min="0" step="500"
                                   class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                        </div>
                    </div>

                    <!-- Kuantitas / Jumlah -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jumlah (Qty)
                        </label>
                        <input type="number" name="jumlah" value="1" min="1" max="50"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Titipan</label>
                        <select name="kategori" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                            <option value="Makanan Berat">🍛 Makanan Berat</option>
                            <option value="Minuman Segar">🥤 Minuman Segar</option>
                            <option value="Cemilan & Dimsum">🥟 Cemilan & Snack</option>
                            <option value="ATK / Kebutuhan Kuliah">📚 ATK & Fotokopi</option>
                            <option value="Obat / Farmasi">💊 Obat & Vitamin</option>
                        </select>
                    </div>

                    <!-- Catatan Khusus -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Khusus (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Contoh: Jangan terlalu pedas, kerupuk dipisah"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm outline-hidden transition-all bg-slate-50/50 focus:bg-white">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="reset" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                        Reset Input
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition-all flex items-center gap-2">
                        <span>✨</span> Buat Objek Titipan Baru (new Titipan)
                    </button>
                </div>
            </form>
        </section>

        <!-- ================================================================== -->
        <!-- KOTAK MATERI PEMBELAJARAN OOP PHP (EDUCATIONAL CARD) -->
        <!-- ================================================================== -->
        <section class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-xl">
                    📚
                </div>
                <div>
                    <h3 class="text-xl font-bold tracking-tight">
                        Dokumentasi & Bedah Konsep OOP PHP (Week 6)
                    </h3>
                    <p class="text-xs text-slate-400">
                        Penjelasan konsep OOP yang diimplementasikan pada proyek NitipDong
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Konsep 1 & 2 -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <div class="font-bold text-orange-400 flex items-center gap-2 text-sm">
                        <span>1.</span> Class & Object
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        <strong>Class</strong> adalah cetak biru (blueprint) bernama <code class="font-mono text-orange-300">Titipan</code> di berkas <code class="font-mono text-slate-300">Titipan.php</code>. Sedangkan <strong>Object</strong> adalah wujud nyata hasil instansiasi dari class tersebut (misalnya: <code class="font-mono text-orange-300">$titipan1 = new Titipan(...)</code>).
                    </p>
                </div>

                <!-- Konsep 3 & 4 -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <div class="font-bold text-orange-400 flex items-center gap-2 text-sm">
                        <span>2.</span> Property & Encapsulation
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Data seperti <code class="font-mono text-orange-300">$namaBarang</code>, <code class="font-mono text-orange-300">$pengirim</code>, <code class="font-mono text-orange-300">$biayaTitip</code>, dan <code class="font-mono text-orange-300">$status</code> dilindungi dengan visibilitas <code class="font-mono text-amber-300">private</code>. Akses baca dan tulis hanya boleh melalui Getter & Setter Method.
                    </p>
                </div>

                <!-- Konsep 5 -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <div class="font-bold text-orange-400 flex items-center gap-2 text-sm">
                        <span>3.</span> Constructor (__construct)
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Method <code class="font-mono text-orange-300">__construct(...)</code> otomatis dieksekusi begitu objek dibentuk melalui kata kunci <code class="font-mono text-amber-300">new</code>. Berfungsi menginisialisasi nilai awal properti dan memastikan data awal valid sejak objek lahir.
                    </p>
                </div>

                <!-- Konsep 6 & 7 -->
                <div class="bg-white/5 rounded-2xl p-4 border border-white/10 space-y-2">
                    <div class="font-bold text-orange-400 flex items-center gap-2 text-sm">
                        <span>4.</span> Methods & Dynamic HTML Output
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Method <code class="font-mono text-orange-300">hitungBiayaTotal()</code> dan <code class="font-mono text-orange-300">getDetailTitipan()</code> mengolah logika bisnis aplikasi. Nilai return-nya disisipkan langsung ke tag HTML (<code class="font-mono text-amber-300">&lt;?= $item->hitungBiayaTotal(); ?&gt;</code>) sehingga antarmuka NitipDong dirender dinamis di sisi server.
                    </p>
                </div>
            </div>
        </section>

    </main>

    <!-- ====================================================================== -->
    <!-- FOOTER -->
    <!-- ====================================================================== -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="text-lg">🍱</span>
                <span class="font-extrabold text-slate-900">NitipDong</span>
                <span>• Solusi Titip Makanan & Jastip Tanpa Drama</span>
            </div>
            <div class="flex items-center gap-4 font-semibold">
                <span>Modul Week 6: OOP PHP</span>
                <span>•</span>
                <a href="../week5%20-%20PHP/index.php" class="text-slate-600 hover:text-orange-600 transition-colors">Week 5 (PHP Prosedural)</a>
                <span>•</span>
                <a href="../week7/index.php" class="text-slate-600 hover:text-orange-600 transition-colors">Week 7 (MVC Architecture)</a>
            </div>
        </div>
    </footer>

</body>
</html>

