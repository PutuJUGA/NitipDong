<?php
/**
 * ============================================================================
 * VIEW: titipan-detail.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Task 02: Build the Controller)
 * ============================================================================
 * TANGGUNG JAWAB VIEW:
 * 1. Ditampilkan secara khusus ketika pengguna/controller memanggil method:
 *    TitipanController::show($id)
 * 2. Menyajikan rincian invoice titipan seorang penitip, catatan pesanan,
 *    dan rincian transfer ke rekening host room.
 * 3. Menyediakan tombol aksi cepat: Ubah Status Bayar, Batalkan Titipan,
 *    dan Kirim Pengingat WhatsApp Personal.
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Titipan: <?php echo htmlspecialchars($item['namaPenitip']); ?> - NitipDong</title>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-xl shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                        🍱
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-900">
                            Nitip<span class="text-orange-500">Dong</span>
                        </h1>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                            Rincian Titipan • Method Controller: show(<?php echo $item['id']; ?>)
                        </p>
                    </div>
                </a>
            </div>

            <!-- TOMBOL KEMBALI KE INDEX -->
            <a href="index.php" class="text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 transition flex items-center gap-1.5 shadow-2xs">
                <span>←</span>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </header>

    <!-- FLASH NOTIFICATION -->
    <?php if (!empty($pesanNotif)): ?>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 w-full animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="p-4 rounded-2xl border flex items-center justify-between text-xs font-semibold <?php echo $pesanNotif['tipe'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($pesanNotif['tipe'] === 'error' ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-blue-50 text-blue-800 border-blue-200'); ?>">
                <div class="flex items-center gap-2">
                    <span class="text-lg"><?php echo $pesanNotif['tipe'] === 'success' ? '🎉' : ($pesanNotif['tipe'] === 'error' ? '⚠️' : 'ℹ️'); ?></span>
                    <span><?php echo $pesanNotif['teks']; ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- MAIN BODY -->
    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 flex-1 space-y-6">

        <!-- BREADCRUMB & STATUS BADGE -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="index.php" class="hover:text-orange-600 transition">Daftar Titipan</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Detail Pesanan #<?php echo $item['id']; ?></span>
            </nav>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-xl text-xs font-black border flex items-center gap-1.5 <?php echo $item['statusBadge']['class']; ?>">
                    <span><?php echo $item['statusBadge']['icon']; ?></span>
                    <span><?php echo $item['statusBadge']['label']; ?></span>
                </span>
            </div>
        </div>

        <!-- MAIN CARD RINCIAN PESANAN -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            
            <!-- Profil Penitip & Waktu Pesan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-3xl shadow-inner shrink-0">
                        <?php echo $item['avatar']; ?>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Penitip Pesanan</div>
                        <h2 class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars($item['namaPenitip']); ?></h2>
                        <p class="text-xs text-slate-500">Waktu Pemesanan: <strong class="text-slate-700"><?php echo htmlspecialchars($item['waktuInput']); ?></strong></p>
                    </div>
                </div>

                <div class="sm:text-right bg-slate-50 sm:bg-transparent p-4 sm:p-0 rounded-2xl border sm:border-0 border-slate-100">
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Nominal Tagihan</div>
                    <div class="text-3xl font-black text-slate-900"><?php echo $item['hargaFormat']; ?></div>
                    <div class="text-xs text-slate-500 font-medium">Metode: <?php echo htmlspecialchars($item['metodeBayar']); ?></div>
                </div>
            </div>

            <!-- Detail Menu & Spesifikasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
                    <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">Detail Menu Makanan/Minuman</div>
                    <div class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🍱</span>
                        <span><?php echo htmlspecialchars($item['menu']); ?></span>
                    </div>
                    <div class="text-xs text-orange-600 font-bold flex items-center gap-1.5">
                        <span>📍</span>
                        <span>Resto: <?php echo htmlspecialchars($item['resto']); ?></span>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2">
                    <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">Catatan Khusus dari Penitip</div>
                    <div class="text-xs text-slate-700 italic bg-white p-3 rounded-xl border border-slate-200">
                        "<?php echo htmlspecialchars($item['catatan']); ?>"
                    </div>
                </div>
            </div>

            <!-- Info Pembayaran Host -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-4 sm:p-5 border border-orange-200 space-y-2">
                <div class="text-[11px] font-black uppercase tracking-wider text-orange-800 flex items-center gap-1.5">
                    <span>💳</span>
                    <span>Tujuan Pembayaran ke Host (<?php echo htmlspecialchars($room['host']); ?>)</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div>
                        <div class="font-mono font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($room['rekeningHost']); ?></div>
                        <div class="text-slate-600">Batas Waktu Transfer: <strong class="text-rose-600"><?php echo htmlspecialchars($room['deadlineOrder']); ?></strong></div>
                    </div>
                    <span class="text-[11px] text-orange-700 bg-white/80 px-3 py-1 rounded-lg border border-orange-200 font-semibold self-start sm:self-auto">
                        Room: <?php echo htmlspecialchars($room['namaRoom']); ?>
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi Mutasi Data Melalui Controller -->
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <!-- Toggle Bayar via Controller process() -->
                    <a href="index.php?action=process&sub_action=toggle-bayar&id=<?php echo $item['id']; ?>&from_show=1"
                       class="text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-md flex items-center gap-2 <?php echo $item['isLunas'] ? 'bg-slate-200 hover:bg-slate-300 text-slate-800' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20'; ?>">
                        <span><?php echo $item['isLunas'] ? '↩️ Ubah Menjadi Belum Bayar (Utang)' : '✅ Tandai Sudah Lunas Ditransfer'; ?></span>
                    </a>

                    <!-- Hapus Pesanan -->
                    <a href="index.php?action=process&sub_action=hapus&id=<?php echo $item['id']; ?>"
                       onclick="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus pesanan dari <?php echo htmlspecialchars(addslashes($item['namaPenitip'])); ?>?')"
                       class="text-xs font-bold px-4 py-2.5 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 transition flex items-center gap-1.5">
                        <span>🗑️</span>
                        <span>Batalkan Pesanan</span>
                    </a>
                </div>

                <a href="index.php" class="text-xs text-slate-500 hover:text-slate-800 font-semibold transition">
                    Kembali ke Rekap Utama
                </a>
            </div>

        </div>

        <!-- FITUR PENGINGAT WHATSAPP PERSONAL -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>💬 Teks Pengingat WhatsApp Personal untuk <?php echo htmlspecialchars($item['namaPenitip']); ?></span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Disusun otomatis oleh Controller (<code>TitipanController::generatePersonalWhatsAppReminder</code>).
                    </p>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <button type="button" onclick="copyPersonalWa()"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <span id="copyPersonalIcon">📋</span>
                        <span id="copyPersonalText">Salin Teks</span>
                    </button>
                    <a href="https://wa.me/?text=<?php echo urlencode($personalWaText); ?>" target="_blank" rel="noopener noreferrer"
                        class="px-3.5 py-2 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <span>📲</span>
                        <span>Kirim via WA</span>
                    </a>
                </div>
            </div>

            <textarea id="personalWaArea" readonly rows="7"
                class="w-full text-xs font-mono bg-slate-900 text-slate-100 rounded-2xl p-4 border border-slate-800 focus:outline-none leading-relaxed resize-y selection:bg-emerald-500 selection:text-white"><?php echo htmlspecialchars($personalWaText); ?></textarea>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-12">
        <div class="max-w-4xl mx-auto px-4 space-y-1">
            <p class="font-bold text-slate-700">
                Nitip<span class="text-orange-500">Dong</span> • Rincian Pesanan Titipan
            </p>
            <p class="text-[11px] text-slate-400">
                Pertemuan 7 Web Programming • Method Controller: <code>TitipanController::show(<?php echo $item['id']; ?>)</code>
            </p>
        </div>
    </footer>

    <!-- JAVASCRIPT COPY PERSONAL WA -->
    <script>
        function copyPersonalWa() {
            const textarea = document.getElementById('personalWaArea');
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(textarea.value).then(() => {
                const btnText = document.getElementById('copyPersonalText');
                const btnIcon = document.getElementById('copyPersonalIcon');
                btnText.innerText = 'Tersalin!';
                btnIcon.innerText = '✅';
                setTimeout(() => {
                    btnText.innerText = 'Salin Teks';
                    btnIcon.innerText = '📋';
                }, 2500);
            }).catch(err => {
                alert('Gagal menyalin teks, silakan salin manual.');
            });
        }
    </script>

</body>
</html>
