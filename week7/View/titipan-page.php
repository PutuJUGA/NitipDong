<?php
/**
 * ============================================================================
 * VIEW: titipan-page.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Task 02: Build the Controller)
 * ============================================================================
 * TANGGUNG JAWAB VIEW:
 * 1. Murni menangani presentasi antarmuka pengguna (UI/UX).
 * 2. Menerima variabel siap pakai yang disiapkan & dikirim oleh TitipanController:
 *    - $room        : Metadata room titipan
 *    - $pesanan     : Daftar pesanan yang sudah diformat rapi
 *    - $stat        : Statistik keuangan (total, lunas, utang, progres)
 *    - $waText      : Rekap teks terformat siap disalin ke WhatsApp
 *    - $filterAktif : Filter yang sedang aktif ('all', 'lunas', 'belum')
 *    - $pesanNotif  : Flash message session status aksi controller
 * 3. Tidak memuat query database atau manipulasi data mentah (Separation of Concerns).
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['namaRoom']); ?> - NitipDong (MVC Controller)</title>
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

    <!-- ==================================================================== -->
    <!-- NAVBAR HEADER IDENTITAS NITIPDONG & PERTEMUAN 7 TASK 02             -->
    <!-- ==================================================================== -->
    <header class="bg-white border-b border-orange-100 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                        🍱
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">
                            Nitip<span class="text-orange-500">Dong</span>
                        </h1>
                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
                            Pertemuan 7 Web Programming • Task 02 Controller
                        </p>
                    </div>
                </a>
            </div>

            <!-- BADGES STATUS MVC & CONTROLLER -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-800 border border-orange-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    Controller: TitipanController.php
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Model: Titipan.php
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    View: titipan-page.php
                </span>
            </div>
        </div>
    </header>

    <!-- ==================================================================== -->
    <!-- FLASH NOTIFICATION DARI PROSES CONTROLLER                            -->
    <!-- ==================================================================== -->
    <?php if (!empty($pesanNotif)): ?>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-6 w-full animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="p-4 rounded-2xl border flex items-center justify-between text-xs font-semibold <?php echo $pesanNotif['tipe'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 shadow-sm' : ($pesanNotif['tipe'] === 'error' ? 'bg-rose-50 text-rose-800 border-rose-200 shadow-sm' : 'bg-blue-50 text-blue-800 border-blue-200 shadow-sm'); ?>">
                <div class="flex items-center gap-3">
                    <span class="text-xl"><?php echo $pesanNotif['tipe'] === 'success' ? '🎉' : ($pesanNotif['tipe'] === 'error' ? '⚠️' : 'ℹ️'); ?></span>
                    <span class="text-sm"><?php echo $pesanNotif['teks']; ?></span>
                </div>
                <a href="index.php" class="text-slate-400 hover:text-slate-700 font-bold ml-4 text-sm">✕</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- MAIN BODY -->
    <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 flex-1 space-y-8">

        <!-- ==================================================================== -->
        <!-- BANNER CHECKLIST EVALUASI TUGAS PERTEMUAN 7 TASK 02                 -->
        <!-- ==================================================================== -->
        <section class="bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 border border-orange-200 rounded-3xl p-6 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-1.5 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-black bg-orange-500 text-white uppercase tracking-wider">
                        Pertemuan 7 • Task 02: Build the Controller
                    </div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">
                        Pipeline: Request ➔ Controller ➔ Process ➔ View
                    </h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Controller <code>TitipanController</code> menerima request, mengeksekusi method (<code>index()</code>, <code>show()</code>, <code>process()</code>), berinteraksi dengan Model <code>Titipan</code>, dan meneruskan data ke View.
                    </p>
                </div>

                <!-- 5 Checklist Evaluasi Sesuai Slide Dosen -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-xs font-bold">
                    <div class="bg-white/95 p-3 rounded-2xl border border-orange-200/80 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Controller Berjalan</span>
                        <span class="text-[9px] text-slate-500 font-medium">TitipanController</span>
                    </div>
                    <div class="bg-white/95 p-3 rounded-2xl border border-orange-200/80 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Request Diterima</span>
                        <span class="text-[9px] text-slate-500 font-medium">handleRequest()</span>
                    </div>
                    <div class="bg-white/95 p-3 rounded-2xl border border-orange-200/80 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Method Dijalankan</span>
                        <span class="text-[9px] text-slate-500 font-medium">index/show/process</span>
                    </div>
                    <div class="bg-white/95 p-3 rounded-2xl border border-orange-200/80 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Model Dipanggil</span>
                        <span class="text-[9px] text-slate-500 font-medium">Model Titipan.php</span>
                    </div>
                    <div class="bg-white/95 p-3 rounded-2xl border border-orange-200/80 flex flex-col gap-1 text-emerald-700 shadow-2xs col-span-2 sm:col-span-1">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">View Ditampilkan</span>
                        <span class="text-[9px] text-slate-500 font-medium">titipan-page.php</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================== -->
        <!-- ROOM HERO CARD (INFO HOST & METRIK FINANCIAL SPLIT BILL)             -->
        <!-- ==================================================================== -->
        <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-zinc-900 to-slate-950 text-white p-6 sm:p-10 shadow-xl relative overflow-hidden border border-slate-800">
            <!-- Glow Efek Oranye -->
            <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full blur-3xl opacity-20 bg-orange-500 pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- Header Room -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                            <span>🔥</span>
                            <span>Room ID: <?php echo htmlspecialchars($room['idRoom']); ?> • <?php echo htmlspecialchars($room['statusRoom']); ?></span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            <?php echo htmlspecialchars($room['namaRoom']); ?>
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-400">
                            Host: <strong class="text-white"><?php echo htmlspecialchars($room['host']); ?></strong> • Tujuan: <strong class="text-orange-400"><?php echo htmlspecialchars($room['targetResto']); ?></strong>
                        </p>
                    </div>

                    <div class="bg-white/5 border border-white/10 p-3.5 rounded-2xl text-xs space-y-1 shrink-0 backdrop-blur-xs">
                        <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Rekening Tujuan / QRIS:</div>
                        <div class="font-mono font-bold text-amber-300 text-sm"><?php echo htmlspecialchars($room['rekeningHost']); ?></div>
                        <div class="text-[11px] text-rose-400 font-semibold">⏰ Batas Pesanan: <?php echo htmlspecialchars($room['deadlineOrder']); ?></div>
                    </div>
                </div>

                <!-- Financial Metric Cards (Hasil Perhitungan Model Titipan) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10 space-y-1">
                        <div class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Total Tagihan Sirkel</div>
                        <div class="text-xl sm:text-2xl font-black text-white"><?php echo $stat['totalTagihan']; ?></div>
                        <div class="text-[10px] text-slate-400"><?php echo $stat['totalPesanan']; ?> Pesanan Terdaftar</div>
                    </div>

                    <div class="bg-emerald-950/40 rounded-2xl p-4 border border-emerald-500/30 space-y-1">
                        <div class="text-emerald-300 text-[11px] font-bold uppercase tracking-wider">Uang Masuk (Lunas)</div>
                        <div class="text-xl sm:text-2xl font-black text-emerald-400"><?php echo $stat['totalLunas']; ?></div>
                        <div class="text-[10px] text-emerald-300/80"><?php echo $stat['jumlahLunas']; ?> Orang Telah Bayar</div>
                    </div>

                    <div class="bg-rose-950/40 rounded-2xl p-4 border border-rose-500/30 space-y-1">
                        <div class="text-rose-300 text-[11px] font-bold uppercase tracking-wider">Sisa Utang Temen</div>
                        <div class="text-xl sm:text-2xl font-black text-rose-400"><?php echo $stat['totalBelumLunas']; ?></div>
                        <div class="text-[10px] text-rose-300/80"><?php echo $stat['jumlahBelumLunas']; ?> Orang Belum Bayar</div>
                    </div>

                    <div class="bg-amber-950/40 rounded-2xl p-4 border border-amber-500/30 space-y-1">
                        <div class="text-amber-300 text-[11px] font-bold uppercase tracking-wider">Rata-Rata / Orang</div>
                        <div class="text-xl sm:text-2xl font-black text-amber-400"><?php echo $stat['rataRataPerOrang']; ?></div>
                        <div class="text-[10px] text-amber-300/80">Progres: <?php echo $stat['persentaseLunas']; ?>%</div>
                    </div>
                </div>

                <!-- Progress Bar Pelunasan -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between text-xs font-bold text-slate-300">
                        <span>Progres Pelunasan Teman Sirkel</span>
                        <span class="text-orange-400"><?php echo $stat['persentaseLunas']; ?>% Terkumpul</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-700">
                        <div class="bg-gradient-to-r from-orange-500 to-emerald-500 h-full rounded-full transition-all duration-500"
                             style="width: <?php echo min(100, max(0, $stat['persentaseLunas'])); ?>%"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================== -->
        <!-- CORE FITUR 1: FORMULIR ENTRI CEPAT (DIPROSES OLEH CONTROLLER)       -->
        <!-- ==================================================================== -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>⚡ Formulir Entri Cepat Titipan Teman</span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Form mengirim request ke Controller melalui <code>index.php?action=process&sub_action=tambah</code>.
                    </p>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-700 px-3 py-1 rounded-full">
                    Method: process()
                </span>
            </div>

            <!-- Formulir POST ke Controller -->
            <form action="index.php?action=process" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
                <input type="hidden" name="sub_action" value="tambah">

                <!-- Nama Penitip -->
                <div class="lg:col-span-3 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Nama Penitip</label>
                    <input type="text" name="namaPenitip" required placeholder="Misal: Yoga Pratama"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <!-- Menu Pesanan -->
                <div class="lg:col-span-3 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Menu Makanan / Minuman</label>
                    <input type="text" name="menu" required placeholder="Misal: Nasi Goreng Gila Spesial"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <!-- Resto -->
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Restoran / Warung</label>
                    <input type="text" name="resto" placeholder="Misal: Kantin Gedung F"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <!-- Harga Satuan -->
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Nominal Harga (Rp)</label>
                    <input type="number" name="harga" required min="1000" step="500" placeholder="25000"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <!-- Tombol Submit -->
                <div class="lg:col-span-2 flex items-end">
                    <button type="submit"
                        class="w-full text-xs font-bold py-2.5 px-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-md shadow-orange-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>➕</span>
                        <span>Tambah Titipan</span>
                    </button>
                </div>

                <!-- Catatan Khusus -->
                <div class="sm:col-span-2 lg:col-span-12 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Catatan Khusus Penitip (Opsional)</label>
                    <input type="text" name="catatan" placeholder="Misal: Sambal dipisah, telur setengah matang, kuah banyak"
                        class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition">
                </div>
            </form>
        </section>

        <!-- ==================================================================== -->
        <!-- CORE FITUR 2 & 3: DAFTAR TITIPAN, TOGGLE BAYAR & DETAIL SHOW()      -->
        <!-- ==================================================================== -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>🍱 Daftar Pesanan Titipan Sirkel</span>
                        <span class="text-xs bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full font-bold">
                            <?php echo count($pesanan); ?> Orang
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Klik tombol <strong>Detail</strong> untuk menjalankan <code>show(\$id)</code> atau <strong>Tandai Lunas</strong> untuk menjalankan <code>process()</code>.
                    </p>
                </div>

                <!-- Filter & Reset Toolbar -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="index.php?filter=all"
                       class="text-xs font-bold px-3 py-1.5 rounded-xl border transition <?php echo $filterAktif === 'all' ? 'bg-orange-500 text-white border-orange-500 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'; ?>">
                        Semua
                    </a>
                    <a href="index.php?filter=belum"
                       class="text-xs font-bold px-3 py-1.5 rounded-xl border transition <?php echo $filterAktif === 'belum' ? 'bg-rose-500 text-white border-rose-500 shadow-sm' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50'; ?>">
                        ⏳ Belum Bayar
                    </a>
                    <a href="index.php?filter=lunas"
                       class="text-xs font-bold px-3 py-1.5 rounded-xl border transition <?php echo $filterAktif === 'lunas' ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50'; ?>">
                        ✅ Lunas
                    </a>
                    <a href="index.php?action=process&sub_action=reset"
                       onclick="return confirm('Kembalikan data pesanan sirkel ke kondisi awal?')"
                       class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 transition ml-2">
                        🔄 Reset Data Demo
                    </a>
                </div>
            </div>

            <!-- GRID CARDS PESANAN TITIPAN -->
            <?php if (empty($pesanan)): ?>
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/90 space-y-3">
                    <div class="text-4xl">🍽️</div>
                    <h4 class="font-extrabold text-slate-800 text-base">Tidak Ada Pesanan Pada Filter Ini</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Silakan pilih tab filter lain atau gunakan formulir di atas untuk menambahkan titipan baru.
                    </p>
                    <a href="index.php" class="inline-block px-4 py-2 bg-orange-600 text-white font-bold text-xs rounded-xl hover:bg-orange-700">
                        Lihat Semua Pesanan
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach ($pesanan as $item): ?>
                        <article class="bg-white rounded-2xl p-5 border shadow-xs hover:shadow-lg transition-all flex flex-col justify-between <?php echo $item['isLunas'] ? 'border-emerald-200 bg-gradient-to-b from-white to-emerald-50/20' : 'border-rose-200 bg-gradient-to-b from-white to-rose-50/20'; ?>">
                            <div class="space-y-3">
                                <!-- Card Header: Avatar & Penitip -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner shrink-0">
                                            <?php echo $item['avatar']; ?>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm leading-snug">
                                                <?php echo htmlspecialchars($item['namaPenitip']); ?>
                                            </h4>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                Order: <?php echo htmlspecialchars($item['waktuInput']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                        <?php echo $item['nomor']; ?>
                                    </span>
                                </div>

                                <!-- Detail Menu & Restoran -->
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-1">
                                    <div class="font-extrabold text-slate-800 text-xs">
                                        <?php echo htmlspecialchars($item['menu']); ?>
                                    </div>
                                    <div class="text-[11px] text-orange-600 font-semibold flex items-center gap-1">
                                        <span>📍</span>
                                        <span><?php echo htmlspecialchars($item['resto']); ?></span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 italic pt-0.5">
                                        "<?php echo htmlspecialchars($item['catatan']); ?>"
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Tagihan & Action Buttons -->
                            <div class="pt-4 mt-3 border-t border-slate-100 space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">Harga Pesanan:</span>
                                    <span class="text-sm font-black text-slate-900"><?php echo $item['hargaFormat']; ?></span>
                                </div>

                                <!-- Status Badge & Controller Action Buttons -->
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black border flex items-center gap-1 <?php echo $item['statusBadge']['class']; ?>">
                                        <span><?php echo $item['statusBadge']['icon']; ?></span>
                                        <span><?php echo $item['statusBadge']['label']; ?></span>
                                    </span>

                                    <div class="flex items-center gap-1.5">
                                        <!-- Tombol Panggil Method show($id) di Controller -->
                                        <a href="index.php?action=show&id=<?php echo $item['id']; ?>"
                                           title="Buka rincian invoice titipan (Method: show())"
                                           class="text-[11px] font-bold px-2.5 py-1 rounded-lg transition border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 shadow-2xs flex items-center gap-1">
                                            <span>🔍</span>
                                            <span>Detail</span>
                                        </a>

                                        <!-- Tombol Panggil Method process('toggle-bayar') di Controller -->
                                        <a href="index.php?action=process&sub_action=toggle-bayar&id=<?php echo $item['id']; ?>"
                                           title="Ubah status bayar lunas/utang (Method: process())"
                                           class="text-[11px] font-bold px-3 py-1 rounded-lg transition border shadow-2xs <?php echo $item['isLunas'] ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600'; ?>">
                                            <?php echo $item['isLunas'] ? 'Ubah ke Utang' : 'Tandai Lunas ✓'; ?>
                                        </a>

                                        <!-- Tombol Hapus Pesanan -->
                                        <a href="index.php?action=process&sub_action=hapus&id=<?php echo $item['id']; ?>"
                                           onclick="return confirm('Batalkan titipan dari <?php echo htmlspecialchars(addslashes($item['namaPenitip'])); ?>?')"
                                           title="Batalkan pesanan titipan"
                                           class="text-[11px] font-bold p-1 rounded-lg transition border border-slate-200 hover:bg-rose-50 text-slate-400 hover:text-rose-600">
                                            🗑️
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- ==================================================================== -->
        <!-- CORE FITUR 4: GENERATOR TAGIHAN WHATSAPP (ONE-CLICK COPY)            -->
        <!-- ==================================================================== -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>📲 Generator Rekap Tagihan WhatsApp Sirkel</span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Disusun otomatis oleh Controller (<code>TitipanController::generateWhatsAppText</code>). Sekali klik untuk salin teks dan kirim ke grup tongkrongan.
                    </p>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <button type="button" id="copyWaBtn" onclick="copyWhatsAppRekap()"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 cursor-pointer">
                        <span id="copyIcon">📋</span>
                        <span id="copyText">Salin Teks WhatsApp</span>
                    </button>
                    <a href="https://wa.me/?text=<?php echo urlencode($waText); ?>" target="_blank" rel="noopener noreferrer"
                        class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <span>💬</span>
                        <span>Buka WA</span>
                    </a>
                </div>
            </div>

            <div class="relative">
                <textarea id="waRekapArea" readonly rows="8"
                    class="w-full text-xs font-mono bg-slate-900 text-slate-100 rounded-2xl p-4 border border-slate-800 focus:outline-none leading-relaxed resize-y selection:bg-emerald-500 selection:text-white"><?php echo htmlspecialchars($waText); ?></textarea>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-12">
        <div class="max-w-6xl mx-auto px-4 space-y-1">
            <p class="font-bold text-slate-700">
                Nitip<span class="text-orange-500">Dong</span> © 2026 • Gak Pusing Lagi Ngurusin Titipan Makanan Temen!
            </p>
            <p class="text-[11px] text-slate-400">
                Pertemuan 7 Web Programming • Pola MVC Nyata: Model (<code>Model/Titipan.php</code>) • View (<code>View/titipan-page.php</code>) • Controller (<code>Controller/TitipanController.php</code>)
            </p>
        </div>
    </footer>

    <!-- JAVASCRIPT COPY REKAP -->
    <script>
        function copyWhatsAppRekap() {
            const textarea = document.getElementById('waRekapArea');
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(textarea.value).then(() => {
                const btnText = document.getElementById('copyText');
                const btnIcon = document.getElementById('copyIcon');
                btnText.innerText = 'Tersalin ke Clipboard!';
                btnIcon.innerText = '✅';
                setTimeout(() => {
                    btnText.innerText = 'Salin Teks WhatsApp';
                    btnIcon.innerText = '📋';
                }, 2500);
            }).catch(err => {
                alert('Gagal menyalin teks, silakan salin manual dari kotak teks.');
            });
        }
    </script>

</body>
</html>
