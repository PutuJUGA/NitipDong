# Pertemuan 6 - Pemrograman Berorientasi Objek (OOP) PHP: NitipDong 🍱

Modul praktikum **Week 6** untuk proyek web **NitipDong** (Aplikasi Titip Beli & Jasa Titip Sirkel Kampus/Kantor). Modul ini mengimplementasikan konsep fundamental **Object-Oriented Programming (OOP)** pada PHP untuk mengelola data pesanan titipan secara dinamis dan terstruktur.

---

## 🎯 Pemenuhan Ketentuan Tugas OOP PHP

| No | Ketentuan Tugas | Berkas / Baris Kode | Deskripsi & Implementasi di NitipDong |
|:---|:---|:---|:---|
| **1** | **Class & Enkapsulasi** | [`week6/Titipan.php`](Titipan.php#L29-L74) | Membuat class `Titipan` dengan properti ber-visibilitas `private` (`$id`, `$namaBarang`, `$pengirim`, `$lokasiBeli`, `$hargaBarang`, `$biayaTitip`, `$jumlah`, `$status`, `$catatan`, `$kategori`, `$waktuPesan`) dan `protected` (`$avatar`). |
| **2** | **Constructor (`__construct`)** | [`week6/Titipan.php`](Titipan.php#L76-L122) | Mendefinisikan method ajaib `__construct(...)` untuk menginisialisasi properti saat objek baru dibentuk (`new Titipan(...)`). Dilengkapi type-hinting dan validasi nilai bawaan. |
| **3** | **Method `hitungBiayaTotal()`** | [`week6/Titipan.php`](Titipan.php#L124-L146) | Menghitung akumulasi biaya pokok pesanan dan fee jastip: <br>`Total = (Harga Barang * Qty) + (Biaya Titip * Qty) + Ongkir - Diskon`. |
| **4** | **Method `getDetailTitipan()`** | [`week6/Titipan.php`](Titipan.php#L148-L182) | Mengembalikan array asosiatif terformat yang memuat seluruh rincian informasi barang titipan, format rupiah, dan penanda status. |
| **5** | **Getter & Setter (Encapsulation Accessors)** | [`week6/Titipan.php`](Titipan.php#L198-L300) | Menyediakan jalur resmi membaca (`getNamaBarang()`, `getStatus()`, dll) dan memutasi data (`setNamaBarang()`, `setStatus()`, dll) dengan validasi logika bisnis. |
| **6** | **Dynamic HTML Output** | [`week6/index.php`](index.php#L220-L400) | Menampilkan objek-objek titipan secara otomatis ke elemen HTML/Tailwind CSS melalui pemanggilan method PHP (`$item->getNamaBarang()`, `$item->hitungBiayaTotal()`, dll). |

---

## 🏗️ Struktur Berkas & Direktori

```text
week6/
├── Titipan.php     # CLASS & LOGIKA OOP: Definisi class Titipan, enkapsulasi, constructor, & methods
├── index.php       # DYNAMIC HTML OUTPUT: Antarmuka web modern dengan Tailwind CSS & interaktivitas objek
└── README.md       # DOKUMENTASI: Penjelasan lengkap materi praktikum OOP PHP
```

---

## 🧩 Bedah Konsep OOP yang Diterapkan

### 1. Class & Object
* **Class (`Titipan`)**: Merupakan *blueprint* atau cetak biru yang mendefinisikan apa saja atribut dan kemampuan yang dimiliki oleh sebuah pesanan titipan di NitipDong.
* **Object (`$titipan1`, `$titipan2`, dst)**: Merupakan wujud konkret (*instance*) yang dibuat dari class `Titipan`:
  ```php
  $titipan1 = new Titipan(
      'NTP-001',
      'Tahu Telor Bumbu Kacang Petis',
      'Dimas Kurnia',
      'Tahu Telor Budhe Yuni',
      20000,
      3000,
      1
  );
  ```

### 2. Encapsulation (Enkapsulasi)
Enkapsulasi membungkus data dan melindungi variabel agar tidak dapat diakses atau diubah secara liar dari luar class:
* Properti dideklarasikan dengan kata kunci `private` atau `protected`:
  ```php
  private string $namaBarang;
  private string $pengirim;
  private int $biayaTitip;
  private string $status;
  ```
* Untuk mengakses nilainya, wajib melalui **Getter**:
  ```php
  echo $titipan1->getNamaBarang();
  ```
* Untuk mengubah nilainya secara aman, wajib melalui **Setter** yang memiliki validasi:
  ```php
  $titipan1->setStatus('Selesai & Lunas');
  ```

### 3. Constructor (`__construct`)
Constructor dijalankan secara otomatis saat keyword `new Titipan(...)` dieksekusi. Berfungsi memastikan objek yang baru lahir langsung memiliki atribut lengkap:
```php
public function __construct(
    string $id,
    string $namaBarang,
    string $pengirim,
    string $lokasiBeli,
    int $hargaBarang,
    int $biayaTitip = 3000,
    int $jumlah = 1,
    string $status = 'Menunggu Dibeli',
    string $catatan = '-',
    string $kategori = 'Makanan',
    string $avatar = '🍱',
    ?string $waktuPesan = null
) {
    $this->id          = $id;
    $this->namaBarang  = trim($namaBarang);
    $this->pengirim    = trim($pengirim);
    $this->lokasiBeli  = trim($lokasiBeli);
    $this->hargaBarang = max(0, $hargaBarang);
    $this->biayaTitip  = max(0, $biayaTitip);
    $this->jumlah      = max(1, $jumlah);
    $this->status      = $status;
    $this->catatan     = $catatan;
    $this->kategori    = $kategori;
    $this->avatar      = $avatar;
    $this->waktuPesan  = $waktuPesan ?? (date('H:i') . ' WIB');
}
```

### 4. Method Utama
* **`hitungBiayaTotal(int $diskon = 0, int $ongkirBagi = 0): int`**
  Memproses perhitungan keuangan secara presisi:
  ```php
  $subtotalBarang = $this->hargaBarang * $this->jumlah;
  $totalJasaTitip = $this->biayaTitip * $this->jumlah;
  $total = ($subtotalBarang + $totalJasaTitip + $ongkirBagi) - $diskon;
  return max(0, $total);
  ```

* **`getDetailTitipan(): array`**
  Mengembalikan representasi array asosiatif lengkap beserta nominal terformat (Rupiah) dan penanda badge visual.

### 5. Dynamic HTML Output
Pada berkas `index.php`, data pesanan diiterasi menggunakan perulangan `foreach` dan dirender langsung ke dalam struktur elemen HTML:
```php
<?php foreach ($daftarObjekTitipan as $item): ?>
    <div class="card">
        <h4><?= htmlspecialchars($item->getPengirim()); ?></h4>
        <h5><?= htmlspecialchars($item->getNamaBarang()); ?></h5>
        <span>Status: <?= htmlspecialchars($item->getStatus()); ?></span>
        <div class="total">
            Total Tagihan: <?= $item->formatRupiah($item->hitungBiayaTotal()); ?>
        </div>
    </div>
<?php endforeach; ?>
```

---

## 🚀 Cara Menjalankan Berkas PHP

### Opsi 1: Menggunakan PHP Built-in Server (Terminal)
Jika di laptop Anda sudah terpasang PHP:
```bash
# 1. Masuk ke folder week6
cd week6

# 2. Jalankan server lokal
php -S localhost:8000
```
Buka browser pada alamat: **`http://localhost:8000`**

---

### Opsi 2: Menggunakan XAMPP (Apache)
1. Pastikan folder proyek berada di dalam direktori `htdocs` XAMPP:
   - **Windows:** `C:\xampp\htdocs\NitipDongProject\week6\`
   - **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/NitipDongProject/week6/`
2. Buka aplikasi **XAMPP Control Panel**, kemudian klik **Start** pada modul **Apache**.
3. Buka browser dan ketik alamat:
   **`http://localhost/NitipDongProject/week6/`**

---

## 🎨 Fitur Tambahan pada Antarmuka Week 6
1. **Formulir Interaktif Instansiasi Objek**: Anda dapat mengisi form secara langsung untuk membuat objek `new Titipan(...)` baru secara live.
2. **Kalkulasi Metrik Otomatis**: Menampilkan total tagihan keseluruhan dan total fee jastip sirkel yang diakumulasikan dari method `hitungBiayaTotal()`.
3. **Ubah Status Cepat (Setter)**: Mengubah status pesanan (*Sedang Dibelikan*, *Siap Diambil*, *Selesai & Lunas*) yang membuktikan penerapan *Setter Method* dengan validasi.
4. **Tabel Rekapitulasi**: Merender seluruh data hasil dari method `getDetailTitipan()`.

