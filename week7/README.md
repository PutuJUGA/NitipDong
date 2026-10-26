# Pertemuan 7 - Web Programming: Task 02: Build the Controller

Implementasi arsitektur **Controller** pada pola **Model-View-Controller (MVC)** yang disesuaikan 100% dengan tema proyek **NitipDong 🍱** (Aplikasi Titip Beli Makanan & Split Bill Sirkel Kampus/Kantor).

---

## 🎯 Pemenuhan Tugas Praktikum (Task 02)

Sesuai materi slide perkuliahan **Pertemuan 7 - Web Programming: Task 02 Build the Controller**:
> *"Buat Controller yang menangani request halaman PresidenKu dan mengarahkan proses ke bagian yang sesuai."*  
> *(Diimplementasikan secara kontekstual & fungsional penuh pada tema **NitipDong**)*

### 📊 Diagram Alur Request ➔ Controller ➔ View:
```text
  [ USER ]
     │
     ▼ (HTTP GET / POST)
  [ REQUEST ]
     │
     ▼
┌─────────────────────────────────────────────────────────────┐
│                 TITIPAN CONTROLLER                          │
│               (TitipanController.php)                       │
│                                                             │
│   ├── index()   ➔ Ambil seluruh pesanan & render dashboard   │
│   ├── show()    ➔ Ambil rincian pesanan ID & render detail   │
│   └── process() ➔ Proses tambah, toggle bayar, hapus, reset │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Panggil Model / Mutasi Data)
                       ▼
                 [ MODEL: Titipan ]
                       │
                       ▼
                 [ VIEW: titipan-page / titipan-detail ]
```

---

## ✅ Checklist Evaluasi Task 02

| Checklist | Status | Implementasi pada NitipDong |
| :--- | :---: | :--- |
| **Controller berjalan** | ✅ | Berkas `week7/Controller/TitipanController.php` berjalan sebagai kelas controller OOP. |
| **Request diterima** | ✅ | Method `handleRequest()` menangani query parameters (`action`, `id`, `filter`) dan form data `$_POST`. |
| **Method dapat dijalankan** | ✅ | Tersedia dan berjalan penuh method `index()`, `show($id)`, dan `process($action)`. |
| **Model dapat dipanggil** | ✅ | Controller memanggil `Titipan::getRoomInfo()`, `getAllPesanan()`, `getPesananById()`, `tambahPesanan()`, `toggleStatusBayar()`, `hapusPesanan()`, `getStatistik()`. |
| **View dapat ditampilkan** | ✅ | Controller meneruskan payload data terstruktur ke View `titipan-page.php` (dashboard) dan `titipan-detail.php` (invoice rincian). |

---

## 🏗️ Struktur Berkas MVC NitipDong

```text
week7/
├── Controller/
│   └── TitipanController.php    # CONTROLLER: Request handling, method index/show/process, data formatting
├── Model/
│   └── Titipan.php              # MODEL: Entitas room, daftar pesanan sirkel, logika kalkulasi split bill
├── View/
│   ├── titipan-page.php         # VIEW 1: Dashboard utama daftar titipan, form input, generator WhatsApp
│   └── titipan-detail.php       # VIEW 2: Tampilan detail rincian pesanan penitip (dipanggil oleh show())
├── index.php                    # ENTRY POINT: Front Controller yang menginisialisasi TitipanController
└── README.md                    # Dokumentasi pemenuhan tugas praktikum
```

---

## 🧩 Penjelasan Lengkap Method Controller (`TitipanController.php`)

### 1. `handleRequest(): void`
- Berperan sebagai **router internal / request dispatcher**.
- Membaca parameter `$_GET['action']`:
  - `action=index` (default): Menjalankan method `index()`.
  - `action=show`: Mengambil `id` dari `$_GET['id']` lalu menjalankan method `show($id)`.
  - `action=process`: Mengambil sub-aksi (`tambah`, `toggle-bayar`, `hapus`, `reset`) lalu menjalankan `process($action)`.
  - Pintasan langsung `action=tambah`, `action=toggle-bayar`, dsb. otomatis dialihkan ke method `process()`.

### 2. `index(): void`
- Menangani request tampilan utama dashboard titipan.
- **Memanggil Model**:
  - `$this->model->getRoomInfo()` untuk data host & deadline.
  - `$this->model->getAllPesanan()` untuk katalog pesanan teman.
  - `$this->model->getStatistik()` untuk kalkulasi split bill (total tagihan, terkumpul, sisa utang).
- **Pemrosesan Data Presentasi**:
  - Format angka rupiah (`formatRupiah()`).
  - Penomoran dinamis `#01`, `#02`, dst.
  - Penentuan styling Tailwind badge status (`LUNAS` warna hijau, `BELUM BAYAR` warna merah).
  - Penyusunan template pesan siaran WhatsApp kolektif (`generateWhatsAppText()`).
- **Meneruskan Data ke View**:
  - Memanggil `$this->render('titipan-page', $viewData)`.

### 3. `show(int $id): void`
- Menangani request untuk melihat rincian / detail pesanan spesifik dari satu orang penitip.
- **Memanggil Model**:
  - `$this->model->getPesananById($id)`.
  - `$this->model->getRoomInfo()`.
- **Pemrosesan Data**:
  - Menyiapkan invoice ringkas pesanan, detail catatan kustom, instruksi nomor rekening host.
  - Menyusun pesan pengingat WhatsApp personal (`generatePersonalWhatsAppReminder()`) untuk menagih teman yang belum lunas.
- **Meneruskan Data ke View**:
  - Memanggil `$this->render('titipan-detail', $viewData)`.

### 4. `process(?string $action): void`
- Mengimplementasikan alur: `REQUEST -> CONTROLLER (process()) -> PROCESS -> REDIRECT / VIEW`.
- Memproses berbagai macam aksi mutasi data:
  - **`tambah`**: Memvalidasi form input POST (Nama, Menu, Resto, Harga, Catatan), lalu memanggil `$this->model->tambahPesanan()`.
  - **`toggle-bayar`**: Memperbarui status pembayaran antara lunas dan belum bayar via `$this->model->toggleStatusBayar($id)`.
  - **`hapus`**: Membatalkan pesanan teman via `$this->model->hapusPesanan($id)`.
  - **`reset`**: Mengembalikan seluruh data ke kondisi awal via `$this->model->resetData()`.
- Menetapkan Flash Notification di Session (`$_SESSION['flash_notif']`) dan melakukan redirect aman ke `index.php`.

### 5. `render(string $viewName, array $data = []): void`
- Mengisolasi logika rendering view.
- Mengekstrak data array menjadi variabel PHP lokal (`extract($data)`).
- Me-require berkas view dari folder `View/`.

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
1. Pastikan folder repositori atau folder `week7` berada di dalam direktori `C:\xampp\htdocs\`.
2. Nyalakan modul **Apache** melalui XAMPP Control Panel.
3. Buka browser pada alamat:
   ```text
   http://localhost/NitipDong-main/week7/
   ```
   *(atau `http://localhost/week7/` jika folder disalin langsung ke root htdocs)*

---

## 💡 Uji Coba Fitur Controller Langsung di Browser

1. **Uji Method `index()`**: Buka `http://localhost:8000/index.php`. Halaman dashboard titipan dan ringkasan split bill akan tampil.
2. **Uji Method `process()` (Tambah)**: Isi formulir entri cepat di bagian atas dengan nama teman baru dan klik **Tambah Titipan**. Pesanan baru akan langsung bertambah dengan kalkulasi total otomatis.
3. **Uji Method `process()` (Toggle Bayar)**: Klik tombol **Tandai Lunas ✓** pada salah satu pesanan yang berstatus belum bayar. Status akan berganti menjadi lunas, kartu pesanan menjadi hijau, dan progress bar bertambah.
4. **Uji Method `show()` (Detail)**: Klik tombol **🔍 Detail** pada salah satu pesanan. Browser akan diarahkan ke `index.php?action=show&id=1` yang menampilkan invoice rincian pesanan dan teks tagihan WhatsApp personal.
