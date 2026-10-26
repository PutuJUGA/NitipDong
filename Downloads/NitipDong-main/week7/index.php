<?php
/**
 * ============================================================================
 * ENTRY POINT: index.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel)
 * Modul: Pertemuan 7 - Web Programming: Task 01 MVC Structure
 * ============================================================================
 * Berkas utama untuk memicu alur kerja MVC NitipDong.
 * Menginisialisasi TitipanController dan memanggil method handleRequest().
 * ============================================================================
 */

// Muat Controller utama NitipDong
require_once __DIR__ . '/Controller/TitipanController.php';

// Inisialisasi Controller dan jalankan dispatcher alur request
$controller = new TitipanController();
$controller->handleRequest();
