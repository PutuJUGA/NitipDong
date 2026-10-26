<?php
/**
 * ============================================================================
 * ENTRY POINT & FRONT CONTROLLER: index.php
 * Proyek: NitipDong 🍱 (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor)
 * Modul: Pertemuan 7 - Web Programming (Task 02: Build the Controller)
 * ============================================================================
 * ALUR SISTEM:
 *   Browser / User Request ➔ index.php (Entry Point) ➔ TitipanController ➔ Model ➔ View
 * ============================================================================
 */

// Muat Controller utama NitipDong
require_once __DIR__ . '/Controller/TitipanController.php';

// Inisialisasi instance Controller
$controller = new TitipanController();

// Delegasikan penanganan request pengguna ke Controller
$controller->handleRequest();
