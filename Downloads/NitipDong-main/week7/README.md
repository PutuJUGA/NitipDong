# Pertemuan 7 - Web Programming: Task 01: Design the MVC Structure

Implementasi arsitektur **Model-View-Controller (MVC)** yang disesuaikan secara tematik dan fungsional 100% dengan proyek **NitipDong 🍱** (Aplikasi Titip Beli & Split Bill Sirkel).

---

## 🎯 Pemenuhan Tugas Praktikum

Sesuai instruksi pada modul slide **Pertemuan 7 (Web Programming)**:
> *"Pisahkan struktur aplikasi menjadi Model, View, dan Controller.*  
> *Mahasiswa menentukan sendiri:*  
> *- data yang dikelola Model;*  
> *- tampilan yang menjadi View;*  
> *- proses yang menjadi tanggung jawab Controller."*

### ✅ Checklist Evaluasi:
- [x] **Model Ditentukan**: [`Model/Titipan.php`](file:///Users/llauerwinrahman/Downloads/NitipDong-main/week7/Model/Titipan.php)
- [x] **View Ditentukan**: [`View/titipan-page.php`](file:///Users/llauerwinrahman/Downloads/NitipDong-main/week7/View/titipan-page.php)
- [x] **Controller Ditentukan**: [`Controller/TitipanController.php`](file:///Users/llauerwinrahman/Downloads/NitipDong-main/week7/Controller/TitipanController.php)
- [x] **Tanggung Jawab Masing-Masing Jelas**: Menerapkan prinsip *Separation of Concerns (SoC)* secara ketat dan bersih.

---

## 🏗️ Struktur Direktori MVC NitipDong

```text
week7/
├── Controller/
│   └── TitipanController.php   # Mengatur alur request, aksi, formatting rupiah & generator WA
├── Model/
│   └── Titipan.php             # Mengelola data room titipan, katalog pesanan sirkel & kalkulasi utang/lunas
├── View/
│   └── titipan-page.php        # Tampilan antarmuka (UI/UX) modern berbasis Tailwind CSS
├── index.php                   # Berkas entri utama (Entry Point)
└── README.md                   # Dokumentasi arsitektur & panduan eksekusi
```

---

## 🧩 Pembagian Tanggung Jawab (Separation of Concerns)

### 1. Model (`Model/Titipan.php`)
- **Peran & Data yang Dikelola:**
  - Mengelola data entitas **Room Titipan** (Nama Room, Host, Kontak, Rekening/QRIS Pembayaran, Target Resto, Batas Waktu Order).
  - Mengelola array data **Daftar Pesanan Titipan** (Nama penitip, menu yang dipesan, restoran, catatan kustom, nominal harga, status lunas/utang).
  - Menyediakan method manipulasi data: `tambahPesanan()` dan `toggleStatusBayar()`.
  - Menjalankan logika bisnis agregasi finansial `getStatistik()`:
    - Total nominal tagihan sirkel.
    - Total uang yang sudah lunas ditransfer.
    - Total sisa tagihan yang masih menunggak (utang).
    - Persentase penyelesaian pelunasan.
  - **Prinsip:** Tidak mengandung tag HTML atau styling CSS sama sekali.

### 2. Controller (`Controller/TitipanController.php`)
- **Peran & Proses yang Ditangani:**
  - Menangani aksi/request dari pengguna (`handleRequest`):
    - `action=index`: Memanggil data dari Model lalu menampilkan view dashboard.
    - `action=tambah`: Menerima POST data form pesanan baru dan menyimpannya melalui Model.
    - `action=toggle-bayar`: Mengubah status bayar pesanan secara instan antara Lunas dan Belum Bayar.
    - `action=reset`: Mengembalikan data contoh ke posisi awal untuk keperluan demonstrasi.
  - Melakukan transformasi data presentasi (helper `formatRupiah()`, styling badge status).
  - Menjalankan **Generator Tagihan WhatsApp Otomatis** (`generateWhatsAppText()`) yang menyusun rincian tagihan dalam format siap salin ke grup chat.
  - Menjalankan method `render()` untuk melempar payload data terstruktur ke View.
  - **Prinsip:** Menjembatani aksi pengguna dan Model tanpa menulis tampilan HTML langsung.

### 3. View (`View/titipan-page.php`)
- **Peran & Tampilan yang Disajikan:**
  - Menampilkan antarmuka responsif dengan desain modern khas NitipDong (Tailwind CSS, Plus Jakarta Sans, card elevasi, micro-animations).
  - Menampilkan banner status pemenuhan checklist MVC Pertemuan 7.
  - Menampilkan header informasi room dan metrik finansial real-time (total tagihan, uang masuk, uang utang, dan progress bar).
  - Formulir Entri Cepat untuk input pesanan titipan baru.
  - Grid kartu pesanan titipan dilengkapi tombol interaktif untuk mengubah status LUNAS / BELUM BAYAR.
  - Kotak Generator Tagihan WhatsApp dengan tombol satu klik salin (*one-click copy to clipboard*).
  - **Prinsip:** Murni bersifat presentasi (*presentation layer*), hanya menerima variabel siap pakai dari Controller.

---

## 🚀 Cara Menjalankan

### Opsi 1: Menggunakan PHP Built-in Server (Terminal)
Buka terminal pada direktori proyek:
```bash
cd week7
php -S localhost:8000
```
Buka browser dan akses:
```text
http://localhost:8000
```

### Opsi 2: Menggunakan XAMPP (Apache)
1. Salin folder `week7` ke dalam direktori `htdocs` (misalnya `C:\xampp\htdocs\` atau `/Applications/XAMPP/xamppfiles/htdocs/`).
2. Nyalakan service **Apache** melalui XAMPP Control Panel.
3. Buka browser dan akses:
   ```text
   http://localhost/week7/
   ```
