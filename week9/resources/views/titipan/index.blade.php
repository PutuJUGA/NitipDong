<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NitipDong 🍱 - Week 9: Laravel CRUD & ORM</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen antialiased flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

    <!-- Navbar Header -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/20">
                    🍱
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight text-white">Nitip<span class="text-emerald-400">Dong</span></span>
                        <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Week 9 • Laravel ORM
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Aplikasi Titip Beli & Split Bill Sirkel Kampus</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="#modalTambah" onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition transform active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Titip Menu Baru</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-8">
        
        <!-- Flash Alert Notifikasi -->
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-lg font-bold">&times;</button>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm">
            <p class="font-bold mb-1">Ada kesalahan pengisian data:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Banner Info Tugas Week 9 -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950/60 via-slate-800/80 to-slate-900 border border-emerald-500/20 p-6 sm:p-8">
            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-xs font-semibold mb-3">
                    🚀 Tugas Praktikum Web: Pertemuan 9
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-2">
                    Manajemen Titipan Sirkel & Kalkulator Split Bill
                </h1>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Diimplementasikan dengan arsitektur <strong>Laravel 11/12 CRUD + Eloquent ORM</strong>. Terintegrasi penuh dengan 
                    migrasi database multi-tabel (<code class="text-emerald-400 text-xs">kategoris</code> & <code class="text-emerald-400 text-xs">titipans</code>), 
                    relasi <code class="text-emerald-400 text-xs">hasMany / belongsTo</code>, agregasi query, dan tampilan Blade modern.
                </p>
            </div>
            <div class="absolute -right-8 -bottom-10 text-9xl opacity-10 pointer-events-none select-none">
                🍱
            </div>
        </section>

        <!-- Ringkasan Statistik Split Bill (ORM Aggregation) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Biaya -->
            <div class="p-5 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span>Total Tagihan Sirkel</span>
                    <span class="text-lg">💰</span>
                </div>
                <div class="text-2xl font-black text-white tracking-tight">
                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Gabungan seluruh pesanan kawan</p>
            </div>

            <!-- Terkumpul / Lunas -->
            <div class="p-5 rounded-2xl bg-emerald-950/30 border border-emerald-500/30 hover:border-emerald-500/50 transition">
                <div class="flex items-center justify-between text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span>Sudah Dibayar (Lunas)</span>
                    <span class="text-lg">✅</span>
                </div>
                <div class="text-2xl font-black text-emerald-300 tracking-tight">
                    Rp {{ number_format($totalLunas, 0, ',', '.') }}
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <div class="flex-1 bg-slate-700/60 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $persenLunas }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-emerald-400">{{ $persenLunas }}%</span>
                </div>
            </div>

            <!-- Sisa Utang -->
            <div class="p-5 rounded-2xl bg-amber-950/30 border border-amber-500/30 hover:border-amber-500/50 transition">
                <div class="flex items-center justify-between text-amber-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span>Sisa Utang Belum Bayar</span>
                    <span class="text-lg">⏳</span>
                </div>
                <div class="text-2xl font-black text-amber-300 tracking-tight">
                    Rp {{ number_format($sisaUtang, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-amber-400/80 mt-1">Perlu ditagihkan ke penitip</p>
            </div>

            <!-- Total Item -->
            <div class="p-5 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 transition">
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                    <span>Total Pesanan Masuk</span>
                    <span class="text-lg">📦</span>
                </div>
                <div class="text-2xl font-black text-white tracking-tight">
                    {{ $titipans->count() }} <span class="text-sm font-semibold text-slate-400">Titipan</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Dari {{ $kategoris->count() }} kategori merchant</p>
            </div>
        </section>

        <!-- Filter & Search Toolbar -->
        <section class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-4">
            <form action="{{ route('titipan.index') }}" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Search input -->
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama penitip, menu, atau warung/kantin..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Filter Kategori & Status -->
                <div class="flex items-center gap-2 flex-wrap">
                    <select name="kategori_id" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->icon }} {{ $kategori->nama_kategori }} ({{ $kategori->titipans_count }})
                            </option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs sm:text-sm text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>⏳ Belum Lunas</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>✅ Lunas</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs sm:text-sm transition">
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'kategori_id', 'status']))
                    <a href="{{ route('titipan.index') }}" class="px-3 py-2.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 text-xs sm:text-sm font-semibold transition">
                        Reset Filter
                    </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- Daftar Pesanan Titipan (Tabel Responsif & Cards) -->
        <section class="rounded-3xl bg-slate-800/40 border border-slate-700/60 overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-700/60 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-white">Daftar Titipan Makanan & Belanjaan</h2>
                    <p class="text-xs text-slate-400">Dimuat secara efisien dengan Eloquent ORM Eager Loading (<code class="text-emerald-400">with('kategori')</code>)</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">
                    Total: {{ $titipans->count() }} Data
                </span>
            </div>

            @if($titipans->isEmpty())
                <div class="p-16 text-center">
                    <div class="text-6xl mb-3">🍽️</div>
                    <h3 class="text-lg font-bold text-white mb-1">Belum Ada Titipan</h3>
                    <p class="text-xs text-slate-400 mb-6">Belum ada pesanan titipan yang cocok dengan filter atau kriteria pencarian.</p>
                    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm transition">
                        + Tambah Titipan Pertama
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/60 text-slate-400 uppercase text-[11px] font-black tracking-wider border-b border-slate-700/60">
                            <tr>
                                <th class="py-4 px-6">Penitip</th>
                                <th class="py-4 px-6">Menu & Kategori</th>
                                <th class="py-4 px-6">Lokasi Beli</th>
                                <th class="py-4 px-6 text-right">Rincian Biaya</th>
                                <th class="py-4 px-6 text-right">Total Bayar</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($titipans as $item)
                            <tr class="hover:bg-slate-800/50 transition">
                                <!-- Penitip -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center font-black text-emerald-300 text-sm">
                                            {{ strtoupper(substr($item->nama_penitip, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $item->nama_penitip }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $item->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Menu & Kategori ORM -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-100">{{ $item->nama_barang }}</div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        @if($item->kategori)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-slate-700 text-slate-300 border border-slate-600">
                                                <span>{{ $item->kategori->icon }}</span>
                                                <span>{{ $item->kategori->nama_kategori }}</span>
                                            </span>
                                        @endif
                                        @if($item->catatan)
                                            <span class="text-[11px] text-slate-400 italic">"{{ Str::limit($item->catatan, 35) }}"</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Lokasi Beli -->
                                <td class="py-4 px-6 whitespace-nowrap text-slate-300 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span>📍</span>
                                        <span class="font-semibold">{{ $item->lokasi_beli }}</span>
                                    </div>
                                </td>

                                <!-- Rincian Biaya -->
                                <td class="py-4 px-6 whitespace-nowrap text-right text-xs">
                                    <div class="text-slate-300 font-medium">{{ $item->formatted_harga }} &times; {{ $item->jumlah }}</div>
                                    <div class="text-[11px] text-slate-400">+ Jastip {{ $item->formatted_jastip }}</div>
                                </td>

                                <!-- Total Bayar -->
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <div class="font-black text-emerald-400 text-base">
                                        {{ $item->formatted_total }}
                                    </div>
                                </td>

                                <!-- Status Badge & Toggle -->
                                <td class="py-4 px-6 whitespace-nowrap text-center">
                                    <form action="{{ route('titipan.toggle-status', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            title="Klik untuk ubah status pembayaran"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer {{ $item->isLunas() ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30 hover:bg-amber-500/30' }}"
                                        >
                                            @if($item->isLunas())
                                                <span>✓ Lunas</span>
                                            @else
                                                <span>⏳ Belum Bayar</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>

                                <!-- Aksi Hapus -->
                                <td class="py-4 px-6 whitespace-nowrap text-center">
                                    <form action="{{ route('titipan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pesanan titipan atas nama {{ $item->nama_penitip }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus pesanan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </main>

    <!-- Modal Form Tambah Titipan Baru (CRUD Create) -->
    <div id="modalTambah" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">📝</span>
                    <h3 class="text-lg font-black text-white">Tambah Titipan Baru</h3>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('titipan.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Nama Penitip -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Nama Penitip</label>
                    <input type="text" name="nama_penitip" required placeholder="Contoh: Radiz / Putu / Fathan" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Kategori ORM -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Kategori Merchant (Relasi ORM)</label>
                    <select name="kategori_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->icon }} {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Barang -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Nama Barang / Makanan</label>
                    <input type="text" name="nama_barang" required placeholder="Contoh: Nasi Goreng Spesial Telur Ceplok" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Lokasi Beli -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Lokasi Beli / Resto</label>
                    <input type="text" name="lokasi_beli" required placeholder="Contoh: Kantin Vokasi Lt. 2 / Warung Bu Imas" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Harga & Jumlah -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_barang" required min="0" placeholder="15000" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Jumlah (Porsi/Item)</label>
                        <input type="number" name="jumlah" required min="1" value="1" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <!-- Biaya Jastip -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Biaya Jastip / Ongkir Sirkel (Rp)</label>
                    <input type="number" name="biaya_jastip" min="0" value="2000" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Catatan Khusus (Opsional)</label>
                    <textarea name="catatan" rows="2" placeholder="Contoh: Pedas level 1, jangan pakai seledri" class="w-full px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition">
                        Simpan Titipan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 mt-12 py-6 text-center text-xs text-slate-500">
        <p class="font-medium">NitipDong 🍱 &copy; 2026 • Praktikum Pemrograman Web (Pertemuan 9 - Laravel CRUD + ORM)</p>
    </footer>

</body>
</html>

