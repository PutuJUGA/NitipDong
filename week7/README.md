# Pertemuan 7 - Web Programming: Arsitektur Model-View-Controller (MVC)

Implementasi pola arsitektur **Model-View-Controller (MVC)** yang disesuaikan 100% dengan tema proyek **NitipDong 🍱** (Aplikasi Titip Beli Makanan & Split Bill Sirkel Kampus/Kantor).

---

## 🎯 Pemenuhan 3 Komponen Utama MVC

Sesuai instruksi praktikum, sistem telah dipisahkan secara tegas berdasarkan prinsip *Separation of Concerns*:

| Komponen | Berkas / Direktori | Tanggung Jawab & Implementasi |
| :--- | :--- | :--- |
| **1. MODEL** | [`week7/Model/Titipan.php`](Model/Titipan.php) | Berisi class `Titipan`, properti data room & pesanan sirkel, method manipulasi data (CRUD), logika bisnis kalkulasi keuangan split bill, serta persistensi session. |
| **2. CONTROLLER** | [`week7/Controller/TitipanController.php`](Controller/TitipanController.php) | Berisi class `TitipanController` yang menerima request HTTP (`handleRequest`), mengeksekusi method (`index()`, `show()`, `process()`), memanggil Model, dan meneruskan data terstruktur ke View. |
| **3. VIEW** | [`week7/View/view-titipan.php`](View/view-titipan.php)<br>[`week7/View/titipan-page.php`](View/titipan-page.php)<br>[`week7/View/titipan-detail.php`](View/titipan-detail.php) | Menerima variabel data siap pakai dari Controller (`extract($data)`) dan menampilkannya dalam format antarmuka HTML modern, responsif, dan rapi ke browser menggunakan Tailwind CSS. |

---

## 📊 Diagram Alur Sistem: Request ➔ Controller ➔ Model ➔ View

```text
  [ USER / BROWSER ]
          │
          ▼ (HTTP GET / POST)
   [ index.php (Front Controller) ]
          │
          ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          TITIPAN CONTROLLER                             │
│                       (TitipanController.php)                           │
│                                                                         │
│   ├── handleRequest() ➔ Routing parameter action / id / form POST       │
│   ├── index()         ➔ Ambil seluruh pesanan, format data & render UI  │
│   ├── show($id)       ➔ Ambil detail rincian pesanan ID & render invoice│
│   ├── process($act)   ➔ Proses tambah, toggle bayar, hapus, reset data │
│   └── render($view)   ➔ Extract data payload & include berkas View      │
└───────────────────┬─────────────────────────────────┬───────────────────┘
                    │                                 │
                    │ 1. Panggil Model                │ 2. Teruskan Data
                    ▼                                 ▼
┌──────────────────────────────────────┐  ┌───────────────────────────────┐
│               MODEL                  │  │             VIEW              │
│           (Titipan.php)              │  │     (view-titipan.php &       │
│                                      │  │     titipan-detail.php)       │
│  - Properti: $roomInfo,              │  │                               │
│              $daftarPesanan          │  │  - Render HTML Tailwind rapi  │
│  - Method:                           │  │  - Hero Info Room & Deadline  │
│    • getRoomInfo()                   │  │  - Kartu Metrik Keuangan      │
│    • getAllPesanan()                 │  │  - Filter & Grid Kartu Pesanan│
│    • getPesananById($id)             │  │  - Form Tambah Cepat          │
│    • tambahPesanan(...)              │  │  - Tombol Ubah Status Lunas   │
│    • toggleStatusBayar($id)          │  │  - Generator Pesan WhatsApp   │
│    • hapusPesanan($id)               │  │  - Detail Invoice Personal    │
│    • getStatistik()                  │  └───────────────┬───────────────┘
│    • simpanKeSession()               │                  │
└──────────────────────────────────────┘                  │ Render Tampilan
                                                          ▼
                                                  [ TAMPILAN BROWSER ]
```

---

## 🏗️ Struktur Berkas & Direktori Proyek

```text
week7/
├── Controller/
│   └── TitipanController.php    # CONTROLLER: Request handler, method index/show/process, data formatting
├── Model/
│   └── Titipan.php              # MODEL: Entitas room, daftar pesanan sirkel, logika kalkulasi split bill
├── View/
│   ├── view-titipan.php         # VIEW UTAMA: Alias view-titipan yang memuat halaman dashboard utama
│   ├── titipan-page.php         # VIEW DASHBOARD: Desain HTML antarmuka daftar titipan, form input, generator WA
│   ├── titipan-detail.php       # VIEW DETAIL: Desain HTML rincian pesanan penitip (dipanggil oleh show())
│   └── index.php                # VIEW ALIAS: Alias view utama
├── index.php                    # ENTRY POINT: Front Controller yang menginisialisasi TitipanController
└── README.md                    # Dokumentasi pemenuhan tugas praktikum MVC
```

---

## 🧩 Penjelasan Lengkap Komponen MVC

### 1. MODEL (`Model/Titipan.php`)
Model bertanggung jawab murni atas data dan logika bisnis (tanpa markup presentasi HTML/CSS):
* **Class:** `Titipan`
* **Properti Data:**
  * `$roomInfo` (array): Menyimpan data room titipan (`idRoom`, `namaRoom`, `host`, `kontakHost`, `rekeningHost`, `targetResto`, `deadlineOrder`, `statusRoom`).
  * `$daftarPesanan` (array): Koleksi data titipan rekan sirkel (`id`, `namaPenitip`, `menu`, `resto`, `catatan`, `harga`, `isLunas`, `metodeBayar`, `waktuInput`, `avatar`).
* **Method Pengelolaan Data & Logika Bisnis:**
  * `getRoomInfo(): array`: Mengembalikan metadata host dan rekening room.
  * `getAllPesanan(): array`: Mengembalikan seluruh koleksi daftar titipan.
  * `getPesananById(int $id): ?array`: Mengembalikan satu pesanan spesifik berdasarkan ID.
  * `tambahPesanan(string $nama, string $menu, string $resto, int $harga, string $catatan): bool`: Validasi dan menambah entri titipan baru dengan ID inkremental otomatis dan avatar emoji acak.
  * `toggleStatusBayar(int $id): bool`: Mengubah status pembayaran (Lunas $\leftrightarrow$ Belum Bayar/Utang).
  * `hapusPesanan(int $id): bool`: Membatalkan atau menghapus pesanan tertentu.
  * `resetData(): void`: Menghapus session dan mengembalikan data ke daftar demo bawaan NitipDong.
  * `getStatistik(): array`: Menghitung total tagihan, jumlah lunas, sisa utang, persentase pelunasan, dan rata-rata pengeluaran per orang.
  * `simpanKeSession(): void`: Menyimpan perubahan data ke dalam `$_SESSION['nitipdong_pesanan']` agar mutasi data tersimpan nyata antar-request.

---

### 2. CONTROLLER (`Controller/TitipanController.php`)
Controller bertindak sebagai perantara yang menerima input pengguna, berinteraksi dengan Model, dan memilih View:
* **Class:** `TitipanController`
* **Properti:** `private Titipan $model`
* **Method:**
  * `handleRequest(): void`: **Router/Dispatcher** yang membaca `$_GET['action']` dan mengarahkan alur ke method yang sesuai (`index`, `show`, `process`).
  * `index(): void`: Menangani tampilan utama. Memanggil Model (`getRoomInfo`, `getAllPesanan`, `getStatistik`), menerapkan filter opsional (semua / lunas / belum bayar), memformat data rupiah dan badge status, lalu memanggil `render('titipan-page', $viewData)`.
  * `show(int $id): void`: Menangani rincian satu pesanan. Memanggil Model (`getPesananById`), menyusun template teks WhatsApp reminder personal, lalu memanggil `render('titipan-detail', $viewData)`.
  * `process(?string $action): void`: Menangani request mutasi data (tambah pesanan dari form POST, toggle bayar, hapus, reset demo), mengatur flash message notifikasi di session, dan melakukan redirect aman ke `index.php`.
  * `render(string $viewName, array $data = []): void`: Mengekstrak array data menjadi variabel lokal (`extract($data)`) lalu me-require berkas View yang dituju.
  * `generateWhatsAppText(...)`: Helper untuk menghasilkan pesan rekap tagihan seluruh sirkel yang siap disalin ke grup WhatsApp.
  * `generatePersonalWhatsAppReminder(...)`: Helper untuk menghasilkan teks reminder tagihan personal ke penitip yang belum bayar.

---

### 3. VIEW (`View/view-titipan.php`, `View/titipan-page.php`, `View/titipan-detail.php`)
View murni menangani presentasi visual antarmuka pengguna ke browser:
* **Variabel yang Diterima:** `$room`, `$pesanan`, `$stat`, `$waText`, `$filterAktif`, `$pesanNotif`, `$item`, `$personalWaText`.
* **Fitur Antarmuka yang Ditampilkan:**
  * **Header & Indikator Status MVC:** Menampilkan identitas berkas aktif Controller, Model, dan View.
  * **Room Banner & Statistik Keuangan Split Bill:** Kartu Total Tagihan, Terkumpul (Lunas), Sisa Utang, Rata-Rata per Orang, dan Bar Progres Pelunasan Dinamis.
  * **Formulir Entri Cepat:** Form input tambah titipan yang terhubung ke controller via method POST.
  * **Katalog Titipan Teman:** Grid kartu interaktif dengan badge status pembayaran, nomor urut otomatis, tombol **Detail** (`show()`), tombol **Tandai Lunas/Utang** (`process()`), dan tombol **Hapus**.
  * **Generator Tagihan WhatsApp Sirkel:** Kotak teks rekap siap salin dalam 1 klik (*copy-to-clipboard*) dan tombol pintasan buka aplikasi WhatsApp.
  * **Halaman Invoice Detail (`titipan-detail.php`):** Tampilan rincian invoice individu penitip, informasi rekening transfer host, dan template pengingat WhatsApp personal.

---

### 4. FRONT CONTROLLER (`index.php`)
Sebagai *single entry point* aplikasi web:
```php
<?php
require_once __DIR__ . '/Controller/TitipanController.php';

$controller = new TitipanController();
$controller->handleRequest();
```

---

## ✅ Checklist Evaluasi Praktikum MVC

| Kriteria Praktikum | Status | Bukti Implementasi |
| :--- | :---: | :--- |
| **1. Folder & File Model Tersedia** | ✅ | Folder `Model/` dan berkas `Titipan.php` berisi class data, properti, dan method pengelolaan titipan. |
| **2. Folder & File Controller Tersedia** | ✅ | Folder `Controller/` dan berkas `TitipanController.php` menerima request, memanggil model, dan meneruskan data. |
| **3. Folder & File View Tersedia** | ✅ | Folder `View/` dan berkas `view-titipan.php` / `titipan-page.php` / `titipan-detail.php` menampilkan HTML rapi ke browser. |
| **4. Controller Berjalan & Routing Request** | ✅ | Method `handleRequest()` menangani parameter `action`, `id`, `filter`, dan `$_POST`. |
| **5. Method index(), show(), process() Lengkap** | ✅ | Berjalan penuh dengan redirect & flash message notification. |
| **6. Model Terintegrasi & Fungsional** | ✅ | Operasi CRUD, penghitungan statistik, dan persistensi session berjalan optimal. |
| **7. Tampilan HTML Rapi & Responsif** | ✅ | Menggunakan Tailwind CSS, kartu metrik, progress bar, badge status, dan typography modern. |

---

## 🚀 Cara Menjalankan Berkas MVC

### Opsi 1: Menggunakan PHP Built-in Server (Terminal)
Buka terminal dan arahkan ke direktori `week7`:
```bash
cd "week7"
php -S localhost:8000
```
Buka browser pada alamat:
```text
http://localhost:8000
```

### Opsi 2: Menggunakan XAMPP (Apache)
1. Salin atau pastikan folder proyek berada di direktori web root XAMPP (misal `C:\xampp\htdocs\`).
2. Aktifkan modul **Apache** melalui XAMPP Control Panel.
3. Buka browser pada alamat:
   ```text
   http://localhost/NitipDong-main/week7/
   ```

---

## 💡 Panduan Uji Coba Fitur di Browser

1. **Dashboard Utama (`index()`):** Akses `http://localhost:8000/`. Dashboard menampilkan rekap room, metrik keuangan split bill, daftar pesanan, dan form entri cepat.
2. **Tambah Titipan Baru (`process('tambah')`):** Isi Nama Penitip, Menu, Resto, dan Harga di form atas, lalu klik **Tambah Titipan**. Data akan bertambah dan metrik keuangan terhitung ulang secara instan.
3. **Ubah Status Lunas / Utang (`process('toggle-bayar')`):** Klik tombol **Tandai Lunas ✓** atau **Ubah ke Utang** pada salah satu pesanan. Status badge dan progress bar akan langsung diperbarui.
4. **Lihat Rincian / Detail Pesanan (`show($id)`):** Klik tombol **🔍 Detail** pada salah satu pesanan. Anda akan diarahkan ke `index.php?action=show&id=X` yang menampilkan rincian invoice dan teks tagihan personal.
5. **Salin Tagihan WhatsApp:** Klik tombol **Salin Teks WhatsApp** di bagian bawah untuk menyalin seluruh rekap tagihan sirkel ke clipboard.
6. **Reset Data:** Klik tombol **🔄 Reset Data Demo** untuk mengembalikan seluruh pesanan ke data awal NitipDong.
