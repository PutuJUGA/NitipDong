<?php
/**
 * ============================================================================
 * VIEW: view-titipan.php (View Utama / Alias untuk titipan-page.php)
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Arsitektur MVC)
 * ============================================================================
 * TANGGUNG JAWAB VIEW:
 * 1. Menerima variabel data siap pakai dari TitipanController:
 *    - $room        : Metadata room titipan
 *    - $pesanan     : Daftar pesanan yang telah diformat rapi
 *    - $stat        : Statistik keuangan split bill (total, lunas, sisa utang)
 *    - $waText      : Rekap teks WhatsApp kolektif
 *    - $filterAktif : Filter yang aktif ('all', 'lunas', 'belum')
 *    - $pesanNotif  : Flash message session notifikasi
 * 2. Menampilkan antarmuka pengguna dalam format HTML yang rapi ke browser.
 * ============================================================================
 */

require __DIR__ . '/titipan-page.php';

