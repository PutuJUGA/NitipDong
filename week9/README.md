# Pertemuan 9 - Pemrograman Web: Laravel CRUD + ORM (NitipDong 🍱)

Implementasi praktikum **Week 9** untuk proyek web **NitipDong** (Aplikasi Titip Beli & Split Bill Sirkel Kampus/Kantor) menggunakan framework **Laravel 11/12** dengan arsitektur **CRUD & Eloquent Object-Relational Mapping (ORM)**.

---

## 🎯 Pemenuhan 7 Langkah Tugas Praktikum

Sesuai instruksi pada modul pertemuan 9:

| No | Instruksi Tugas | Status | Berkas / Direktori Terkait | Penjelasan Implementasi |
|:--:|:---|:---:|:---|:---|
| **1** | **Instalasi Laravel** | ✅ | Folder [`week9/`](./) | Proyek Laravel diinstal menggunakan Composer (`laravel/laravel v13.x`). |
| **2** | **Setting Database (.env)** | ✅ | [`.env`](.env) | Mengonfigurasi `APP_NAME="NitipDong"`, koneksi database SQLite (siap jalan instan), serta template koneksi MySQL XAMPP (`DB_CONNECTION=mysql`, `DB_DATABASE=nitipdong_db`). |
| **3** | **Buat Table di Database (Migrations)** | ✅ | [`database/migrations/`](database/migrations/) | Membuat migrasi untuk **sejumlah table** (relasi multi-tabel):<br>1. `2026_11_09_122420_create_kategoris_table.php`<br>2. `2026_11_09_122421_create_titipans_table.php` (Foreign Key `kategori_id` ke tabel `kategoris`). |
| **4** | **Buat Model untuk Masing-Masing Table** | ✅ | [`app/Models/`](app/Models/) | 1. [`Kategori.php`](app/Models/Kategori.php): Relasi ORM `hasMany(Titipan::class)`.<br>2. [`Titipan.php`](app/Models/Titipan.php): Relasi ORM `belongsTo(Kategori::class)` lengkap dengan casts & helper format Rupiah. |
| **5** | **Buat Controller (Method index)** | ✅ | [`app/Http/Controllers/TitipanController.php`](app/Http/Controllers/TitipanController.php) | Method `index()` mengambil pesanan menggunakan Eager Loading ORM (`Titipan::with('kategori')->latest()`), filter dinamis, serta kalkulasi ringkasan keuangan split bill (ORM aggregation). Dilengkapi juga method CRUD pendukung (`store`, `toggleStatus`, `destroy`). |
| **6** | **Buat Views (index)** | ✅ | [`resources/views/titipan/index.blade.php`](resources/views/titipan/index.blade.php) | Template Blade modern dengan antarmuka bertema NitipDong (Tailwind CSS, kartu metrik split bill, badge kategori ORM, progress bar pelunasan, tabel pesanan responsif, dan modal tambah titipan). |
| **7** | **Buat Route (index)** | ✅ | [`routes/web.php`](routes/web.php) | Mendaftarkan route utama `Route::get('/', [TitipanController::class, 'index'])->name('titipan.index')` dan route pendukung CRUD. |

---

## 🔗 Integrasi 1 Projek pada Web Utama NitipDong

Fitur **Database & Eloquent ORM (CRUD)** ini telah terintegrasi langsung sebagai modul bawaan di dalam **Website Utama NitipDong** (`npm run dev`):
1. **Navigasi Langsung di Navbar Web Utama**: Tersedia tombol **🗄️ Database ORM** di navbar utama ([`src/App.vue`](../src/App.vue)) yang langsung mengarahkan pengguna ke dashboard database.
2. **Menu Sidebar Dashboard**: Pada dashboard sirkel ([`src/components/MainMenu.vue`](../src/components/MainMenu.vue)), terdapat item menu **Database & ORM (WEEK 9)** dengan icon database.
3. **Komponen Terpadu ([`src/components/OrmCrudManager.vue`](../src/components/OrmCrudManager.vue))**:
   - Mendukung koneksi live (*Live API Sync*) ke backend Laravel `week9/` pada port 8000 (`http://127.0.0.1:8000/api/titipans`).
   - Menyediakan visualisasi interaktif relasi ORM multi-tabel (`kategoris` & `titipans`).
   - Memiliki fitur **"Ke Room"** untuk langsung menyalin pesanan dari database ORM ke Room aktif NitipDong.
   - Tetap berjalan mulus dengan persistensi lokal bila backend server Laravel belum dinyalakan.

---

## 🗄️ Skema Database & Relasi Eloquent ORM

```text
┌───────────────────────────────┐               ┌─────────────────────────────────────────┐
│          KATEGORIS            │               │                TITIPANS                 │
├───────────────────────────────┤               ├─────────────────────────────────────────┤
│ id (PK)                       │ 1           N │ id (PK)                                 │
│ nama_kategori (varchar 100)   │───────────────│ kategori_id (FK -> kategoris.id)        │
│ slug (varchar 100)            │  hasMany      │ nama_penitip (varchar 100)              │
│ icon (varchar 50)             │  belongsTo    │ nama_barang (varchar 150)               │
│ deskripsi (text)              │               │ lokasi_beli (varchar 150)               │
│ created_at / updated_at       │               │ harga_barang (unsigned int)             │
└───────────────────────────────┘               │ biaya_jastip (unsigned int)             │
                                                │ jumlah (unsigned smallint)              │
                                                │ total_bayar (unsigned int)              │
                                                │ status_pembayaran (enum: belum, lunas)  │
                                                │ catatan (text, nullable)                │
                                                │ created_at / updated_at                 │
                                                └─────────────────────────────────────────┘
```

---

## 📁 Struktur Direktori Week 9

```text
week9/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── TitipanController.php   # Controller utama (method index & CRUD)
│   └── Models/
│       ├── Kategori.php                # Model ORM Tabel Kategoris (hasMany Titipan)
│       └── Titipan.php                 # Model ORM Tabel Titipans (belongsTo Kategori)
├── database/
│   ├── database.sqlite                 # File database lokal SQLite
│   ├── migrations/
│   │   ├── ..._create_kategoris_table.php # Migrasi tabel kategoris
│   │   └── ..._create_titipans_table.php  # Migrasi tabel titipans
│   └── seeders/
│       └── DatabaseSeeder.php          # Dummy data realistis NitipDong
├── resources/
│   └── views/
│       └── titipan/
│           └── index.blade.php         # Tampilan Blade halaman utama (View index)
├── routes/
│   └── web.php                         # Definisi route halaman index & CRUD
├── .env                                # Konfigurasi environment & database
└── README.md                           # Dokumentasi tugas ini
```

---

## 🚀 Panduan Menjalankan Aplikasi

### Opsi 1: Menjalankan Langsung dengan SQLite (Default & Siap Pakai)
Database SQLite sudah dimigrasi dan diisi data seed demo NitipDong secara otomatis.

1. Buka terminal dan masuk ke folder `week9`:
   ```bash
   cd week9
   ```
2. Jalankan development server bawaan Laravel:
   ```bash
   php artisan serve
   ```
3. Buka browser pada alamat:
   ```text
   http://localhost:8000
   ```

### Opsi 2: Menggunakan MySQL (XAMPP)
Jika ingin menggunakan database MySQL di phpMyAdmin:

1. Nyalakan modul **Apache** dan **MySQL** di **XAMPP Control Panel**.
2. Buka phpMyAdmin (`http://localhost/phpmyadmin`) dan buat database baru bernama: `nitipdong_db`.
3. Buka file `.env` di folder `week9`, sesuaikan bagian database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nitipdong_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Jalankan migrasi dan seeder di terminal:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. Jalankan server:
   ```bash
   php artisan serve
   ```

---

## ✨ Fitur-Fitur yang Tersedia pada Halaman Index

1. **Dashboard Metrik Keuangan Split Bill**:
   - Menghitung **Total Tagihan Seluruh Sirkel**, **Sudah Dibayar (Lunas)**, **Sisa Utang Belum Bayar**, dan persentase progress pelunasan secara *real-time* via ORM.
2. **Eager Loading Relasi ORM**:
   - Menampilkan badge kategori (`$titipan->kategori->nama_kategori`) dan icon tanpa *N+1 query problem*.
3. **Filter & Pencarian Dinamis**:
   - Pencarian berdasarkan nama penitip, nama barang/menu, atau warung/kantin.
   - Filter *dropdown* berdasarkan Kategori dan Status Pembayaran.
4. **Interaksi Cepat Status Bayar**:
   - Tombol toggle instan untuk mengubah status pesanan antara **Lunas** dan **Belum Bayar**.
5. **Modal Tambah Titipan Baru**:
   - Formulir modal popup dengan validasi data dan kalkulasi otomatis total tagihan.
