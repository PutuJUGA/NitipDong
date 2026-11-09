<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitipanController;

/**
 * ============================================================================
 * ROUTE PERTEMUAN 9: LARAVEL CRUD + ORM (Tema NitipDong 🍱)
 * ============================================================================
 */

// LANGKAH 7: Route index untuk menampilkan halaman utama titipan
Route::get('/', [TitipanController::class, 'index'])->name('titipan.index');
Route::get('/titipan', [TitipanController::class, 'index'])->name('titipan.index.alias');

// Rute Pendukung Fitur CRUD Lengkap (Create, Update Status, Delete)
Route::post('/titipan', [TitipanController::class, 'store'])->name('titipan.store');
Route::patch('/titipan/{titipan}/toggle-status', [TitipanController::class, 'toggleStatus'])->name('titipan.toggle-status');
Route::delete('/titipan/{titipan}', [TitipanController::class, 'destroy'])->name('titipan.destroy');
