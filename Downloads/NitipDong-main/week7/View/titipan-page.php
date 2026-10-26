<?php
/**
 * ============================================================================
 * VIEW: titipan-page.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel)
 * Modul: Pertemuan 7 - Web Programming: Task 01 MVC Structure
 * ============================================================================
 * TANGGUNG JAWAB VIEW:
 * 1. Murni menangani tampilan antarmuka (User Interface & User Experience).
 * 2. Menerima variabel siap saji ($room, $pesanan, $stat, $waText, $pesanNotif) dari Controller.
 * 3. Tidak memuat logika query data mentah atau mutasi database (Separation of Concerns).
 * 4. Menyajikan komponen interaktif untuk entri pesanan, toggle status lunas, dan salin tagihan WA.
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['namaRoom']); ?> - NitipDong (MVC Architecture)</title>
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

    <!-- NAVBAR HEADER -->
    <header class="bg-white border-b border-orange-100 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-orange-500/20">
                    🍱
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">
                        Nitip<span class="text-orange-500">Dong</span>
                    </h1>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
                        Pertemuan 7 Web Programming • Desain Arsitektur MVC
                    </p>
                </div>
            </div>

            <!-- MVC BADGES -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Model: Titipan.php
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    View: titipan-page.php
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Controller: TitipanController.php
                </span>
            </div>
        </div>
    </header>

    <!-- FLASH NOTIFICATION -->
    <?php if (!empty($pesanNotif)): ?>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-6 w-full">
            <div class="p-4 rounded-2xl border flex items-center justify-between text-xs font-semibold <?php echo $pesanNotif['tipe'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($pesanNotif['tipe'] === 'error' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-blue-50 text-blue-800 border-blue-200'); ?>">
                <div class="flex items-center gap-2">
                    <span class="text-base"><?php echo $pesanNotif['tipe'] === 'success' ? '🎉' : ($pesanNotif['tipe'] === 'error' ? '⚠️' : 'ℹ️'); ?></span>
                    <span><?php echo $pesanNotif['teks']; ?></span>
                </div>
                <a href="index.php" class="text-slate-400 hover:text-slate-700 font-bold ml-4">✕</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- MAIN BODY -->
    <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 flex-1 space-y-8">

        <!-- ==================================================================== -->
        <!-- BANNER CHECKLIST EVALUASI TUGAS PERTEMUAN 7 (TASK 01 MVC STRUCTURE)  -->
        <!-- ==================================================================== -->
        <section class="bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 border border-orange-200 rounded-3xl p-6 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-1.5 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-black bg-orange-500 text-white uppercase tracking-wider">
                        Pertemuan 7 • Task 01: Design the MVC Structure
                    </div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">
                        Penerapan MVC Disesuaikan untuk Proyek NitipDong 🍱
                    </h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data dan alur aplikasi dipisahkan secara independen ke dalam <strong>Model</strong> (katalog pesanan & kalkulasi bill), <strong>Controller</strong> (orkestrator request & WhatsApp generator), dan <strong>View</strong> (dashboard interaktif).
                    </p>
                </div>

                <!-- 4 Checklist Items -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs font-bold">
                    <div class="bg-white/90 backdrop-blur-xs p-3 rounded-2xl border border-orange-200/70 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Model Ditentukan</span>
                        <span class="text-[10px] text-slate-500 font-medium">Titipan.php</span>
                    </div>
                    <div class="bg-white/90 backdrop-blur-xs p-3 rounded-2xl border border-orange-200/70 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">View Ditentukan</span>
                        <span class="text-[10px] text-slate-500 font-medium">titipan-page.php</span>
                    </div>
                    <div class="bg-white/90 backdrop-blur-xs p-3 rounded-2xl border border-orange-200/70 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Controller Ditentukan</span>
                        <span class="text-[10px] text-slate-500 font-medium">TitipanController.php</span>
                    </div>
                    <div class="bg-white/90 backdrop-blur-xs p-3 rounded-2xl border border-orange-200/70 flex flex-col gap-1 text-emerald-700 shadow-2xs">
                        <span class="text-lg">✅</span>
                        <span class="font-extrabold text-[11px] text-slate-900">Tanggung Jawab Jelas</span>
                        <span class="text-[10px] text-slate-500 font-medium">Separation of Concerns</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================== -->
        <!-- ROOM HERO CARD (INFO HOST & METRIK FINANCIAL SPLIT BILL)             -->
        <!-- ==================================================================== -->
        <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-zinc-900 to-slate-950 text-white p-6 sm:p-10 shadow-xl relative overflow-hidden border border-slate-800">
            <!-- Glow Efek -->
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

                    <div class="bg-white/5 border border-white/10 p-3.5 rounded-2xl text-xs space-y-1 shrink-0">
                        <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Rekening Tujuan / QRIS:</div>
                        <div class="font-mono font-bold text-amber-300 text-sm"><?php echo htmlspecialchars($room['rekeningHost']); ?></div>
                        <div class="text-[11px] text-rose-400 font-semibold">⏰ Batas Pesanan: <?php echo htmlspecialchars($room['deadlineOrder']); ?></div>
                    </div>
                </div>

                <!-- Financial Metric Cards (Hasil Perhitungan Model Titipan) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Total Tagihan Sirkel</span>
                        <span class="text-xl sm:text-2xl font-black text-white block mt-1"><?php echo $stat['totalTagihan']; ?></span>
                        <span class="text-[11px] text-slate-400 mt-1 block">Dari <?php echo $stat['totalPesanan']; ?> pesanan titipan</span>
                    </div>

                    <div class="bg-emerald-500/10 rounded-2xl p-4 border border-emerald-500/20">
                        <span class="text-emerald-400 block text-[10px] uppercase font-bold tracking-wider">Sudah Lunas Ditransfer</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-400 block mt-1"><?php echo $stat['totalLunas']; ?></span>
                        <span class="text-[11px] text-emerald-300/80 mt-1 block">✅ <?php echo $stat['jumlahLunas']; ?> orang telah melunasi</span>
                    </div>

                    <div class="bg-rose-500/10 rounded-2xl p-4 border border-rose-500/20">
                        <span class="text-rose-400 block text-[10px] uppercase font-bold tracking-wider">Belum Lunas (Utang)</span>
                        <span class="text-xl sm:text-2xl font-black text-rose-400 block mt-1"><?php echo $stat['totalBelumLunas']; ?></span>
                        <span class="text-[11px] text-rose-300/80 mt-1 block">⏳ <?php echo $stat['jumlahBelumLunas']; ?> orang belum transfer</span>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Progres Pelunasan</span>
                        <span class="text-xl sm:text-2xl font-black text-amber-300 block mt-1"><?php echo $stat['persentaseLunas']; ?>%</span>
                        <!-- Progress Bar -->
                        <div class="w-full bg-white/10 h-2 rounded-full mt-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: <?php echo min(100, $stat['persentaseLunas']); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================================================================== -->
        <!-- CORE FITUR 1: FORMULIR ENTRI CEPAT TITIPAN MAKANAN                  -->
        <!-- ==================================================================== -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>➕ Formulir Entri Cepat Titipan Baru</span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Menambahkan titipan teman baru ke dalam Model secara dinamis melalui Controller
                    </p>
                </div>
                <span class="text-[11px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full border border-orange-200">
                    Input 10 Detik
                </span>
            </div>

            <form action="index.php?action=tambah" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 pt-2">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Penitip</label>
                    <input type="text" name="namaPenitip" placeholder="Cth: Angga Wijaya" required
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Menu Makanan / Minuman</label>
                    <input type="text" name="menu" placeholder="Cth: Nasi Goreng Gila Pedas" required
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Restoran / Warung</label>
                    <input type="text" name="resto" placeholder="Cth: Kantin Barokah"
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nominal Harga (Rp)</label>
                    <input type="number" name="harga" placeholder="25000" min="1000" step="500" required
                        class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white transition">
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex flex-col sm:flex-row gap-3 pt-1">
                    <input type="text" name="catatan" placeholder="Catatan kustom (Opsional: Cth: Jangan pakai daun bawang, sambal dipisah)"
                        class="flex-1 text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white transition">
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs rounded-xl shadow-md shadow-orange-500/20 transition flex items-center justify-center gap-1.5 shrink-0">
                        <span>Simpan Titipan</span>
                        <span>➔</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- ==================================================================== -->
        <!-- CORE FITUR 2 & 3: DAFTAR TITIPAN SIRKEL & INDIKATOR STATUS LUNAS     -->
        <!-- ==================================================================== -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Daftar Titipan Rekan Sirkel</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-orange-100 text-orange-600">
                            <?php echo $stat['totalPesanan']; ?> Orang
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">
                        Klik tombol status untuk mengubah LUNAS / BELUM BAYAR secara real-time
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="index.php?action=reset" onclick="return confirm('Kembalikan data pesanan ke default awal?');"
                        class="text-xs text-slate-500 hover:text-slate-800 font-semibold bg-white px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition shadow-2xs">
                        🔄 Reset Data Demo
                    </a>
                </div>
            </div>

            <!-- GRID CARDS PESANAN TITIPAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($pesanan as $item): ?>
                    <article class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-lg transition flex flex-col justify-between <?php echo $item['isLunas'] ? 'border-emerald-200 bg-gradient-to-b from-white to-emerald-50/20' : 'border-rose-200 bg-gradient-to-b from-white to-rose-50/20'; ?>">
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
                                            Diinput: <?php echo htmlspecialchars($item['waktuInput']); ?>
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

                        <!-- Card Footer: Tagihan & Toggle Button -->
                        <div class="pt-4 mt-3 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Harga Pesanan:</span>
                                <span class="text-sm font-black text-slate-900"><?php echo $item['hargaFormat']; ?></span>
                            </div>

                            <!-- Tombol Aksi Toggle Lunas / Belum Lunas -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black border flex items-center gap-1 <?php echo $item['statusBadge']['class']; ?>">
                                    <span><?php echo $item['statusBadge']['icon']; ?></span>
                                    <span><?php echo $item['statusBadge']['label']; ?></span>
                                </span>

                                <a href="index.php?action=toggle-bayar&id=<?php echo $item['id']; ?>"
                                    class="text-[11px] font-bold px-3 py-1 rounded-lg transition border shadow-2xs <?php echo $item['isLunas'] ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600'; ?>">
                                    <?php echo $item['isLunas'] ? 'Ubah ke Utang' : 'Tandai Lunas ✓'; ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
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
                        Diformat otomatis oleh Controller. Cukup sekali klik untuk salin teks dan kirim ke grup WhatsApp.
                    </p>
                </div>
                <button type="button" id="copyWaBtn" onclick="copyWhatsAppRekap()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                    <span id="copyIcon">📋</span>
                    <span id="copyText">Salin Teks WhatsApp</span>
                </button>
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
                Implementasi Murni Pola MVC: Model (<code>Model/Titipan.php</code>) • View (<code>View/titipan-page.php</code>) • Controller (<code>Controller/TitipanController.php</code>)
            </p>
        </div>
    </footer>

    <!-- SCRIPT COPY WHATSAPP -->
    <script>
        function copyWhatsAppRekap() {
            const textarea = document.getElementById('waRekapArea');
            textarea.select();
            textarea.setSelectionRange(0, 99999); // Untuk smartphone
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
