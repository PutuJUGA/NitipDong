# PresidenKu - Implementasi PHP Prosedural (Server-Side Rendering)

Modul lanjutan dari **Week 4 (PresidenKu SPA)** yang diubah menjadi arsitektur **Server-Side Rendering (SSR)** menggunakan **PHP Prosedural**.

---

## 📋 Pemenuhan Tugas Praktikum

1. **Task 01: Create PHP Page**
   - Menginisialisasi variabel PHP utama:
     - `$namaPresiden`
     - `$periode`
     - `$statusProgram`
     - `$programPrioritas`
   - Ditampilkan langsung pada banner hero menggunakan sintaks `<?php echo ...; ?>`.

2. **Task 02: Process Data with PHP**
   - **Array Multidimensi**: `$daftarProgram` yang menyimpan data program kerja (id, judul, kategori, target, anggaran, status, icon, deskripsi).
   - **Conditional Status/Badge**: Fungsi `getStatusBadgeClass($status)` yang mengembalikan warna kelas Tailwind berdasarkan status (`Prioritas Utama`, `Siap Eksekusi`, `Dalam Kajian`, dsb).
   - **Loop Penomoran**: Menggunakan perulangan `foreach ($daftarProgram as $program)` dengan variabel inkremen `$nomorUrut` (`#01`, `#02`, dst).

3. **Task 03: Build Dynamic Server-Side Content**
   - Halaman HTML dirender secara utuh dari server (Server-Side Rendering / SSR).
   - Menghasilkan dynamic grid cards Program Prioritas yang responsif dan modern dengan Tailwind CSS.

---

## 🚀 Cara Menjalankan Berkas PHP

### Opsi 1: Menggunakan PHP Built-in Server (Terminal)
Jika Anda sudah menginstal PHP di laptop (atau via XAMPP):
```bash
cd "week5 - PHP"
php -S localhost:8000
```
Buka browser di: `http://localhost:8000`

### Opsi 2: Menggunakan XAMPP (Apache)
1. Salin folder `week5 - PHP` ke dalam direktori `C:\xampp\htdocs\`.
2. Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache**.
3. Buka browser di: `http://localhost/week5%20-%20PHP/`
