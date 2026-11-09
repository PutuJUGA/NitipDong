# NitipDong 🍱

> **Gak Pusing Lagi Ngurusin Titipan Makanan Temen!**  
> Web application modern untuk kumpulin pesanan bareng, auto-split ongkir & diskon secara adil, dan pantau status pembayaran teman tanpa drama.

---

## 🛠️ Tech Stack
- **Framework:** [Vue.js 3](https://vuejs.org/) (Composition API, `<script setup>`)
- **Build Tool:** [Vite](https://vite.dev/)
- **Styling:** [Tailwind CSS v4](https://tailwindcss.com/)
- **Typography:** [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans)

---

## 🚀 Menjalankan Proyek Secara Lokal

1. **Install dependensi:**
   ```bash
   npm install
   ```

2. **Jalankan development server (Frontend Vue):**
   ```bash
   npm run dev
   ```
   Akses aplikasi melalui browser di `http://localhost:5173`.

3. **Build untuk production:**
   ```bash
   npm run build
   ```

---

## 📁 Struktur Direktori & Modul Praktikum
```text
NitipDong/
├── index.html          # HTML entry point & font links
├── package.json        # Dependensi proyek
├── vite.config.js      # Konfigurasi Vite & Tailwind plugin
├── public/
│   └── favicon.svg     # Brand logo favicon
├── src/
│   ├── App.vue         # Halaman utama (Landing Page NitipDong)
│   ├── main.js         # Entry point Vue app
│   └── style.css       # Tailwind CSS base styles
├── week4 - API/        # Week 4: REST API & Explorer
├── week5 - PHP/        # Week 5: Server-Side Rendering (PHP Prosedural)
├── week6/              # Pertemuan 6: Pemrograman Berorientasi Objek (OOP PHP)
│   ├── Titipan.php     # Class Titipan, Enkapsulasi, Constructor & Methods
│   ├── index.php       # Dynamic HTML Output render dari method-method objek
│   └── README.md       # Panduan materi OOP PHP & instruksi eksekusi
├── week7/              # Pertemuan 7: Arsitektur MVC (Model-View-Controller)
│   ├── Controller/     # TitipanController.php (Menerima request, panggil model, teruskan ke view)
│   ├── Model/          # Titipan.php (Class entitas, properti & logika bisnis split bill)
│   ├── View/           # view-titipan.php, titipan-page.php, titipan-detail.php (Format HTML rapi)
│   ├── index.php       # Entry Point / Front Controller
│   └── README.md       # Dokumentasi & panduan lengkap modul MVC
└── week9/              # Pertemuan 9: Laravel CRUD + Eloquent ORM
    ├── app/Http/       # TitipanController.php (method index & CRUD)
    ├── app/Models/     # Kategori.php & Titipan.php (Relasi hasMany & belongsTo)
    ├── database/       # Migrations multi-tabel (kategoris & titipans) & Seeders
    ├── resources/      # index.blade.php (View Index Tailwind modern)
    ├── routes/         # web.php (Route index & CRUD)
    └── README.md       # Dokumentasi lengkap pemenuhan 7 langkah tugas
```
