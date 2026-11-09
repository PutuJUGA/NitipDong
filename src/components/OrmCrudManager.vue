<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Database,
  Plus,
  Search,
  Filter,
  CheckCircle2,
  Clock,
  Trash2,
  RefreshCw,
  Server,
  ArrowRight,
  ExternalLink,
  Code2,
  Check,
  AlertCircle,
  FolderGit2,
  Sparkles,
  ShoppingBag
} from 'lucide-vue-next'

const props = defineProps({
  currentTheme: {
    type: Object,
    default: () => ({
      primary: '#f97316',
      primaryHover: '#ea580c',
      lightBg: '#fff7ed',
      border: '#fed7aa',
      shadow: 'rgba(249, 115, 22, 0.25)'
    })
  }
})

const emit = defineEmits(['add-to-room'])

// --- STATE MANAGEMENT ---
const activeTab = ref('crud') // 'crud' | 'schema' | 'checklist'
const backendStatus = ref('checking') // 'checking' | 'connected' | 'offline'
const isLoading = ref(false)
const searchQuery = ref('')
const selectedKategori = ref('all')
const statusFilter = ref('all')
const showAddModal = ref(false)
const notification = ref(null)

// Kategori default NitipDong (Relasi Induk ORM)
const kategoris = ref([
  { id: 1, nama_kategori: 'Makanan Berat', slug: 'makanan-berat', icon: '🍛', deskripsi: 'Porsi kenyang ayam, bebek, soto, mie' },
  { id: 2, nama_kategori: 'Minuman & Kopi', slug: 'minuman-kopi', icon: '☕', deskripsi: 'Kopi susu, es teh manis, matcha latte' },
  { id: 3, nama_kategori: 'Snack & Camilan', slug: 'snack-camilan', icon: '🍟', deskripsi: 'Gorengan, pastry, dimsum, toast' },
  { id: 4, nama_kategori: 'Jastip Minimarket', slug: 'jastip-minimarket', icon: '🏪', deskripsi: 'Air mineral, snack kemasan, alat tulis' }
])

// Data titipan (Seed data awal / tersimpan di localStorage / sinkron via API)
const defaultTitipans = [
  {
    id: 1,
    kategori_id: 1,
    nama_penitip: 'Syauqi Faradiz',
    nama_barang: 'Ayam Geprek Sambal Korek Level 3 + Nasi',
    lokasi_beli: 'Kantin Teknik Lt. 1',
    harga_barang: 16000,
    biaya_jastip: 2000,
    jumlah: 1,
    total_bayar: 18000,
    status_pembayaran: 'lunas',
    catatan: 'Sambal dipisah ya, timun dibanyakin.',
    created_at: '2026-11-09 12:00:00'
  },
  {
    id: 2,
    kategori_id: 2,
    nama_penitip: 'Lalu Fathan',
    nama_barang: 'Es Kopi Susu Gula Aren Double Shot',
    lokasi_beli: 'Kopi Kenangan Fakultas',
    harga_barang: 18000,
    biaya_jastip: 2000,
    jumlah: 1,
    total_bayar: 20000,
    status_pembayaran: 'belum_lunas',
    catatan: 'Less sugar 50%, less ice.',
    created_at: '2026-11-09 12:05:00'
  },
  {
    id: 3,
    kategori_id: 1,
    nama_penitip: 'Putu Oka',
    nama_barang: 'Nasi Padang Rendang Daging + Perkedel',
    lokasi_beli: 'RM Minang Saiyo',
    harga_barang: 24000,
    biaya_jastip: 3000,
    jumlah: 1,
    total_bayar: 27000,
    status_pembayaran: 'lunas',
    catatan: 'Kuah gulai dan daun singkong banjir.',
    created_at: '2026-11-09 12:10:00'
  },
  {
    id: 4,
    kategori_id: 3,
    nama_penitip: 'Nadia Putri',
    nama_barang: 'Croissant Cokelat Keju & Matcha Latte',
    lokasi_beli: 'Bakery Corner Gedung A',
    harga_barang: 25000,
    biaya_jastip: 2500,
    jumlah: 1,
    total_bayar: 27500,
    status_pembayaran: 'belum_lunas',
    catatan: 'Minta dipanaskan sebentar pas diambil.',
    created_at: '2026-11-09 12:15:00'
  },
  {
    id: 5,
    kategori_id: 4,
    nama_penitip: 'Dewi Lestari',
    nama_barang: 'Air Mineral 1.5L + Sticky Notes Pastel',
    lokasi_beli: 'Koperasi Mahasiswa',
    harga_barang: 11000,
    biaya_jastip: 1500,
    jumlah: 1,
    total_bayar: 12500,
    status_pembayaran: 'belum_lunas',
    catatan: 'Sticky notes yang warna ungu atau pink.',
    created_at: '2026-11-09 12:20:00'
  }
]

const titipans = ref([])

// Form Tambah Titipan State
const newForm = ref({
  nama_penitip: '',
  kategori_id: 1,
  nama_barang: '',
  lokasi_beli: '',
  harga_barang: 15000,
  biaya_jastip: 2000,
  jumlah: 1,
  catatan: ''
})

// Format Rupiah
const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val || 0)
}

// Show Toast Notifikasi
const showToast = (text, type = 'success') => {
  notification.value = { text, type }
  setTimeout(() => {
    notification.value = null
  }, 4000)
}

// Ambil Kategori Object berdasarkan ID
const getKategori = (id) => {
  return kategoris.value.find((k) => k.id === Number(id)) || {
    nama_kategori: 'Umum',
    icon: '📦'
  }
}

// Cek Koneksi ke Laravel Backend API
const checkBackendConnection = async () => {
  backendStatus.value = 'checking'
  try {
    const res = await fetch('http://127.0.0.1:8000/api/health', { method: 'GET' })
    if (res.ok) {
      backendStatus.value = 'connected'
      await fetchTitipansFromBackend()
    } else {
      backendStatus.value = 'offline'
    }
  } catch {
    backendStatus.value = 'offline'
  }
}

// Fetch Data Titipan dari Backend Laravel
const fetchTitipansFromBackend = async () => {
  isLoading.value = true
  try {
    const res = await fetch('http://127.0.0.1:8000/api/titipans')
    if (res.ok) {
      const json = await res.json()
      if (json.data && Array.isArray(json.data)) {
        titipans.value = json.data
      }
      if (json.kategoris && Array.isArray(json.kategoris)) {
        kategoris.value = json.kategoris
      }
    }
  } catch (err) {
    console.warn('Gagal fetch dari backend Laravel, fallback ke lokal:', err)
  } finally {
    isLoading.value = false
  }
}

// Simpan Titipan Baru (Create)
const handleCreateTitipan = async () => {
  if (!newForm.value.nama_penitip || !newForm.value.nama_barang || !newForm.value.lokasi_beli) {
    alert('Harap isi nama penitip, nama barang, dan lokasi resto!')
    return
  }

  const harga = Number(newForm.value.harga_barang) || 0
  const qty = Number(newForm.value.jumlah) || 1
  const jastip = Number(newForm.value.biaya_jastip) || 0
  const total = (harga * qty) + jastip

  const payload = {
    kategori_id: Number(newForm.value.kategori_id),
    nama_penitip: newForm.value.nama_penitip.trim(),
    nama_barang: newForm.value.nama_barang.trim(),
    lokasi_beli: newForm.value.lokasi_beli.trim(),
    harga_barang: harga,
    biaya_jastip: jastip,
    jumlah: qty,
    total_bayar: total,
    status_pembayaran: 'belum_lunas',
    catatan: newForm.value.catatan.trim(),
    created_at: new Date().toISOString()
  }

  // Coba POST ke backend jika online
  if (backendStatus.value === 'connected') {
    try {
      const res = await fetch('http://127.0.0.1:8000/api/titipans', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      })
      if (res.ok) {
        const json = await res.json()
        titipans.value.unshift(json.data || { ...payload, id: Date.now() })
        showToast('Titipan berhasil disimpan ke Database Laravel ORM! 🍱')
      } else {
        throw new Error('Gagal simpan ke backend')
      }
    } catch {
      payload.id = Date.now()
      titipans.value.unshift(payload)
      saveToLocalStorage()
      showToast('Tersimpan di database lokal (Backend offline) 🍱')
    }
  } else {
    payload.id = Date.now()
    titipans.value.unshift(payload)
    saveToLocalStorage()
    showToast('Titipan baru berhasil ditambahkan! 🍱')
  }

  // Reset form & close modal
  newForm.value = {
    nama_penitip: '',
    kategori_id: 1,
    nama_barang: '',
    lokasi_beli: '',
    harga_barang: 15000,
    biaya_jastip: 2000,
    jumlah: 1,
    catatan: ''
  }
  showAddModal.value = false
}

// Toggle Status Lunas / Belum (Update)
const handleToggleStatus = async (item) => {
  const statusBaru = item.status_pembayaran === 'lunas' ? 'belum_lunas' : 'lunas'
  item.status_pembayaran = statusBaru

  if (backendStatus.value === 'connected') {
    try {
      await fetch(`http://127.0.0.1:8000/api/titipans/${item.id}/toggle`, {
        method: 'PATCH',
        headers: { 'Accept': 'application/json' }
      })
    } catch (e) {
      console.warn('Gagal sync status ke backend:', e)
    }
  }
  saveToLocalStorage()
  showToast(statusBaru === 'lunas' ? `Pesanan ${item.nama_penitip} ditandai LUNAS ✓` : `Status ${item.nama_penitip} diubah ke BELUM LUNAS`)
}

// Hapus Titipan (Delete)
const handleDelete = async (id, nama) => {
  if (!confirm(`Hapus pesanan titipan atas nama "${nama}"?`)) return

  titipans.value = titipans.value.filter((t) => t.id !== id)

  if (backendStatus.value === 'connected') {
    try {
      await fetch(`http://127.0.0.1:8000/api/titipans/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
      })
    } catch (e) {
      console.warn('Gagal delete di backend:', e)
    }
  }
  saveToLocalStorage()
  showToast(`Pesanan ${nama} berhasil dihapus dari database.`)
}

// Tambah Titipan dari Database ke Room Aktif
const handleAddToCurrentRoom = (item) => {
  emit('add-to-room', {
    name: item.nama_barang,
    friend: item.nama_penitip,
    price: item.harga_barang,
    qty: item.jumlah,
    resto: item.lokasi_beli,
    note: item.catatan,
    isPaid: item.status_pembayaran === 'lunas'
  })
  showToast(`"${item.nama_barang}" (${item.nama_penitip}) berhasil dimasukkan ke Room Aktif! 🎉`)
}

// LocalStorage Persistence
const saveToLocalStorage = () => {
  try {
    localStorage.setItem('nitipdong_week9_titipans', JSON.stringify(titipans.value))
  } catch (e) {
    console.error('Error saving to storage:', e)
  }
}

const loadFromLocalStorage = () => {
  try {
    const saved = localStorage.getItem('nitipdong_week9_titipans')
    if (saved) {
      titipans.value = JSON.parse(saved)
      return
    }
  } catch (e) {
    console.error('Error loading from storage:', e)
  }
  titipans.value = [...defaultTitipans]
}

// Reset data demo
const handleResetDemoData = () => {
  if (confirm('Kembalikan data ke demo awal?')) {
    titipans.value = [...defaultTitipans]
    saveToLocalStorage()
    showToast('Data demo berhasil di-reset!')
  }
}

// Filtered data
const filteredTitipans = computed(() => {
  return titipans.value.filter((item) => {
    // Filter Kategori
    if (selectedKategori.value !== 'all' && Number(item.kategori_id) !== Number(selectedKategori.value)) {
      return false
    }
    // Filter Status
    if (statusFilter.value !== 'all' && item.status_pembayaran !== statusFilter.value) {
      return false
    }
    // Search
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const matchPenitip = item.nama_penitip?.toLowerCase().includes(q)
      const matchBarang = item.nama_barang?.toLowerCase().includes(q)
      const matchLokasi = item.lokasi_beli?.toLowerCase().includes(q)
      return matchPenitip || matchBarang || matchLokasi
    }
    return true
  })
})

// Statistik Keuangan (ORM Aggregation)
const stats = computed(() => {
  const totalBiaya = titipans.value.reduce((sum, item) => sum + (Number(item.total_bayar) || 0), 0)
  const totalLunas = titipans.value
    .filter((item) => item.status_pembayaran === 'lunas')
    .reduce((sum, item) => sum + (Number(item.total_bayar) || 0), 0)
  const sisaUtang = totalBiaya - totalLunas
  const persenLunas = totalBiaya > 0 ? Math.round((totalLunas / totalBiaya) * 100) : 0

  return {
    totalBiaya,
    totalLunas,
    sisaUtang,
    persenLunas,
    totalCount: titipans.value.length
  }
})

onMounted(() => {
  loadFromLocalStorage()
  checkBackendConnection()
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header Komponen -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 shadow-sm">
      <div class="flex items-center gap-4">
        <div 
          class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg shrink-0"
          :style="{ backgroundColor: currentTheme.primary, boxShadow: `0 10px 25px -5px ${currentTheme.shadow}` }"
        >
          <Database class="w-6 h-6" />
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">
              Database & Eloquent ORM Titipan
            </h2>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300 border border-orange-200 dark:border-orange-800">
              Week 9 Laravel
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
            Integrasi langsung arsitektur CRUD, Multi-Tabel (<code class="text-xs font-mono font-bold text-orange-600 dark:text-orange-400">kategoris</code> & <code class="text-xs font-mono font-bold text-orange-600 dark:text-orange-400">titipans</code>), serta Relasi ORM ke dalam web NitipDong.
          </p>
        </div>
      </div>

      <!-- Indikator Koneksi Backend & Quick Action -->
      <div class="flex items-center gap-2.5 flex-wrap">
        <!-- Badge Status Koneksi Backend -->
        <div 
          class="flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold border transition"
          :class="backendStatus === 'connected' 
            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' 
            : backendStatus === 'checking'
            ? 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 border-slate-200 dark:border-zinc-700'
            : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800'"
        >
          <span 
            class="w-2 h-2 rounded-full"
            :class="backendStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : backendStatus === 'checking' ? 'bg-slate-400' : 'bg-amber-500'"
          />
          <span v-if="backendStatus === 'connected'">Laravel API Live (Port 8000)</span>
          <span v-else-if="backendStatus === 'checking'">Mengecek Koneksi...</span>
          <span v-else>Mode Standalone / Lokal</span>
        </div>

        <button 
          @click="checkBackendConnection" 
          class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
          title="Cek ulang koneksi ke backend Laravel"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': backendStatus === 'checking' }" />
        </button>

        <button 
          @click="showAddModal = true"
          class="px-4 py-2 rounded-xl text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer"
          :style="{ backgroundColor: currentTheme.primary, boxShadow: `0 8px 20px -4px ${currentTheme.shadow}` }"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Titipan Baru</span>
        </button>
      </div>
    </div>

    <!-- Toast Notification -->
    <div 
      v-if="notification" 
      class="p-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm transition animate-fade-in"
      :class="notification.type === 'success' 
        ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' 
        : 'bg-rose-50 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800'"
    >
      <div class="flex items-center gap-2.5">
        <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
        <span>{{ notification.text }}</span>
      </div>
      <button @click="notification = null" class="font-black text-sm">&times;</button>
    </div>

    <!-- Tab Navigasi Sub-Modul -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-zinc-800 pb-2">
      <button 
        @click="activeTab = 'crud'"
        class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2"
        :class="activeTab === 'crud' ? 'text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
        :style="activeTab === 'crud' ? { backgroundColor: currentTheme.primary } : {}"
      >
        <Database class="w-3.5 h-3.5" />
        <span>Kelola Titipan (CRUD & Aggregasi)</span>
      </button>

      <button 
        @click="activeTab = 'schema'"
        class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2"
        :class="activeTab === 'schema' ? 'text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
        :style="activeTab === 'schema' ? { backgroundColor: currentTheme.primary } : {}"
      >
        <FolderGit2 class="w-3.5 h-3.5" />
        <span>Skema Relasi ORM (1 : N)</span>
      </button>

      <button 
        @click="activeTab = 'checklist'"
        class="px-4 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-2"
        :class="activeTab === 'checklist' ? 'text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
        :style="activeTab === 'checklist' ? { backgroundColor: currentTheme.primary } : {}"
      >
        <Code2 class="w-3.5 h-3.5" />
        <span>Pemenuhan 7 Poin Tugas</span>
      </button>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 1: KELOLA TITIPAN (CRUD & AGREGASI ORM) -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'crud'" class="space-y-6">
      <!-- 4 Kartu Metrik Keuangan (ORM Aggregates) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800">
          <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1 flex items-center justify-between">
            <span>Total Tagihan Sirkel</span>
            <span>💰</span>
          </div>
          <div class="text-xl font-black text-slate-900 dark:text-white">
            {{ formatRupiah(stats.totalBiaya) }}
          </div>
          <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-1">ORM: Titipan::sum('total_bayar')</p>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/20 dark:bg-emerald-950/10">
          <div class="text-[11px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1 flex items-center justify-between">
            <span>Sudah Lunas</span>
            <span>✅</span>
          </div>
          <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">
            {{ formatRupiah(stats.totalLunas) }}
          </div>
          <div class="mt-2 flex items-center gap-2">
            <div class="flex-1 bg-slate-200 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" :style="{ width: `${stats.persenLunas}%` }"></div>
            </div>
            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400">{{ stats.persenLunas }}%</span>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-900/40 bg-amber-50/20 dark:bg-amber-950/10">
          <div class="text-[11px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1 flex items-center justify-between">
            <span>Sisa Utang Belum Lunas</span>
            <span>⏳</span>
          </div>
          <div class="text-xl font-black text-amber-600 dark:text-amber-400">
            {{ formatRupiah(stats.sisaUtang) }}
          </div>
          <p class="text-[10px] text-amber-600/80 dark:text-amber-400/80 mt-1">Perlu ditagihkan ke penitip</p>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800">
          <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1 flex items-center justify-between">
            <span>Total Item Titipan</span>
            <span>📦</span>
          </div>
          <div class="text-xl font-black text-slate-900 dark:text-white">
            {{ stats.totalCount }} <span class="text-xs text-slate-400 font-bold">Pesanan</span>
          </div>
          <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-1">{{ kategoris.length }} Kategori Merchant ORM</p>
        </div>
      </div>

      <!-- Filter & Search Toolbar -->
      <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari penitip, menu, atau warung..."
            class="w-full pl-9 pr-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700 text-slate-800 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-orange-500"
          >
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
        </div>

        <!-- Filter Dropdown & Badges -->
        <div class="flex items-center gap-2 flex-wrap">
          <select 
            v-model="selectedKategori"
            class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 focus:outline-none"
          >
            <option value="all">Semua Kategori ORM</option>
            <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">
              {{ kat.icon }} {{ kat.nama_kategori }}
            </option>
          </select>

          <select 
            v-model="statusFilter"
            class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 focus:outline-none"
          >
            <option value="all">Semua Status</option>
            <option value="belum_lunas">⏳ Belum Bayar</option>
            <option value="lunas">✅ Lunas</option>
          </select>

          <button 
            @click="handleResetDemoData"
            class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
            title="Reset data ke data demo awal"
          >
            Reset Demo
          </button>
        </div>
      </div>

      <!-- Tabel Data Titipan Responsif (CRUD Index) -->
      <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-black text-slate-900 dark:text-white">
              Data Titipan Hasil Query Eloquent ORM
            </h3>
            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
              {{ filteredTitipans.length }} Data
            </span>
          </div>
          <span class="text-[11px] text-slate-400 dark:text-zinc-500 hidden sm:inline">
            Eager Loading: <code class="font-mono text-orange-600 dark:text-orange-400">with('kategori')</code>
          </span>
        </div>

        <div v-if="filteredTitipans.length === 0" class="p-12 text-center">
          <div class="text-5xl mb-2">🍽️</div>
          <h4 class="text-sm font-bold text-slate-700 dark:text-zinc-300">Belum ada titipan yang cocok</h4>
          <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Coba ubah filter pencarian atau tambahkan titipan baru.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-zinc-800/60 text-slate-400 uppercase text-[10px] font-black tracking-wider border-b border-slate-100 dark:border-zinc-800">
              <tr>
                <th class="py-3 px-4">Penitip</th>
                <th class="py-3 px-4">Menu & Kategori ORM</th>
                <th class="py-3 px-4">Lokasi Resto</th>
                <th class="py-3 px-4 text-right">Harga x Qty</th>
                <th class="py-3 px-4 text-right">Total Bayar</th>
                <th class="py-3 px-4 text-center">Status Lunas</th>
                <th class="py-3 px-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
              <tr 
                v-for="item in filteredTitipans" 
                :key="item.id"
                class="hover:bg-slate-50/70 dark:hover:bg-zinc-800/40 transition"
              >
                <!-- Penitip -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div 
                      class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-white text-[11px]"
                      :style="{ backgroundColor: currentTheme.primary }"
                    >
                      {{ item.nama_penitip.charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-bold text-slate-800 dark:text-zinc-100">{{ item.nama_penitip }}</span>
                  </div>
                </td>

                <!-- Menu & Kategori -->
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-800 dark:text-zinc-100">{{ item.nama_barang }}</div>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300">
                      <span>{{ getKategori(item.kategori_id).icon }}</span>
                      <span>{{ getKategori(item.kategori_id).nama_kategori }}</span>
                    </span>
                    <span v-if="item.catatan" class="text-[10px] text-slate-400 italic truncate max-w-xs">
                      "{{ item.catatan }}"
                    </span>
                  </div>
                </td>

                <!-- Lokasi Beli -->
                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 dark:text-zinc-300">
                  <span class="font-medium">📍 {{ item.lokasi_beli }}</span>
                </td>

                <!-- Harga x Qty -->
                <td class="py-3.5 px-4 whitespace-nowrap text-right text-slate-600 dark:text-zinc-400">
                  <div>{{ formatRupiah(item.harga_barang) }} &times; {{ item.jumlah }}</div>
                  <div class="text-[10px] text-slate-400">+ Jastip {{ formatRupiah(item.biaya_jastip) }}</div>
                </td>

                <!-- Total Bayar -->
                <td class="py-3.5 px-4 whitespace-nowrap text-right">
                  <span class="font-black text-slate-900 dark:text-white text-sm" :style="{ color: currentTheme.primary }">
                    {{ formatRupiah(item.total_bayar) }}
                  </span>
                </td>

                <!-- Status Pembayaran (Toggle Instant) -->
                <td class="py-3.5 px-4 whitespace-nowrap text-center">
                  <button 
                    @click="handleToggleStatus(item)"
                    class="px-2.5 py-1 rounded-full text-[11px] font-extrabold transition cursor-pointer border"
                    :class="item.status_pembayaran === 'lunas' 
                      ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' 
                      : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 hover:bg-amber-100'"
                    title="Klik untuk ubah status pembayaran"
                  >
                    <span v-if="item.status_pembayaran === 'lunas'">✓ Lunas</span>
                    <span v-else>⏳ Belum Bayar</span>
                  </button>
                </td>

                <!-- Aksi: Add to Room & Delete -->
                <td class="py-3.5 px-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button 
                      @click="handleAddToCurrentRoom(item)"
                      class="px-2 py-1 rounded-lg text-[10px] font-bold text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-950/40 transition cursor-pointer flex items-center gap-1"
                      title="Tambahkan pesanan ini ke Room NitipDong aktif"
                    >
                      <ShoppingBag class="w-3 h-3" />
                      <span>Ke Room</span>
                    </button>
                    <button 
                      @click="handleDelete(item.id, item.nama_penitip)"
                      class="p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer"
                      title="Hapus titipan dari database"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: SKEMA RELASI MODEL ORM (1 : N) -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'schema'" class="space-y-6">
      <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 space-y-6">
        <div>
          <h3 class="text-base font-black text-slate-900 dark:text-white">
            Visualisasi Relasi Eloquent ORM Multi-Tabel
          </h3>
          <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
            Menghubungkan tabel kategori merchant dengan pesanan titipan teman secara otomatis menggunakan kunci asing (<code class="font-mono text-orange-600 font-bold">kategori_id</code>).
          </p>
        </div>

        <!-- Diagram visual 1:N -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
          <!-- Tabel Kategori (Induk) -->
          <div class="p-5 rounded-2xl border-2 border-emerald-500/40 bg-emerald-50/20 dark:bg-emerald-950/10 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-emerald-200 dark:border-emerald-800">
              <span class="text-xs font-black text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">
                1. Tabel Induk: `kategoris`
              </span>
              <span class="text-[10px] font-black px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                1 (One)
              </span>
            </div>
            <div class="space-y-1 font-mono text-xs">
              <div class="text-slate-700 dark:text-zinc-300 font-bold">🔑 id (PK)</div>
              <div class="text-slate-600 dark:text-zinc-400">📄 nama_kategori (varchar 100)</div>
              <div class="text-slate-600 dark:text-zinc-400">🏷️ slug (varchar 100, unique)</div>
              <div class="text-slate-600 dark:text-zinc-400">🎨 icon (varchar 50)</div>
              <div class="text-slate-600 dark:text-zinc-400">📝 deskripsi (text)</div>
            </div>
            <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800 text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">
              Relasi Model: <code class="bg-emerald-100 dark:bg-emerald-900 px-1 py-0.5 rounded">hasMany(Titipan::class)</code>
            </div>
          </div>

          <!-- Tabel Titipan (Anak) -->
          <div class="p-5 rounded-2xl border-2 border-orange-500/40 bg-orange-50/20 dark:bg-orange-950/10 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-orange-200 dark:border-orange-800">
              <span class="text-xs font-black text-orange-700 dark:text-orange-300 uppercase tracking-wider">
                2. Tabel Anak: `titipans`
              </span>
              <span class="text-[10px] font-black px-2 py-0.5 rounded bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                N (Many)
              </span>
            </div>
            <div class="space-y-1 font-mono text-xs">
              <div class="text-slate-700 dark:text-zinc-300 font-bold">🔑 id (PK)</div>
              <div class="text-orange-600 dark:text-orange-400 font-bold">🔗 kategori_id (FK -> kategoris.id)</div>
              <div class="text-slate-600 dark:text-zinc-400">👤 nama_penitip (varchar 100)</div>
              <div class="text-slate-600 dark:text-zinc-400">🍱 nama_barang (varchar 150)</div>
              <div class="text-slate-600 dark:text-zinc-400">📍 lokasi_beli (varchar 150)</div>
              <div class="text-slate-600 dark:text-zinc-400">💵 total_bayar (unsigned int)</div>
              <div class="text-slate-600 dark:text-zinc-400">🏷️ status_pembayaran (enum)</div>
            </div>
            <div class="pt-2 border-t border-orange-200 dark:border-orange-800 text-[11px] text-orange-700 dark:text-orange-300 font-bold">
              Relasi Model: <code class="bg-orange-100 dark:bg-orange-900 px-1 py-0.5 rounded">belongsTo(Kategori::class)</code>
            </div>
          </div>
        </div>

        <!-- Preview Cuplikan Kode Model PHP -->
        <div class="p-4 rounded-2xl bg-slate-900 text-slate-200 font-mono text-xs space-y-2 overflow-x-auto">
          <div class="text-[11px] text-slate-400 font-bold">// app/Models/Titipan.php</div>
          <div class="text-emerald-400">public function kategori(): BelongsTo</div>
          <div>{</div>
          <div class="pl-4 text-slate-300">return $this->belongsTo(Kategori::class, 'kategori_id');</div>
          <div>}</div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 3: PEMENUHAN 7 POIN TUGAS DOSEN -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'checklist'" class="space-y-4">
      <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800">
        <h3 class="text-base font-black text-slate-900 dark:text-white mb-1">
          Checklist Evaluasi Praktikum Week 9: Laravel CRUD + ORM
        </h3>
        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-6">
          Sesuai dengan instruksi yang tertera pada catatan modul perkuliahan:
        </p>

        <div class="space-y-3">
          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">1. Instalasi Laravel</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">Framework Laravel terinstal di <code class="font-mono">week9/</code> dengan dependensi lengkap.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">2. Setting Database (.env)</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">Dikonfigurasi di <code class="font-mono">week9/.env</code> dengan driver SQLite & opsi MySQL XAMPP.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">3. Buat Table di Database -> Migrations Sejumlah Table</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">Membuat multi-tabel: <code class="font-mono">kategoris</code> dan <code class="font-mono">titipans</code> dengan foreign key.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">4. Buat Model untuk Masing-Masing Table</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5"><code class="font-mono">app/Models/Kategori.php</code> dan <code class="font-mono">app/Models/Titipan.php</code> dengan relasi ORM <code class="font-mono">hasMany</code> & <code class="font-mono">belongsTo</code>.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">5. Buat Controller (Method index)</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5"><code class="font-mono">app/Http/Controllers/TitipanController.php</code> menjalankan query Eager Loading ORM & kalkulasi agregat split bill.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">6. Buat Views (Index)</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">Template Blade di <code class="font-mono">resources/views/titipan/index.blade.php</code> serta antarmuka terpadu di website utama NitipDong.</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-800/40 flex items-start gap-3">
            <Check class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
            <div>
              <div class="font-bold text-xs text-slate-800 dark:text-zinc-200">7. Buat Route (Index)</div>
              <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5"><code class="font-mono">routes/web.php</code> memetakan URL utama ke <code class="font-mono">TitipanController@index</code>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah Titipan Baru (Create CRUD) -->
    <div 
      v-if="showAddModal" 
      class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
          <div class="flex items-center gap-2">
            <span class="text-xl">📝</span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Tambah Titipan Baru (CRUD)</h3>
          </div>
          <button 
            @click="showAddModal = false"
            class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-white flex items-center justify-center font-bold"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="handleCreateTitipan" class="space-y-3.5">
          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Nama Penitip</label>
            <input 
              v-model="newForm.nama_penitip" 
              type="text" 
              required 
              placeholder="Contoh: Radiz / Putu / Fathan" 
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            >
          </div>

          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Kategori Merchant (Relasi ORM)</label>
            <select 
              v-model="newForm.kategori_id" 
              required
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            >
              <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">
                {{ kat.icon }} {{ kat.nama_kategori }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Nama Makanan / Barang</label>
            <input 
              v-model="newForm.nama_barang" 
              type="text" 
              required 
              placeholder="Contoh: Nasi Goreng Spesial Telur Ceplok" 
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            >
          </div>

          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Lokasi Resto / Warung</label>
            <input 
              v-model="newForm.lokasi_beli" 
              type="text" 
              required 
              placeholder="Contoh: Kantin Vokasi Lt. 2 / Warung Bu Imas" 
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            >
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Harga Satuan (Rp)</label>
              <input 
                v-model="newForm.harga_barang" 
                type="number" 
                min="0"
                required 
                class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
              >
            </div>
            <div>
              <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Jumlah Porsi</label>
              <input 
                v-model="newForm.jumlah" 
                type="number" 
                min="1"
                required 
                class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
              >
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Biaya Jastip Sirkel (Rp)</label>
            <input 
              v-model="newForm.biaya_jastip" 
              type="number" 
              min="0"
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            >
          </div>

          <div>
            <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">Catatan Khusus</label>
            <textarea 
              v-model="newForm.catatan" 
              rows="2" 
              placeholder="Contoh: Pedas level 2, jangan pakai timun"
              class="w-full px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-orange-500"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-zinc-800">
            <button 
              type="button" 
              @click="showAddModal = false"
              class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 rounded-xl text-xs font-black text-white shadow-md transition cursor-pointer"
              :style="{ backgroundColor: currentTheme.primary, boxShadow: `0 8px 20px -4px ${currentTheme.shadow}` }"
            >
              Simpan ke Database
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>
