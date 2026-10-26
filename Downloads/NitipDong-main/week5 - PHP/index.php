<?php
/**
 * ============================================================================
 * NITIPDONG 🍱 - PORTAL REKOMENDASI KULINER & MENU PRIORITAS SIRKEL
 * Implementasi PHP Prosedural (Server-Side Rendering / SSR)
 * (Disesuaikan 100% dengan Tema Proyek NitipDong & Memenuhi Task Praktikum)
 * ============================================================================
 */

// ============================================================================
// TASK 01: CREATE PHP PAGE & INISIALISASI VARIABEL PHP
// ============================================================================
$namaPresiden      = "Budi Pratama (Presiden Sirkel / Host NitipDong)";
$periode           = "Makan Siang (11:30 - 13:00 WIB)";
$statusProgram     = "Room Aktif & Menerima Pesanan";
$programPrioritas  = "Paket Kuliner Hemat & Menu Terlaris Sirkel";

// Variabel Pendukung Identitas NitipDong
$namaPlatform      = "NitipDong";
$tagline           = "Gak Pusing Lagi Ngurusin Titipan Makanan Temen!";
$merchantUtama     = "Pujasera & Resto Sekitar Kampus";

// ============================================================================
// TASK 02: PROCESS DATA WITH PHP
// 1. Array Multidimensi Menu Kuliner NitipDong
// 2. Conditional Status / Badge Logic
// 3. Loop Penomoran Dinamis
// ============================================================================

$daftarProgram = [
    [
        "id"         => 1,
        "judul"      => "Paket Ayam Geprek Sambal Matah",
        "resto"      => "Ayam Geprek Bensu",
        "kategori"   => "Makanan Berat",
        "target"     => "Estimasi 15 Menit",
        "anggaran"   => 28000,
        "rating"     => 4.8,
        "status"     => "Prioritas Utama",
        "deskripsi"  => "Ayam krispi gurih dengan ulekan cabai rawit dan irisan bawang segar khas Bali. Menu wajib makan siang sirkel.",
        "icon"       => "🍗"
    ],
    [
        "id"         => 2,
        "judul"      => "Bebek Goreng Bumbu Hitam Madura",
        "resto"      => "Bebek Sinjay & Rempah Madura",
        "kategori"   => "Makanan Berat",
        "target"     => "Estimasi 20 Menit",
        "anggaran"   => 38000,
        "rating"     => 4.9,
        "status"     => "Siap Dipesan",
        "deskripsi"  => "Bebek ungkep empuk berpadu bumbu rempah hitam pekat gurih nan pedas nikmat disajikan dengan serundeng gurih.",
        "icon"       => "🦆"
    ],
    [
        "id"         => 3,
        "judul"      => "Dimsum Mentai Mozzarella (4 pcs)",
        "resto"      => "DIMSUM TITI Premium",
        "kategori"   => "Cemilan & Snack",
        "target"     => "Estimasi 12 Menit",
        "anggaran"   => 26000,
        "rating"     => 4.7,
        "status"     => "Promo Diskon",
        "deskripsi"  => "Dimsum ayam udang lembut disiram saus mentai bakar dan keju mozzarella leleh. Camilan favorit saat nugas bareng.",
        "icon"       => "🥟"
    ],
    [
        "id"         => 4,
        "judul"      => "Tahu Telor Bumbu Kacang Petis",
        "resto"      => "Tahu Telor Budhe Yuni",
        "kategori"   => "Makanan Berat",
        "target"     => "Estimasi 10 Menit",
        "anggaran"   => 20000,
        "rating"     => 4.9,
        "status"     => "Prioritas Utama",
        "deskripsi"  => "Dadar tahu telur renyah disiram petis gurih kental dengan taburan tauge dan bawang goreng. Porsi jumbo ramah kantong.",
        "icon"       => "🍳"
    ],
    [
        "id"         => 5,
        "judul"      => "Kopi Susu Gula Aren Mantan (Large)",
        "resto"      => "Kopi Kenangan",
        "kategori"   => "Minuman Segar",
        "target"     => "Estimasi 8 Menit",
        "anggaran"   => 22000,
        "rating"     => 4.8,
        "status"     => "Siap Dipesan",
        "deskripsi"  => "Espresso blend spesial dipadu susu segar creamy dan sirup gula aren murni. Mood booster ampuh anti ngantuk di kelas.",
        "icon"       => "☕"
    ],
    [
        "id"         => 6,
        "judul"      => "Croffle Salted Caramel Crunchy",
        "resto"      => "Sweet Bites & Toast Bar",
        "kategori"   => "Cemilan & Snack",
        "target"     => "Estimasi 10 Menit",
        "anggaran"   => 24000,
        "rating"     => 4.7,
        "status"     => "Menu Favorit",
        "deskripsi"  => "Croissant waffle karamelisasi renyah dengan lelehan saus karamel asin dan taburan remah biskuit renyah.",
        "icon"       => "🥐"
    ]
];

// Helper: Format Mata Uang Rupiah
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Helper: Conditional Status / Badge Logic (Task 02)
function getStatusBadgeClass($status) {
    switch ($status) {
        case "Prioritas Utama":
            return "bg-orange-100 text-orange-700 border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-900";
        case "Promo Diskon":
            return "bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900";
        case "Siap Dipesan":
            return "bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900";
        case "Menu Favorit":
        default:
            return "bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900";
    }
}

// Perhitungan Ringkasan Server-Side
$totalMenu = count($daftarProgram);
$rataRataHarga = 0;
$totalEstimasi = 0;
foreach ($daftarProgram as $item) {
    $totalEstimasi += $item['anggaran'];
}
$rataRataHarga = $totalMenu > 0 ? round($totalEstimasi / $totalMenu) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($namaPlatform); ?> - Portal Titipan & Rekomendasi Menu (PHP SSR)</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-orange-500 selection:text-white">

    <!-- NAVBAR HEADER KHAS NITIPDONG -->
    <header class="bg-white border-b border-orange-100 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-orange-500/20">
                    🍱
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">
                        Nitip<span class="text-orange-500">Dong</span>
                    </h1>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
                        Server-Side Rendering (SSR) • PHP Prosedural
                    </p>
                </div>
            </div>

            <!-- TASK STATUS BADGES (VALIDASI DOSEN) -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 border border-orange-200">
                    ✓ Task 01: Variabel PHP
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                    ✓ Task 02: Array & Conditional
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    ✓ Task 03: SSR Dynamic Grid
                </span>
            </div>
        </div>
    </header>

    <!-- MAIN BODY -->
    <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 flex-1 space-y-8">

        <!-- ==================================================================== -->
        <!-- TASK 01: MENAMPILKAN VARIABEL PHP SESUAI TEMA NITIPDONG -->
        <!-- ==================================================================== -->
        <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-zinc-900 to-slate-950 text-white p-6 sm:p-10 shadow-xl relative overflow-hidden border border-slate-800">
            <!-- Ambient Glow Oranye -->
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full blur-3xl opacity-20 bg-orange-500 pointer-events-none"></div>

            <div class="relative z-10 space-y-4 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                    <span>🔥</span>
                    <span>Sesi Titipan: <?php echo htmlspecialchars($periode); ?></span>
                </div>

                <div>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        <?php echo htmlspecialchars($namaPresiden); ?>
                    </h2>
                    <p class="text-orange-400 text-xs sm:text-sm font-semibold mt-1">
                        Tagline: "<?php echo htmlspecialchars($tagline); ?>"
                    </p>
                </div>

                <p class="text-slate-300 text-sm leading-relaxed">
                    Fokus Menu: <strong class="text-white"><?php echo htmlspecialchars($programPrioritas); ?></strong>. 
                    Seluruh katalog kartu titipan di bawah ini diproses dan dirender langsung di sisi server menggunakan bahasa <strong>PHP Prosedural</strong>.
                </p>

                <!-- Status Banner & Metrik PHP -->
                <div class="pt-2 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-white/10 rounded-2xl p-3 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Status Room</span>
                        <span class="text-emerald-400 font-extrabold text-xs sm:text-sm truncate block"><?php echo htmlspecialchars($statusProgram); ?></span>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-3 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Menu Tersedia</span>
                        <span class="text-white font-extrabold text-sm"><?php echo $totalMenu; ?> Pilihan</span>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-3 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Rata-Rata Harga</span>
                        <span class="text-amber-300 font-extrabold text-sm"><?php echo formatRupiah($rataRataHarga); ?></span>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-3 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Merchant Wilayah</span>
                        <span class="text-orange-300 font-extrabold text-xs truncate block"><?php echo htmlspecialchars($merchantUtama); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================== -->
        <!-- TASK 03: SSR DYNAMIC GRID CARDS PROGRAM PRIORITAS NITIPDONG -->
        <!-- Dilengkapi Loop Penomoran & Conditional Badge (Task 02) -->
        <!-- ==================================================================== -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Daftar Menu Prioritas Sirkel</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-orange-100 text-orange-600">
                            <?php echo $totalMenu; ?> Menu
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">
                        Hasil eksekusi perulangan <code>foreach</code> array multidimensi di sisi server PHP (SSR)
                    </p>
                </div>

                <div class="text-xs text-slate-500 font-semibold bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    📍 Mitra Resto Terpilih • Bebas Ongkir Bareng
                </div>
            </div>

            <!-- GRID CARDS SSR -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                // TASK 02: LOOP PENOMORAN MENGGUNAKAN FOREACH
                $nomorUrut = 1;
                foreach ($daftarProgram as $program): 
                    // TASK 02: CONDITIONAL BADGE DARI FUNGSI getStatusBadgeClass
                    $badgeClass = getStatusBadgeClass($program['status']);
                ?>
                    <article class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs hover:shadow-xl hover:-translate-y-1 transition duration-200 flex flex-col justify-between">
                        <div>
                            <!-- Header Kartu -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 flex items-center justify-center text-2xl shadow-inner shrink-0">
                                    <?php echo $program['icon']; ?>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700">
                                        #<?php echo str_pad($nomorUrut, 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <p class="text-[11px] text-slate-400 font-bold mt-1">
                                        <?php echo htmlspecialchars($program['kategori']); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Judul Menu & Restoran -->
                            <h4 class="font-bold text-slate-900 text-base leading-snug hover:text-orange-500 transition mb-1">
                                <?php echo htmlspecialchars($program['judul']); ?>
                            </h4>

                            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-3">
                                <span>📍 <?php echo htmlspecialchars($program['resto']); ?></span>
                                <span>•</span>
                                <span class="text-amber-500 font-bold">★ <?php echo $program['rating']; ?></span>
                            </div>

                            <!-- Deskripsi -->
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                <?php echo htmlspecialchars($program['deskripsi']); ?>
                            </p>
                        </div>

                        <!-- Footer Kartu: Estimasi Waktu, Harga & Status Badge -->
                        <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Estimasi Siap:</span>
                                <span class="font-bold text-slate-700"><?php echo htmlspecialchars($program['target']); ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Harga Titipan:</span>
                                <span class="font-extrabold text-orange-600 text-sm"><?php echo formatRupiah($program['anggaran']); ?></span>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-slate-400 font-medium">Kategori Status:</span>
                                <!-- TASK 02: CONDITIONAL BADGE -->
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($program['status']); ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php 
                    $nomorUrut++;
                endforeach; 
                ?>
            </div>
        </section>

    </main>

    <!-- FOOTER RESMI NITIPDONG -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 space-y-1">
            <p class="font-bold text-slate-700">
                Nitip<span class="text-orange-500">Dong</span> © 2026 • Platform Titip Beli & Split Bill Sirkel
            </p>
            <p class="text-[11px] text-slate-400">
                Implementasi Server-Side Rendering (SSR) Menggunakan PHP Prosedural & Tailwind CSS
            </p>
        </div>
    </footer>

</body>
</html>
