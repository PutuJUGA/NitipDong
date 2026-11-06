<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Search,
  Plus,
  Sparkles,
  ShoppingBag,
  Star,
  Clock,
  MapPin,
  RefreshCw,
  AlertCircle,
  WifiOff,
  Flame,
  Check
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

const emit = defineEmits(['add-order'])

// ========================================================
// STATE MANAGEMENT & DATA FETCHING
// ========================================================
const apiState = ref('idle') // 'idle' | 'loading' | 'success' | 'error'
const menuList = ref([])
const errorMessage = ref('')
const searchQuery = ref('')
const selectedCategory = ref('Semua')
const sortBy = ref('popular') // 'popular' | 'price-low' | 'price-high' | 'rating'
const addedItemId = ref(null)

// Kategori Filter
const categories = [
  { id: 'Semua', label: 'Semua Menu', icon: '🍽️' },
  { id: 'Makanan', label: 'Makanan Berat', icon: '🍗' },
  { id: 'Minuman', label: 'Minuman Segar', icon: '🧋' },
  { id: 'Snack', label: 'Cemilan & Snack', icon: '🥟' }
]

// Format Rupiah
const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val)
}

// Fetch API Menu Rekomendasi
const fetchMenuData = async () => {
  apiState.value = 'loading'
  errorMessage.value = ''

  try {
    // Simulasi loading sebentar agar transisi skeleton halus
    const [res] = await Promise.all([
      fetch('/api/menu-titipan.json'),
      new Promise((resolve) => setTimeout(resolve, 400))
    ])

    if (!res.ok) {
      throw new Error(`Gagal memuat katalog menu (Status: ${res.status})`)
    }

    const data = await res.json()
    menuList.value = Array.isArray(data) ? data : []
    apiState.value = 'success'
  } catch (err) {
    console.error('Error memuat menu:', err)
    apiState.value = 'error'
    errorMessage.value = 'Tidak dapat memuat rekomendasi menu kuliner saat ini. Silakan periksa koneksi internet Anda.'
  }
}

// Filter & Sort Data Menu
const filteredMenuList = computed(() => {
  let list = menuList.value.filter((item) => {
    const matchCategory =
      selectedCategory.value === 'Semua' || item.category === selectedCategory.value

    const query = searchQuery.value.toLowerCase().trim()
    const matchSearch =
      !query ||
      item.name.toLowerCase().includes(query) ||
      item.resto.toLowerCase().includes(query) ||
      item.description.toLowerCase().includes(query)

    return matchCategory && matchSearch
  })

  // Pengurutan
  if (sortBy.value === 'price-low') {
    list = [...list].sort((a, b) => a.price - b.price)
  } else if (sortBy.value === 'price-high') {
    list = [...list].sort((a, b) => b.price - a.price)
  } else if (sortBy.value === 'rating') {
    list = [...list].sort((a, b) => b.rating - a.rating)
  }

  return list
})

// State Modal Pemesanan & Catatan Kustom
const isModalOpen = ref(false)
const selectedMenuItem = ref(null)
const orderForm = ref({
  personName: '',
  qty: 1,
  notes: ''
})

const openAddModal = (item) => {
  selectedMenuItem.value = item
  orderForm.value = {
    personName: '',
    qty: 1,
    notes: ''
  }
  isModalOpen.value = true
}

const submitOrder = () => {
  if (!selectedMenuItem.value) return
  emit('add-order', {
    ...selectedMenuItem.value,
    personName: orderForm.value.personName.trim(),
    qty: Math.max(1, Number(orderForm.value.qty) || 1),
    notes: orderForm.value.notes.trim()
  })
  isModalOpen.value = false
  addedItemId.value = selectedMenuItem.value.id
  setTimeout(() => {
    addedItemId.value = null
  }, 1500)
}

// Tambah Cepat Langsung
const handleAddMenu = (item) => {
  openAddModal(item)
}

onMounted(() => {
  fetchMenuData()
})
</script>

<template>
  <div class="space-y-6">
    <!-- HERO DISCOVERY BANNER (Modern & Real Production Web Look) -->
    <div
      class="relative overflow-hidden rounded-3xl p-6 sm:p-10 text-white shadow-xl transition-all"
      :style="{
        background: 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)'
      }"
    >
      <!-- Background Ambient Glow -->
      <div
        class="absolute -right-16 -top-16 w-80 h-80 rounded-full blur-3xl opacity-25 pointer-events-none"
        :style="{ backgroundColor: currentTheme.primary }"
      ></div>

      <div class="relative z-10 max-w-2xl space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/15 text-orange-400">
          <Flame class="w-3.5 h-3.5 text-orange-400" />
          <span>Rekomendasi Kuliner Terlaris</span>
        </div>

        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
          Eksplor Menu & Jajanan Sirkel
        </h2>

        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-medium">
          Lagi bingung mau titip makan apa? Temukan menu favorit dari resto terdekat dengan harga terjangkau, lalu tambahkan ke room pesanan dengan sekali klik.
        </p>
      </div>
    </div>

    <!-- SEARCH & FILTER TOOLBAR -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-zinc-800 shadow-sm space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari geprek, mie, kopi, dimsum, atau nama resto..."
            class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium"
          />
        </div>

        <!-- Sort Select -->
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-xs text-slate-400 font-bold hidden sm:inline">Urutkan:</span>
          <select
            v-model="sortBy"
            class="px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 cursor-pointer"
          >
            <option value="popular">🔥 Paling Populer</option>
            <option value="rating">⭐ Rating Tertinggi</option>
            <option value="price-low">💰 Harga Terendah</option>
            <option value="price-high">💎 Harga Tertinggi</option>
          </select>
        </div>
      </div>

      <!-- Category Filter Pills -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          @click="selectedCategory = cat.id"
          class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 cursor-pointer"
          :class="
            selectedCategory === cat.id
              ? 'bg-slate-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm'
              : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700'
          "
        >
          <span>{{ cat.icon }}</span>
          <span>{{ cat.label }}</span>
        </button>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- CONTENT STATES -->
    <!-- ======================================================== -->

    <!-- 1. LOADING STATE: Realistic Skeleton Cards -->
    <div
      v-if="apiState === 'loading'"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
    >
      <div
        v-for="n in 8"
        :key="n"
        class="bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-slate-200/80 dark:border-zinc-800 animate-pulse space-y-4"
      >
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-xl bg-slate-200 dark:bg-zinc-800"></div>
          <div class="w-16 h-5 rounded-full bg-slate-200 dark:bg-zinc-800"></div>
        </div>
        <div class="space-y-2">
          <div class="h-4 bg-slate-200 dark:bg-zinc-800 rounded w-3/4"></div>
          <div class="h-3 bg-slate-200 dark:bg-zinc-800 rounded w-1/2"></div>
          <div class="h-3 bg-slate-200 dark:bg-zinc-800 rounded w-full"></div>
        </div>
        <div class="pt-3 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
          <div class="h-5 bg-slate-200 dark:bg-zinc-800 rounded w-20"></div>
          <div class="h-8 bg-slate-200 dark:bg-zinc-800 rounded-xl w-16"></div>
        </div>
      </div>
    </div>

    <!-- 2. ERROR STATE: Natural User-Friendly Offline/Error Box -->
    <div
      v-else-if="apiState === 'error'"
      class="bg-white dark:bg-zinc-900 rounded-3xl p-10 border border-slate-200 dark:border-zinc-800 text-center space-y-4 max-w-lg mx-auto shadow-sm"
    >
      <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto text-2xl shadow-inner">
        <WifiOff class="w-7 h-7" />
      </div>

      <div class="space-y-1.5">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
          Koneksi Menu Terkendala
        </h3>
        <p class="text-xs text-slate-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">
          {{ errorMessage }}
        </p>
      </div>

      <div>
        <button
          type="button"
          @click="fetchMenuData"
          class="px-5 py-2.5 rounded-xl text-xs font-bold text-white transition flex items-center gap-2 mx-auto cursor-pointer shadow-md hover:scale-105"
          :style="{
            backgroundColor: currentTheme.primary,
            boxShadow: `0 8px 20px -4px ${currentTheme.shadow}`
          }"
        >
          <RefreshCw class="w-3.5 h-3.5" />
          <span>Muat Ulang Menu</span>
        </button>
      </div>
    </div>

    <!-- 3. SUCCESS STATE: Rich Food Discovery Cards -->
    <div v-else-if="apiState === 'success'">
      <div class="flex items-center justify-between text-xs text-slate-500 dark:text-zinc-400 px-1 mb-4">
        <span class="font-bold text-slate-700 dark:text-zinc-300">
          Menampilkan {{ filteredMenuList.length }} menu pilihan
        </span>
        <span v-if="selectedCategory !== 'Semua'" class="text-[11px] font-medium text-slate-400">
          Kategori: {{ selectedCategory }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <article
          v-for="item in filteredMenuList"
          :key="item.id"
          class="group bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-slate-200/80 dark:border-zinc-800 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
        >
          <div>
            <div class="flex items-start justify-between gap-3 mb-3">
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-inner transition-transform group-hover:scale-110"
                :style="{ backgroundColor: currentTheme.lightBg }"
              >
                {{ item.image }}
              </div>
              <div class="flex flex-col items-end gap-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300">
                  {{ item.category }}
                </span>
                <span class="text-xs text-amber-500 font-black flex items-center gap-0.5">
                  <Star class="w-3 h-3 fill-amber-400 text-amber-400 inline" />
                  {{ item.rating }}
                </span>
              </div>
            </div>

            <h4 class="font-bold text-slate-900 dark:text-white text-base group-hover:text-orange-500 transition-colors line-clamp-1">
              {{ item.name }}
            </h4>

            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400 mt-1 mb-2 font-medium">
              <span class="flex items-center gap-1 truncate">
                <MapPin class="w-3 h-3 shrink-0 text-slate-400" />
                {{ item.resto }}
              </span>
              <span>•</span>
              <span class="flex items-center gap-1 shrink-0">
                <Clock class="w-3 h-3 shrink-0 text-slate-400" />
                {{ item.prepTime }}
              </span>
            </div>

            <p class="text-xs text-slate-600 dark:text-zinc-400 line-clamp-2 mb-4 leading-relaxed font-normal">
              {{ item.description }}
            </p>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-between">
            <span
              class="font-black text-sm"
              :style="{ color: currentTheme.primary }"
            >
              {{ formatRupiah(item.price) }}
            </span>

            <button
              type="button"
              @click="handleAddMenu(item)"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white transition-all cursor-pointer shadow-xs active:scale-95"
              :class="addedItemId === item.id ? 'bg-emerald-600' : 'hover:opacity-90'"
              :style="addedItemId !== item.id ? {
                backgroundColor: currentTheme.primary,
                boxShadow: `0 4px 12px -2px ${currentTheme.shadow}`
              } : {}"
            >
              <component :is="addedItemId === item.id ? Check : Plus" class="w-3.5 h-3.5" />
              <span>{{ addedItemId === item.id ? 'Ditambahkan' : 'Titip' }}</span>
            </button>
          </div>
        </article>

        <!-- Empty Filter State -->
        <div
          v-if="filteredMenuList.length === 0"
          class="col-span-full text-center py-12 text-slate-500 dark:text-zinc-400 bg-white/50 dark:bg-zinc-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-zinc-700 space-y-2"
        >
          <p class="font-bold text-slate-700 dark:text-zinc-300">Tidak ada menu yang sesuai</p>
          <p class="text-xs text-slate-400">Coba ganti kata kunci pencarian atau pilih kategori yang berbeda.</p>
        </div>
      </div>
    </div>

    <!-- MODAL TAMBAH TITIPAN & CATATAN KUSTOM -->
    <div
      v-if="isModalOpen && selectedMenuItem"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
    >
      <div
        class="bg-white dark:bg-zinc-900 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-zinc-800 space-y-5 animate-in fade-in zoom-in-95 duration-200"
      >
        <div class="flex items-start justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
          <div class="flex items-center gap-3">
            <div
              class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-inner shrink-0"
              :style="{ backgroundColor: currentTheme.lightBg }"
            >
              {{ selectedMenuItem.image }}
            </div>
            <div>
              <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
                {{ selectedMenuItem.name }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                📍 {{ selectedMenuItem.resto }} • {{ formatRupiah(selectedMenuItem.price) }}
              </p>
            </div>
          </div>
          <button
            @click="isModalOpen = false"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 font-bold p-1 cursor-pointer"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="submitOrder" class="space-y-4 text-xs">
          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Nama Penitip (Opsional)
            </label>
            <input
              v-model="orderForm.personName"
              type="text"
              placeholder="Kosongkan jika untuk nama Anda sendiri"
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 font-medium transition"
            />
          </div>

          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Jumlah Porsi
            </label>
            <div class="flex items-center gap-3">
              <button
                type="button"
                @click="orderForm.qty = Math.max(1, orderForm.qty - 1)"
                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 font-bold text-base flex items-center justify-center cursor-pointer text-slate-700 dark:text-zinc-200"
              >
                -
              </button>
              <input
                v-model.number="orderForm.qty"
                type="number"
                min="1"
                class="w-16 text-center py-2 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 font-extrabold text-sm dark:text-zinc-100"
              />
              <button
                type="button"
                @click="orderForm.qty++"
                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 font-bold text-base flex items-center justify-center cursor-pointer text-slate-700 dark:text-zinc-200"
              >
                +
              </button>
              <span class="text-slate-500 dark:text-zinc-400 font-bold text-xs ml-auto">
                Subtotal: {{ formatRupiah(selectedMenuItem.price * (orderForm.qty || 1)) }}
              </span>
            </div>
          </div>

          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Catatan Pesanan Khusus
            </label>
            <textarea
              v-model="orderForm.notes"
              rows="2"
              placeholder="Misal: Sambal dipisah, level 2 pedas, es teh manis gak pake gula..."
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 font-medium transition resize-none"
            ></textarea>
          </div>

          <div class="pt-2 flex items-center justify-end gap-2.5">
            <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 font-bold hover:bg-slate-50 dark:hover:bg-zinc-800 cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-5 py-2.5 rounded-xl text-white font-bold transition flex items-center gap-1.5 cursor-pointer shadow-md hover:scale-102"
              :style="{
                backgroundColor: currentTheme.primary,
                boxShadow: `0 8px 20px -4px ${currentTheme.shadow}`
              }"
            >
              <Plus class="w-4 h-4" />
              <span>Tambahkan ke Room Titipan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
