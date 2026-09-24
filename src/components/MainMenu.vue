<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import {
  Plus,
  Share2,
  Copy,
  Check,
  Trash2,
  Calculator,
  Users,
  Wallet,
  MessageCircle,
  CheckCircle2,
  AlertCircle,
  Clock,
  ShoppingBag,
  Sparkles,
  Lock,
  Unlock,
  LogOut,
  Home,
  Menu as MenuIcon,
  X,
  LayoutDashboard,
  Radio,
  Settings,
  User as UserIcon
} from 'lucide-vue-next'
import ProfileSettingModal from './ProfileSettingModal.vue'
import ThemeSwitcher from './ThemeSwitcher.vue'
import { getThemeById, getThemeByAvatarUrl } from '@/utils/theme'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: 'Budi Pratama',
      email: 'budi.pratama@gmail.com',
      avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi',
      themeId: 'budi'
    })
  }
})

const emit = defineEmits(['back-home', 'logout', 'update-user'])

// State Pengaturan Profil & Avatar
const isProfileModalOpen = ref(false)

const profileData = ref({
  name: props.user?.name || 'Budi Pratama',
  username: props.user?.username || 'budipratama',
  email: props.user?.email || 'budi.pratama@gmail.com',
  phone: props.user?.phone || '081234567890',
  bio: props.user?.bio || 'Pecinta kuliner pedas & anti ribet hitung bon! 🌶️',
  avatar: props.user?.avatar || 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi',
  themeId: props.user?.themeId || 'budi',
  themeColor: props.user?.themeColor || '#f97316',
  themeName: props.user?.themeName || 'Oranye Nitip (Sunset)',
  defaultBank: props.user?.defaultBank || 'BCA / QRIS',
  defaultAccountNumber: props.user?.defaultAccountNumber || '8271928391',
  defaultAccountName: props.user?.defaultAccountName || props.user?.name || 'Budi Pratama'
})

// Tema warna dinamis berdasarkan avatar yang dipilih
const currentTheme = computed(() => {
  if (profileData.value.themeId) {
    return getThemeById(profileData.value.themeId)
  }
  return getThemeByAvatarUrl(profileData.value.avatar)
})

const themeCssVars = computed(() => ({
  '--theme-primary': currentTheme.value.primary,
  '--theme-primary-hover': currentTheme.value.primaryHover,
  '--theme-secondary': currentTheme.value.secondary,
  '--theme-light-bg': currentTheme.value.lightBg,
  '--theme-border': currentTheme.value.border,
  '--theme-shadow': currentTheme.value.shadow,
  '--theme-dark-bg': currentTheme.value.darkBg,
  '--theme-dark-text': currentTheme.value.darkText,
  '--theme-gradient': currentTheme.value.gradient
}))

// Sinkronisasi data profil dari props atau localStorage
watch(() => props.user, (newVal) => {
  if (newVal) {
    profileData.value = {
      ...profileData.value,
      ...newVal
    }
  }
}, { deep: true, immediate: true })

onMounted(() => {
  try {
    const saved = localStorage.getItem('nitipdong_profile')
    if (saved) {
      const parsed = JSON.parse(saved)
      profileData.value = { ...profileData.value, ...parsed }
    }
  } catch (e) {}
})

const openProfileModal = () => {
  isProfileModalOpen.value = true
}

const handleProfileSave = (updated) => {
  profileData.value = { ...profileData.value, ...updated }

  // Sinkronkan nama default rekening bila buat room baru
  newRoomRekName.value = updated.name || newRoomRekName.value
  if (updated.defaultAccountNumber) {
    newRoomRek.value = updated.defaultAccountNumber
  }
  if (updated.defaultBank) {
    const b = updated.defaultBank.split(' ')[0]
    if (['BCA', 'Mandiri', 'BRI', 'BNI', 'GoPay', 'OVO', 'Dana', 'QRIS'].includes(b)) {
      newRoomBank.value = b
    }
  }

  // Update rekening room aktif jika dimiliki user ini
  if (currentRoom.value && (currentRoom.value.bankInfo.accountName === props.user?.name || currentRoom.value.bankInfo.accountName === 'Budi Pratama')) {
    currentRoom.value.bankInfo.accountName = updated.name
    if (updated.defaultAccountNumber) {
      currentRoom.value.bankInfo.accountNumber = updated.defaultAccountNumber
    }
    if (updated.defaultBank) {
      currentRoom.value.bankInfo.bank = updated.defaultBank
    }
  }

  // Simpan ke localStorage & kabari parent
  try {
    localStorage.setItem('nitipdong_profile', JSON.stringify(profileData.value))
  } catch (e) {}
  emit('update-user', { ...profileData.value })
  showToast('Profil akun & avatar berhasil diperbarui! 🎉')
}

// Mobile Sidebar State
const isMobileSidebarOpen = ref(false)

// Toast Feedback Notification
const toastMessage = ref('')
const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => {
    toastMessage.value = ''
  }, 2600)
}

// Active Feature Section View: 'all' | 'calculator' | 'orders' | 'radar'
const activeNav = ref('all')

// Filter Status Pesanan: 'all' | 'unpaid' | 'paid'
const orderFilter = ref('all')

// Data Room Titipan
const rooms = ref([
  {
    id: 'room-1',
    name: 'Makan Siang Sirkel Kantor',
    resto: 'Ayam Geprek Bensu & Bebek Madura',
    inviteCode: 'geprek-siang-78',
    isOpen: true,
    targetTime: '12:30 WIB',
    deliveryFee: 12000,
    serviceFee: 2000,
    discount: 15000,
    taxPercent: 10,
    splitType: 'proportional', // 'proportional' | 'equal'
    bankInfo: {
      bank: 'BCA / QRIS',
      accountNumber: '8271928391',
      accountName: 'Budi Pratama'
    },
    orders: [
      {
        id: 1,
        personName: 'Rizky',
        itemName: 'Paket Geprek Leleh Level 3',
        notes: 'Es teh manis dipisah plastiknya',
        basePrice: 28000,
        qty: 1,
        isPaid: true
      },
      {
        id: 2,
        personName: 'Siti',
        itemName: 'Bebek Goreng Bumbu Hitam + Nasi',
        notes: 'Sambal dipisah ya',
        basePrice: 38000,
        qty: 1,
        isPaid: true
      },
      {
        id: 3,
        personName: 'Fathan',
        itemName: 'Ayam Geprek Sambal Matah + Tahu Tempe',
        notes: 'Gak pake kol goreng',
        basePrice: 27000,
        qty: 1,
        isPaid: false
      },
      {
        id: 4,
        personName: 'Radiz',
        itemName: 'Paket Mozzarella + Air Mineral',
        notes: 'Level 1 cabe 1 aja',
        basePrice: 31000,
        qty: 1,
        isPaid: false
      }
    ]
  },
  {
    id: 'room-2',
    name: 'Kopi & Snack Santai Sore',
    resto: 'Kopi Kenangan & Toast Bar',
    inviteCode: 'kenangan-sore-22',
    isOpen: true,
    targetTime: '16:00 WIB',
    deliveryFee: 9000,
    serviceFee: 1000,
    discount: 10000,
    taxPercent: 0,
    splitType: 'equal',
    bankInfo: {
      bank: 'GoPay / Dana',
      accountNumber: '081234567890',
      accountName: 'Budi Pratama'
    },
    orders: [
      {
        id: 101,
        personName: 'Putu',
        itemName: 'Kopi Kenangan Mantan (Large)',
        notes: 'Less sugar 50%, normal ice',
        basePrice: 22000,
        qty: 1,
        isPaid: true
      },
      {
        id: 102,
        personName: 'Budi',
        itemName: 'Avocado Coffee + Toast Coklat Keju',
        notes: 'Minta roti dipanggang garing',
        basePrice: 34000,
        qty: 1,
        isPaid: true
      }
    ]
  }
])

const activeRoomId = ref('room-1')

// Current Active Room
const currentRoom = computed(() => {
  return rooms.value.find(r => r.id === activeRoomId.value) || rooms.value[0] || null
})

// Calculations for Active Room
const roomSubtotal = computed(() => {
  if (!currentRoom.value) return 0
  return currentRoom.value.orders.reduce((sum, o) => sum + (o.basePrice * o.qty), 0)
})

const roomTaxAmount = computed(() => {
  if (!currentRoom.value) return 0
  return Math.round(roomSubtotal.value * (currentRoom.value.taxPercent / 100))
})

const roomNetExtraFees = computed(() => {
  if (!currentRoom.value) return 0
  const r = currentRoom.value
  return (r.deliveryFee || 0) + (r.serviceFee || 0) - (r.discount || 0)
})

const roomGrandTotal = computed(() => {
  return Math.max(0, roomSubtotal.value + roomTaxAmount.value + roomNetExtraFees.value)
})

// Calculate Each Order's Final Price Breakdown
const detailedOrders = computed(() => {
  if (!currentRoom.value) return []
  const r = currentRoom.value
  const subtotal = roomSubtotal.value
  const orderCount = r.orders.length

  return r.orders.map(order => {
    const itemTotal = order.basePrice * order.qty
    const taxShare = Math.round(itemTotal * (r.taxPercent / 100))
    
    let extraShare = 0
    if (orderCount > 0) {
      if (r.splitType === 'equal') {
        extraShare = Math.round(roomNetExtraFees.value / orderCount)
      } else {
        const proportion = subtotal > 0 ? itemTotal / subtotal : 0
        extraShare = Math.round(roomNetExtraFees.value * proportion)
      }
    }

    const finalBill = Math.max(0, itemTotal + taxShare + extraShare)

    return {
      ...order,
      itemTotal,
      taxShare,
      extraShare,
      finalBill
    }
  })
})

// Filtered Orders
const filteredOrders = computed(() => {
  if (orderFilter.value === 'paid') {
    return detailedOrders.value.filter(o => o.isPaid)
  }
  if (orderFilter.value === 'unpaid') {
    return detailedOrders.value.filter(o => !o.isPaid)
  }
  return detailedOrders.value
})

// Payment Metrics
const totalPaidBill = computed(() => {
  return detailedOrders.value
    .filter(o => o.isPaid)
    .reduce((sum, o) => sum + o.finalBill, 0)
})

const totalUnpaidBill = computed(() => {
  return detailedOrders.value
    .filter(o => !o.isPaid)
    .reduce((sum, o) => sum + o.finalBill, 0)
})

const unpaidCount = computed(() => {
  return detailedOrders.value.filter(o => !o.isPaid).length
})

const paidPercentage = computed(() => {
  if (roomGrandTotal.value === 0) return 0
  const pct = Math.round((totalPaidBill.value / roomGrandTotal.value) * 100)
  return Math.min(100, Math.max(0, pct))
})

// Actions: Toggle Payment Status
const togglePaid = (orderId) => {
  if (!currentRoom.value) return
  const ord = currentRoom.value.orders.find(o => o.id === orderId)
  if (ord) {
    ord.isPaid = !ord.isPaid
    showToast(ord.isPaid ? `Status ${ord.personName}: LUNAS ✅` : `Status ${ord.personName}: NGUTANG ⚠️`)
  }
}

// Actions: Toggle Room Status
const toggleRoomStatus = () => {
  if (!currentRoom.value) return
  currentRoom.value.isOpen = !currentRoom.value.isOpen
  showToast(currentRoom.value.isOpen ? 'Room dibuka kembali untuk titipan' : 'Room ditutup (Pesanan dikunci)')
}

// Actions: Delete Order
const deleteOrder = (orderId) => {
  if (!currentRoom.value) return
  currentRoom.value.orders = currentRoom.value.orders.filter(o => o.id !== orderId)
  showToast('Pesanan berhasil dihapus')
}

// Form: Tambah Pesanan Baru
const newPerson = ref('')
const newItem = ref('')
const newNotes = ref('')
const newPrice = ref(25000)
const newQty = ref(1)

const addOrder = () => {
  if (!newPerson.value.trim() || !newItem.value.trim()) {
    showToast('Harap isi nama pemesan dan menu makanan!')
    return
  }
  if (!currentRoom.value) return

  currentRoom.value.orders.push({
    id: Date.now(),
    personName: newPerson.value.trim(),
    itemName: newItem.value.trim(),
    notes: newNotes.value.trim(),
    basePrice: Number(newPrice.value) || 0,
    qty: Number(newQty.value) || 1,
    isPaid: false
  })

  newPerson.value = ''
  newItem.value = ''
  newNotes.value = ''
  newPrice.value = 25000
  newQty.value = 1
  showToast('Pesanan baru berhasil ditambahkan!')
}

// Modal: Buat Room Baru
const showNewRoomModal = ref(false)
const newRoomName = ref('')
const newRoomResto = ref('')
const newRoomTarget = ref('12:30 WIB')
const newRoomBank = ref('BCA')
const newRoomRek = ref('')
const newRoomRekName = ref(props.user.name || '')

const createRoom = () => {
  if (!newRoomName.value.trim() || !newRoomResto.value.trim()) {
    showToast('Harap isi nama room dan nama resto!')
    return
  }

  const generatedId = `room-${Date.now()}`
  const generatedCode = newRoomName.value.toLowerCase().replace(/[^a-z0-9]/g, '-') + '-' + Math.floor(Math.random() * 90 + 10)

  rooms.value.unshift({
    id: generatedId,
    name: newRoomName.value.trim(),
    resto: newRoomResto.value.trim(),
    inviteCode: generatedCode,
    isOpen: true,
    targetTime: newRoomTarget.value.trim() || '12:30 WIB',
    deliveryFee: 10000,
    serviceFee: 1000,
    discount: 0,
    taxPercent: 10,
    splitType: 'proportional',
    bankInfo: {
      bank: newRoomBank.value,
      accountNumber: newRoomRek.value || '1234567890',
      accountName: newRoomRekName.value || props.user.name
    },
    orders: []
  })

  activeRoomId.value = generatedId
  showNewRoomModal.value = false
  newRoomName.value = ''
  newRoomResto.value = ''
  newRoomRek.value = ''
  showToast('Room baru berhasil dibuat! 🎉')
}

// Modal & Fitur: Hapus Room
const showDeleteRoomModal = ref(false)
const roomToDelete = ref(null)

const confirmDeleteRoom = (room) => {
  roomToDelete.value = room
  showDeleteRoomModal.value = true
}

const executeDeleteRoom = () => {
  if (!roomToDelete.value) return
  const deletedName = roomToDelete.value.name
  rooms.value = rooms.value.filter(r => r.id !== roomToDelete.value.id)
  
  if (rooms.value.length > 0) {
    activeRoomId.value = rooms.value[0].id
  } else {
    activeRoomId.value = ''
  }

  showDeleteRoomModal.value = false
  roomToDelete.value = null
  showToast(`Room "${deletedName}" berhasil dihapus 🗑️`)
}

// Copy & WhatsApp Handlers
const copyRoomLink = () => {
  if (!currentRoom.value) return
  const url = `https://nitipdong.com/join/${currentRoom.value.inviteCode}`
  navigator.clipboard?.writeText(url)
  showToast('Link room tersalin ke clipboard! 📋')
}

const shareRoomToWhatsApp = () => {
  if (!currentRoom.value) return
  const r = currentRoom.value
  const text = `Halo guys! Yuk isi titipan makanan bareng di *NitipDong* 🍱\n\n📌 *Room:* ${r.name}\n🏪 *Resto:* ${r.resto}\n⏰ *Batas Pesan:* ${r.targetTime}\n\n👉 Klik link untuk isi pesananmu sendiri:\nhttps://nitipdong.com/join/${r.inviteCode}`
  window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank')
}

const sendWhatsAppBill = (order) => {
  if (!currentRoom.value) return
  const r = currentRoom.value
  const feeLabel = r.splitType === 'equal' ? 'Bagi Rata' : 'Proporsional'
  const text = `🍱 *Tagihan NitipDong - Titip Makanan*\n\nHalo *${order.personName}*! Ini rincian pesananmu:\n` +
    `• ${order.itemName} (${order.qty}x) = Rp ${order.itemTotal.toLocaleString('id-ID')}\n` +
    (order.notes ? `  _(Catatan: ${order.notes})_\n` : '') +
    (r.taxPercent > 0 ? `• Pajak Resto (${r.taxPercent}%): Rp ${order.taxShare.toLocaleString('id-ID')}\n` : '') +
    `• Ongkir & Promo (${feeLabel}): ${order.extraShare >= 0 ? '+' : ''}Rp ${order.extraShare.toLocaleString('id-ID')}\n` +
    `--------------------------------\n` +
    `*TOTAL YANG HARUS DITRANSFER: Rp ${order.finalBill.toLocaleString('id-ID')}*\n\n` +
    `💳 Pembayaran via:\n*${r.bankInfo.bank}*: \`${r.bankInfo.accountNumber}\`\na/n *${r.bankInfo.accountName}*\n\n` +
    `Makasih ya! Tolong kabari kalau sudah transfer 🙏`

  window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank')
}

const copyBankInfo = () => {
  if (!currentRoom.value) return
  const b = currentRoom.value.bankInfo
  const text = `${b.bank} - ${b.accountNumber} (a/n ${b.accountName})`
  navigator.clipboard?.writeText(text)
  showToast('Info rekening tersalin! 💳')
}

const selectRoom = (id) => {
  activeRoomId.value = id
  isMobileSidebarOpen.value = false
}
</script>

<template>
  <div 
    class="min-h-screen bg-slate-50 dark:bg-zinc-950 text-slate-900 dark:text-zinc-100 flex transition-colors duration-200"
    :class="currentTheme.themeClass"
    :style="themeCssVars"
  >
    
    <!-- Toast Popup Notification -->
    <transition
      enter-active-class="transform transition ease-out duration-300"
      enter-from-class="translate-y-4 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="toastMessage" 
        class="fixed bottom-6 right-6 z-50 bg-slate-900 dark:bg-zinc-800 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 dark:border-zinc-600 text-sm font-bold"
      >
        <Sparkles class="w-4 h-4 shrink-0 animate-spin" :style="{ color: currentTheme.primary }" />
        <span>{{ toastMessage }}</span>
      </div>
    </transition>

    <!-- BACKDROP MOBILE SIDEBAR -->
    <div 
      v-if="isMobileSidebarOpen"
      @click="isMobileSidebarOpen = false"
      class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 lg:hidden"
    />

    <!-- ==================== SIDEBAR / NAVBAR SAMPING ==================== -->
    <aside 
      class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-zinc-900 border-r border-slate-200/80 dark:border-zinc-800 flex flex-col justify-between transition-transform duration-300 transform lg:translate-x-0"
      :class="isMobileSidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
    >
      <!-- Bagian Atas Sidebar -->
      <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- Logo & Header Brand -->
        <div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
          <div @click="emit('back-home')" class="flex items-center gap-3 cursor-pointer group">
            <img 
              src="/LOGO.svg" 
              alt="NitipDong Logo" 
              class="h-9 w-auto group-hover:scale-105 transition-transform object-contain"
            />
            <div>
              <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white">
                Nitip<span :style="{ color: currentTheme.primary }">Dong</span>
              </span>
              <span class="block text-[10px] font-black uppercase tracking-widest" :style="{ color: currentTheme.primary }">
                Dashboard Menu
              </span>
            </div>
          </div>

          <!-- Tombol Close Mobile -->
          <button 
            @click="isMobileSidebarOpen = false"
            class="lg:hidden p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-zinc-800 cursor-pointer"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- User Profile Card di Sidebar (Klik untuk Pengaturan Profil) -->
        <div 
          @click="openProfileModal"
          class="p-3.5 mx-4 my-3 rounded-2xl border hover:shadow-md flex items-center gap-3 cursor-pointer group transition-all duration-200 relative overflow-hidden"
          :style="{
            backgroundColor: currentTheme.lightBg,
            borderColor: currentTheme.border
          }"
          title="Klik untuk membuka Pengaturan Profil"
        >
          <div class="relative shrink-0">
            <img :src="profileData.avatar" class="w-11 h-11 rounded-full shadow-xs object-cover border-2 border-white dark:border-zinc-600 group-hover:scale-105 transition-transform" :style="{ backgroundColor: currentTheme.lightBg }" />
            <div class="absolute -bottom-1 -right-1 text-white p-1 rounded-full shadow-xs transition-colors" :style="{ backgroundColor: currentTheme.primary }">
              <Settings class="w-2.5 h-2.5" />
            </div>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <p class="text-xs font-black text-slate-900 dark:text-white truncate transition-colors" :style="{ '--hover-color': currentTheme.primary }">{{ profileData.name }}</p>
              <span class="text-[10px] font-black opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5" :style="{ color: currentTheme.primary }">
                <span>Atur</span>
                <span>⚙️</span>
              </span>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">{{ profileData.email }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Online</span>
              <span v-if="profileData.phone" class="text-[10px] text-slate-400 dark:text-zinc-500 truncate">• {{ profileData.phone }}</span>
            </div>
          </div>
        </div>

        <!-- Navigation Menu Fitur -->
        <div class="px-4 py-2 space-y-1">
          <span class="px-3 text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">
            Fitur Menu
          </span>

          <button 
            @click="activeNav = 'all'; orderFilter = 'all'"
            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition cursor-pointer text-left"
            :class="activeNav === 'all' && orderFilter === 'all' ? 'text-white shadow-md' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800'"
            :style="activeNav === 'all' && orderFilter === 'all' ? { backgroundColor: currentTheme.primary, boxShadow: `0 8px 20px -4px ${currentTheme.shadow}` } : {}"
          >
            <LayoutDashboard class="w-4 h-4" />
            <span>Semua Titipan</span>
          </button>

          <button 
            @click="activeNav = 'radar'; orderFilter = 'unpaid'"
            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition cursor-pointer"
            :class="orderFilter === 'unpaid' ? 'text-white shadow-md' : 'text-slate-700 dark:text-zinc-300 hover:bg-rose-50 dark:hover:bg-zinc-800 hover:text-rose-600 dark:hover:text-rose-400'"
            :style="orderFilter === 'unpaid' ? { backgroundColor: currentTheme.primary, boxShadow: `0 8px 20px -4px ${currentTheme.shadow}` } : {}"
          >
            <div class="flex items-center gap-3">
              <Radio class="w-4 h-4" />
              <span>Radar Tagihan</span>
            </div>
            <span 
              v-if="unpaidCount > 0" 
              class="px-2 py-0.5 rounded-full text-[10px] font-black"
              :class="orderFilter === 'unpaid' ? 'bg-white' : 'bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-300'"
              :style="orderFilter === 'unpaid' ? { color: currentTheme.primary } : {}"
            >
              {{ unpaidCount }}
            </span>
          </button>

          <button 
            @click="activeNav = 'calculator'"
            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition cursor-pointer text-left"
            :class="activeNav === 'calculator' ? 'text-white shadow-md' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800'"
            :style="activeNav === 'calculator' ? { backgroundColor: currentTheme.primary, boxShadow: `0 8px 20px -4px ${currentTheme.shadow}` } : {}"
          >
            <Calculator class="w-4 h-4" />
            <span>Kalkulator Split-Bill</span>
          </button>
        </div>

        <!-- Daftar Room Aktif (Quick Room Switcher) -->
        <div class="px-4 py-4 mt-2 border-t border-slate-100 dark:border-zinc-800 flex-1">
          <div class="flex items-center justify-between mb-2.5 px-3">
            <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-wider">
              Room Aktif ({{ rooms.length }})
            </span>
            <button 
              @click="showNewRoomModal = true"
              class="p-1 text-xs font-extrabold hover:underline flex items-center gap-1 cursor-pointer"
              :style="{ color: currentTheme.primary }"
              title="Buat Room Baru"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Tambah</span>
            </button>
          </div>

          <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
            <div 
              v-for="room in rooms"
              :key="room.id"
              class="group relative flex items-center justify-between rounded-xl transition cursor-pointer"
              :class="activeRoomId === room.id ? 'font-black' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 font-semibold'"
              :style="activeRoomId === room.id ? { backgroundColor: currentTheme.lightBg, color: currentTheme.primary } : {}"
            >
              <button 
                @click="selectRoom(room.id)"
                class="flex-1 py-2 px-3 text-left text-xs truncate flex items-center gap-2 cursor-pointer"
              >
                <span 
                  class="w-2 h-2 rounded-full shrink-0"
                  :class="room.isOpen ? 'bg-emerald-500' : 'bg-slate-400 dark:bg-zinc-600'"
                  :title="room.isOpen ? 'Menerima pesanan' : 'Ditutup'"
                />
                <span class="truncate">{{ room.name }}</span>
              </button>

              <!-- Tombol Hapus Room Cepat di Sidebar -->
              <button 
                @click.stop="confirmDeleteRoom(room)"
                title="Hapus room ini"
                class="p-1.5 text-slate-300 dark:text-zinc-600 hover:text-red-500 hover:bg-red-100 dark:hover:bg-red-950/40 rounded-lg transition mr-1.5 cursor-pointer opacity-70 group-hover:opacity-100"
              >
                <Trash2 class="w-3.5 h-3.5" />
              </button>
            </div>

            <!-- Jika tidak ada room -->
            <div v-if="rooms.length === 0" class="text-center py-4 px-2 text-xs text-slate-400 dark:text-zinc-500">
              Belum ada room titipan.
            </div>
          </div>
        </div>
      </div>

      <!-- Bagian Bawah Sidebar (Tema, Tombol Kembali & Logout) -->
      <div class="p-4 border-t border-orange-100 dark:border-zinc-800 space-y-2.5 bg-slate-50/50 dark:bg-zinc-900/50">
        <!-- Switcher Tema (Di atas 'Kembali ke Beranda') -->
        <div class="flex items-center justify-between p-2 rounded-2xl bg-white dark:bg-zinc-800/90 border border-slate-200/80 dark:border-zinc-700/80 shadow-2xs">
          <span class="text-xs font-bold text-slate-600 dark:text-zinc-300 pl-1">
            Tema
          </span>
          <ThemeSwitcher />
        </div>

        <button 
          @click="emit('back-home')"
          class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-800 hover:text-orange-600 dark:hover:text-orange-400 transition border border-transparent hover:border-orange-200 dark:hover:border-zinc-700 cursor-pointer"
        >
          <Home class="w-4 h-4 text-slate-400 dark:text-zinc-500" />
          <span>Kembali ke Beranda</span>
        </button>

        <button 
          @click="emit('logout')"
          class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition border border-transparent hover:border-rose-200 dark:hover:border-rose-900 cursor-pointer"
        >
          <LogOut class="w-4 h-4" />
          <span>Keluar (Logout)</span>
        </button>
      </div>
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <div class="flex-1 lg:pl-72 flex flex-col min-w-0">
      
      <!-- TOP BAR KHUSUS DASHBOARD -->
      <header class="sticky top-0 z-30 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-orange-100 dark:border-zinc-800 px-4 sm:px-8 py-3 flex items-center justify-between shadow-2xs transition-colors">
        <div class="flex items-center gap-3">
          <!-- Hamburger Menu Mobile -->
          <button 
            @click="isMobileSidebarOpen = true"
            class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-zinc-300 hover:bg-orange-50 dark:hover:bg-zinc-800 hover:text-orange-600 dark:hover:text-orange-400 border border-slate-200 dark:border-zinc-700 cursor-pointer"
          >
            <MenuIcon class="w-5 h-5" />
          </button>

          <!-- Current Active Room Breadcrumb Title -->
          <div v-if="currentRoom" class="flex items-center gap-2">
            <span class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500 hidden sm:inline">Room:</span>
            <span class="text-sm sm:text-base font-black text-slate-900 dark:text-white truncate max-w-[200px] sm:max-w-md">
              {{ currentRoom.name }}
            </span>
            <span 
              class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase shrink-0"
              :class="currentRoom.isOpen ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300'"
            >
              {{ currentRoom.isOpen ? 'Buka' : 'Tutup' }}
            </span>
          </div>

          <div v-else class="text-sm font-black text-slate-900 dark:text-white">
            Dashboard Menu Utama
          </div>
        </div>

        <!-- Action Buttons di Top Bar -->
        <div class="flex items-center gap-2 sm:gap-3">
          <button 
            @click="showNewRoomModal = true"
            class="px-3.5 py-1.5 rounded-xl text-white font-black text-xs transition flex items-center gap-1.5 cursor-pointer"
            :style="{
              backgroundColor: currentTheme.primary,
              boxShadow: `0 4px 14px -2px ${currentTheme.shadow}`
            }"
          >
            <Plus class="w-4 h-4" />
            <span class="hidden sm:inline">Buat Room</span>
          </button>

          <button 
            v-if="currentRoom"
            @click="copyRoomLink"
            title="Salin Link Room"
            class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer bg-white dark:bg-zinc-800"
          >
            <Copy class="w-3.5 h-3.5" />
            <span class="hidden sm:inline">Salin Link</span>
          </button>

          <button 
            v-if="currentRoom"
            @click="shareRoomToWhatsApp"
            title="Bagikan ke WhatsApp Group"
            class="px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black transition flex items-center gap-1.5 shadow-xs cursor-pointer"
          >
            <MessageCircle class="w-4 h-4" />
            <span class="hidden sm:inline">Share WA</span>
          </button>
        </div>
      </header>

      <!-- BODY CONTENT -->
      <main class="p-4 sm:p-8 space-y-6 max-w-7xl w-full mx-auto">
        
        <!-- EMPTY STATE JIKA SEMUA ROOM DIHAPUS -->
        <div 
          v-if="rooms.length === 0" 
          class="bg-white dark:bg-zinc-900 rounded-3xl p-12 text-center border-2 border-dashed border-slate-200 dark:border-zinc-800 space-y-5 my-12"
        >
          <div 
            class="w-16 h-16 mx-auto rounded-3xl flex items-center justify-center text-3xl font-black"
            :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
          >
            🍱
          </div>
          <div class="space-y-2 max-w-md mx-auto">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Belum Ada Room Titipan Aktif</h2>
            <p class="text-slate-500 dark:text-zinc-400 text-sm font-medium">
              Semua room titipan telah dihapus. Buat room baru sekarang untuk kumpulkan pesanan makanan bersama teman!
            </p>
          </div>
          <button 
            @click="showNewRoomModal = true"
            class="px-6 py-3 rounded-2xl text-white font-black text-xs uppercase tracking-wider transition transform hover:scale-105 cursor-pointer inline-flex items-center gap-2"
            :style="{
              backgroundImage: currentTheme.gradient,
              boxShadow: `0 10px 25px -5px ${currentTheme.shadow}`
            }"
          >
            <Plus class="w-4 h-4" />
            <span>Buat Room Pertama</span>
          </button>
        </div>

        <template v-else-if="currentRoom">
          
          <!-- BANNER ROOM AKTIF & TOMBOL HAPUS ROOM -->
          <section 
            class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border-2 shadow-sm relative overflow-hidden space-y-6"
            :style="{ borderColor: currentTheme.border }"
          >
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-zinc-800">
              <div class="space-y-2">
                <div class="flex items-center gap-3 flex-wrap">
                  <span 
                    class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider flex items-center gap-1.5"
                    :class="currentRoom.isOpen ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300'"
                  >
                    <component :is="currentRoom.isOpen ? Unlock : Lock" class="w-3.5 h-3.5" />
                    {{ currentRoom.isOpen ? 'Menerima Pesanan' : 'Pesanan Dikunci' }}
                  </span>

                  <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-3 py-1 rounded-full">
                    <Clock class="w-3.5 h-3.5" :style="{ color: currentTheme.primary }" />
                    Batas Jam: <strong class="text-slate-800 dark:text-zinc-200">{{ currentRoom.targetTime }}</strong>
                  </span>

                  <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-3 py-1 rounded-full">
                    <Users class="w-3.5 h-3.5" :style="{ color: currentTheme.primary }" />
                    {{ currentRoom.orders.length }} Orang Bergabung
                  </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                  {{ currentRoom.name }}
                </h1>
                <p class="text-slate-600 dark:text-zinc-400 text-sm font-medium flex items-center gap-1.5">
                  <span>Restoran/Merchant:</span>
                  <span class="font-extrabold" :style="{ color: currentTheme.primary }">{{ currentRoom.resto }}</span>
                </p>
              </div>

              <!-- Action Bar Room: Rekening + Kunci + HAPUS ROOM -->
              <div class="flex flex-wrap items-center gap-3">
                <!-- Info Rekening Host -->
                <div 
                  @click="copyBankInfo"
                  class="border p-2.5 sm:p-3 rounded-2xl cursor-pointer transition flex items-center gap-2.5"
                  :style="{
                    backgroundColor: currentTheme.lightBg,
                    borderColor: currentTheme.border
                  }"
                  title="Klik untuk salin info transfer"
                >
                  <div 
                    class="w-8 h-8 rounded-xl text-white flex items-center justify-center font-black shrink-0"
                    :style="{ backgroundColor: currentTheme.primary }"
                  >
                    <Wallet class="w-4 h-4" />
                  </div>
                  <div class="text-xs text-left">
                    <p class="font-black text-slate-900 dark:text-white">{{ currentRoom.bankInfo.bank }}: {{ currentRoom.bankInfo.accountNumber }}</p>
                    <p class="text-slate-500 dark:text-zinc-400 font-medium">a/n {{ currentRoom.bankInfo.accountName }} (Salin)</p>
                  </div>
                </div>

                <!-- Tombol Kunci / Buka Room -->
                <button 
                  @click="toggleRoomStatus"
                  class="px-3.5 py-2.5 rounded-2xl border text-xs font-extrabold uppercase tracking-wider transition cursor-pointer flex items-center gap-1.5"
                  :class="currentRoom.isOpen 
                    ? 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300' 
                    : 'border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900'"
                >
                  <component :is="currentRoom.isOpen ? Lock : Unlock" class="w-4 h-4" />
                  <span class="hidden sm:inline">{{ currentRoom.isOpen ? 'Kunci' : 'Buka' }}</span>
                </button>

                <!-- FITUR HAPUS ROOM -->
                <button 
                  @click="confirmDeleteRoom(currentRoom)"
                  class="px-3.5 py-2.5 rounded-2xl border border-red-200 dark:border-red-900/60 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950/70 text-red-600 dark:text-red-400 font-extrabold text-xs uppercase tracking-wider transition cursor-pointer flex items-center gap-1.5"
                  title="Hapus room ini secara permanen"
                >
                  <Trash2 class="w-4 h-4" />
                  <span class="hidden sm:inline">Hapus Room</span>
                </button>
              </div>
            </div>

            <!-- RADAR TAGIHAN & SUMMARY CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Total Tagihan Keseluruhan -->
              <div class="bg-slate-50 dark:bg-zinc-800/60 p-4 rounded-2xl border border-slate-200 dark:border-zinc-700 space-y-1">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500">Total Tagihan Resto</span>
                <p class="text-2xl font-black text-slate-900 dark:text-white">
                  Rp {{ roomGrandTotal.toLocaleString('id-ID') }}
                </p>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400 font-medium">Termasuk ongkir, promo & pajak</p>
              </div>

              <!-- Total Sudah Lunas -->
              <div class="bg-emerald-50/70 dark:bg-emerald-950/30 p-4 rounded-2xl border border-emerald-200 dark:border-emerald-900/50 space-y-1">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300 flex items-center gap-1.5">
                  <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                  Terkumpul (Lunas)
                </span>
                <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">
                  Rp {{ totalPaidBill.toLocaleString('id-ID') }}
                </p>
                <div class="w-full bg-emerald-200 dark:bg-emerald-900/60 h-2 rounded-full overflow-hidden mt-1">
                  <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" :style="{ width: `${paidPercentage}%` }" />
                </div>
              </div>

              <!-- Total Masih Ngutang -->
              <div class="bg-rose-50/70 dark:bg-rose-950/30 p-4 rounded-2xl border border-rose-200 dark:border-rose-900/50 space-y-1">
                <span class="text-xs font-black uppercase tracking-wider text-rose-700 dark:text-rose-300 flex items-center gap-1.5">
                  <AlertCircle class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                  Belum Bayar (Ngutang)
                </span>
                <p class="text-2xl font-black text-rose-600 dark:text-rose-400">
                  Rp {{ totalUnpaidBill.toLocaleString('id-ID') }}
                </p>
                <p class="text-[11px] font-bold" :class="unpaidCount > 0 ? 'text-rose-600 dark:text-rose-400 animate-pulse' : 'text-slate-500 dark:text-zinc-400'">
                  {{ unpaidCount > 0 ? `${unpaidCount} orang belum transfer` : 'Semua sudah beres lunas! 🎉' }}
                </p>
              </div>
            </div>
          </section>

          <!-- 2-COLUMN WORKSPACE: LEFT (SPLIT CALCULATOR & TAMBAH PESANAN) / RIGHT (DAFTAR PESANAN) -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- KOLOM KIRI (5 Kolom): CALCULATOR & FORM PESANAN -->
            <div class="lg:col-span-5 space-y-6">
              
              <!-- CARD: TAMBAH PESANAN TITIPAN -->
              <div 
                class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border-2 shadow-xl space-y-5"
                :style="{ borderColor: currentTheme.border }"
              >
                <div class="flex items-center gap-2">
                  <div 
                    class="w-8 h-8 rounded-xl flex items-center justify-center font-black"
                    :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
                  >
                    <ShoppingBag class="w-4 h-4" />
                  </div>
                  <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    Tambah Titipan Makanan
                  </h3>
                </div>

                <form @submit.prevent="addOrder" class="space-y-4">
                  <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                      Nama Pemesan <span :style="{ color: currentTheme.primary }">*</span>
                    </label>
                    <input 
                      v-model="newPerson"
                      type="text" 
                      placeholder="Misal: Kevin, Salsa, Budi"
                      class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                      Menu Makanan / Minuman <span :style="{ color: currentTheme.primary }">*</span>
                    </label>
                    <input 
                      v-model="newItem"
                      type="text" 
                      placeholder="Misal: Nasi Goreng Gila + Telur Ceplok"
                      class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
                      required
                    />
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                        Harga Satuan (Rp) <span :style="{ color: currentTheme.primary }">*</span>
                      </label>
                      <input 
                        v-model.number="newPrice"
                        type="number" 
                        min="0"
                        step="500"
                        class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 font-medium transition"
                        required
                      />
                    </div>

                    <div>
                      <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                        Jumlah (Qty)
                      </label>
                      <input 
                        v-model.number="newQty"
                        type="number" 
                        min="1"
                        class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 font-medium transition"
                      />
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                      Catatan Tambahan <span class="text-slate-400 dark:text-zinc-500 font-normal">(Opsional)</span>
                    </label>
                    <input 
                      v-model="newNotes"
                      type="text" 
                      placeholder="Misal: Sambal banyak, es batu dipisah"
                      class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
                    />
                  </div>

                  <button 
                    type="submit"
                    class="w-full py-3 px-4 rounded-2xl text-white font-black text-xs uppercase tracking-wider hover:-translate-y-0.5 transition transform cursor-pointer flex items-center justify-center gap-2"
                    :style="{
                      backgroundImage: currentTheme.gradient,
                      boxShadow: `0 10px 25px -5px ${currentTheme.shadow}`
                    }"
                  >
                    <Plus class="w-4 h-4" />
                    <span>Simpan Pesanan Titipan</span>
                  </button>
                </form>
              </div>

              <!-- CARD: KALKULATOR AUTO SPLIT-BILL & PAJAK -->
              <div 
                class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border-2 shadow-xl space-y-5"
                :style="{ borderColor: currentTheme.border }"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div 
                      class="w-8 h-8 rounded-xl flex items-center justify-center font-black"
                      :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
                    >
                      <Calculator class="w-4 h-4" />
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                      Kalkulator Auto Split-Bill
                    </h3>
                  </div>

                  <span 
                    class="text-[11px] font-bold px-2.5 py-1 rounded-full"
                    :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
                  >
                    {{ currentRoom.splitType === 'proportional' ? 'Proporsional' : 'Bagi Rata' }}
                  </span>
                </div>

                <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                  Ubah ongkir, voucher promo atau pajak. Sistem otomatis menghitung ulang porsi bayar tiap orang.
                </p>

                <div class="space-y-3 pt-2 text-xs">
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block font-bold text-slate-600 dark:text-zinc-300 mb-1">Ongkir (Rp)</label>
                      <input 
                        v-model.number="currentRoom.deliveryFee"
                        type="number"
                        min="0"
                        step="1000"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 dark:text-zinc-100 focus:outline-none font-semibold"
                      />
                    </div>

                    <div>
                      <label class="block font-bold text-slate-600 dark:text-zinc-300 mb-1">Biaya Layanan/Jastip</label>
                      <input 
                        v-model.number="currentRoom.serviceFee"
                        type="number"
                        min="0"
                        step="500"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 dark:text-zinc-100 focus:outline-none font-semibold"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block font-bold text-slate-600 dark:text-zinc-300 mb-1">Diskon Promo / Voucher</label>
                      <input 
                        v-model.number="currentRoom.discount"
                        type="number"
                        min="0"
                        step="1000"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none font-semibold text-emerald-600 dark:text-emerald-400"
                      />
                    </div>

                    <div>
                      <label class="block font-bold text-slate-600 dark:text-zinc-300 mb-1">Pajak Resto / PB1 (%)</label>
                      <input 
                        v-model.number="currentRoom.taxPercent"
                        type="number"
                        min="0"
                        max="100"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 dark:text-zinc-100 focus:outline-none font-semibold"
                      />
                    </div>
                  </div>

                  <!-- Metode Pembagian Biaya -->
                  <div class="pt-2">
                    <label class="block font-bold text-slate-600 dark:text-zinc-300 mb-1.5">Metode Pembagian Ongkir & Promo:</label>
                    <div class="grid grid-cols-2 gap-2">
                      <button 
                        type="button"
                        @click="currentRoom.splitType = 'proportional'"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                        :class="currentRoom.splitType === 'proportional' 
                          ? 'text-white shadow-xs' 
                          : 'bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-200 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-700'"
                        :style="currentRoom.splitType === 'proportional' ? {
                          backgroundColor: currentTheme.primary,
                          borderColor: currentTheme.primary
                        } : {}"
                      >
                        Proporsional (Adil)
                      </button>
                      <button 
                        type="button"
                        @click="currentRoom.splitType = 'equal'"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition text-center cursor-pointer"
                        :class="currentRoom.splitType === 'equal' 
                          ? 'text-white shadow-xs' 
                          : 'bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-200 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-700'"
                        :style="currentRoom.splitType === 'equal' ? {
                          backgroundColor: currentTheme.primary,
                          borderColor: currentTheme.primary
                        } : {}"
                      >
                        Bagi Rata (Sama)
                      </button>
                    </div>
                  </div>

                  <!-- Rangkuman Ringkas -->
                  <div class="pt-3 border-t border-slate-100 dark:border-zinc-800 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500 dark:text-zinc-400">
                      <span>Subtotal Makanan ({{ currentRoom.orders.length }} item)</span>
                      <span class="font-bold text-slate-800 dark:text-zinc-200">Rp {{ roomSubtotal.toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-zinc-400">
                      <span>Pajak Resto ({{ currentRoom.taxPercent }}%)</span>
                      <span class="font-bold text-slate-800 dark:text-zinc-200">Rp {{ roomTaxAmount.toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-zinc-400">
                      <span>Ongkir + Jastip - Promo</span>
                      <span class="font-bold" :class="roomNetExtraFees <= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-zinc-200'">
                        {{ roomNetExtraFees <= 0 ? `-Rp ${Math.abs(roomNetExtraFees).toLocaleString('id-ID')}` : `+Rp ${roomNetExtraFees.toLocaleString('id-ID')}` }}
                      </span>
                    </div>
                    <div 
                      class="flex justify-between font-black text-sm pt-2 border-t border-dashed border-slate-200 dark:border-zinc-700"
                      :style="{ color: currentTheme.primary }"
                    >
                      <span>Total Tagihan Bersih</span>
                      <span>Rp {{ roomGrandTotal.toLocaleString('id-ID') }}</span>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- KOLOM KANAN (7 Kolom): DAFTAR PESANAN & RADAR TAGIHAN -->
            <div class="lg:col-span-7 space-y-6">
              <div 
                class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border-2 shadow-xl space-y-6"
                :style="{ borderColor: currentTheme.border }"
              >
                
                <!-- Header List & Filter Tabs -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-zinc-800">
                  <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                      <span>Daftar Pesanan & Radar Tagihan</span>
                      <span 
                        class="px-2.5 py-0.5 rounded-full text-xs font-black"
                        :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
                      >
                        {{ detailedOrders.length }}
                      </span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                      Klik status untuk ubah lunas/ngutang atau kirim rincian tagihan via WhatsApp.
                    </p>
                  </div>

                  <!-- Filter Buttons -->
                  <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-zinc-800 p-1 rounded-2xl shrink-0">
                    <button 
                      @click="orderFilter = 'all'"
                      class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer"
                      :class="orderFilter === 'all' ? 'bg-white dark:bg-zinc-700 shadow-xs' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'"
                      :style="orderFilter === 'all' ? { color: currentTheme.primary } : {}"
                    >
                      Semua
                    </button>
                    <button 
                      @click="orderFilter = 'unpaid'"
                      class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer"
                      :class="orderFilter === 'unpaid' ? 'bg-white dark:bg-zinc-700 text-rose-600 dark:text-rose-400 shadow-xs' : 'text-slate-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400'"
                    >
                      Ngutang ({{ unpaidCount }})
                    </button>
                    <button 
                      @click="orderFilter = 'paid'"
                      class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition cursor-pointer"
                      :class="orderFilter === 'paid' ? 'bg-white dark:bg-zinc-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400'"
                    >
                      Lunas
                    </button>
                  </div>
                </div>

                <!-- Empty State Orders -->
                <div v-if="filteredOrders.length === 0" class="text-center py-12 space-y-3 bg-slate-50 dark:bg-zinc-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-zinc-700">
                  <div 
                    class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-xl font-black"
                    :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
                  >
                    🍽️
                  </div>
                  <p class="font-bold text-slate-700 dark:text-zinc-300 text-sm">Belum ada pesanan di kategori ini</p>
                  <p class="text-xs text-slate-400 dark:text-zinc-500">Tambahkan pesanan di form sebelah kiri atau bagikan link room ke temanmu.</p>
                </div>

                <!-- Orders Cards List -->
                <div v-else class="space-y-3">
                  <div 
                    v-for="order in filteredOrders" 
                    :key="order.id"
                    class="p-4 sm:p-5 rounded-2xl border transition-all hover:shadow-md space-y-3"
                    :class="order.isPaid ? 'bg-slate-50/70 dark:bg-zinc-800/50 border-slate-200 dark:border-zinc-700' : 'bg-white dark:bg-zinc-800/90 shadow-sm'"
                    :style="!order.isPaid ? { borderColor: currentTheme.border } : {}"
                  >
                    <!-- Row 1: Header Pemesan & Status Badge -->
                    <div class="flex items-center justify-between gap-3">
                      <div class="flex items-center gap-2.5">
                        <div 
                          class="w-8 h-8 rounded-full text-white font-black text-xs flex items-center justify-center uppercase shadow-xs"
                          :style="{ backgroundImage: currentTheme.gradient }"
                        >
                          {{ order.personName.charAt(0) }}
                        </div>
                        <div>
                          <h4 class="font-black text-slate-900 dark:text-white text-sm">{{ order.personName }}</h4>
                          <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                            {{ order.itemName }} <span v-if="order.qty > 1" class="font-bold" :style="{ color: currentTheme.primary }">({{ order.qty }}x)</span>
                          </p>
                        </div>
                      </div>

                      <!-- Status Badge Button (Interactive Toggle) -->
                      <button 
                        @click="togglePaid(order.id)"
                        class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider transition transform hover:scale-105 cursor-pointer flex items-center gap-1.5 shadow-xs"
                        :class="order.isPaid 
                          ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-900 border border-emerald-300 dark:border-emerald-800' 
                          : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 hover:bg-rose-200 dark:hover:bg-rose-900 border border-rose-300 dark:border-rose-800 animate-pulse'"
                        :title="order.isPaid ? 'Klik untuk tandai belum bayar' : 'Klik untuk tandai sudah lunas'"
                      >
                        <component :is="order.isPaid ? CheckCircle2 : AlertCircle" class="w-3.5 h-3.5" />
                        <span>{{ order.isPaid ? 'Lunas' : 'Ngutang' }}</span>
                      </button>
                    </div>

                    <!-- Notes if any -->
                    <div v-if="order.notes" class="text-[11px] font-medium text-slate-500 dark:text-zinc-300 bg-white/80 dark:bg-zinc-900/90 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-zinc-700">
                      <span class="font-bold" :style="{ color: currentTheme.primary }">Catatan:</span> {{ order.notes }}
                    </div>

                    <!-- Row 2: Price Calculation Breakdown & Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-zinc-700/80 text-xs">
                      <div class="space-y-0.5 text-slate-500 dark:text-zinc-400 text-[11px]">
                        <div>
                          Harga Menu: <span class="font-bold text-slate-800 dark:text-zinc-200">Rp {{ order.itemTotal.toLocaleString('id-ID') }}</span>
                          <span v-if="order.taxShare > 0"> + Pajak: Rp {{ order.taxShare.toLocaleString('id-ID') }}</span>
                          <span v-if="order.extraShare !== 0"> 
                            {{ order.extraShare > 0 ? `+ Ongkir: Rp ${order.extraShare.toLocaleString('id-ID')}` : `- Promo: Rp ${Math.abs(order.extraShare).toLocaleString('id-ID')}` }}
                          </span>
                        </div>
                        <div class="text-sm font-black text-slate-900 dark:text-white">
                          Total Bayar: <span class="font-black" :style="{ color: currentTheme.primary }">Rp {{ order.finalBill.toLocaleString('id-ID') }}</span>
                        </div>
                      </div>

                      <!-- Actions for this Order -->
                      <div class="flex items-center gap-2 self-end sm:self-center">
                        <button 
                          @click="sendWhatsAppBill(order)"
                          class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-black text-xs transition flex items-center gap-1.5 cursor-pointer"
                          title="Kirim rincian tagihan ke WhatsApp teman ini"
                        >
                          <MessageCircle class="w-3.5 h-3.5" />
                          <span>Tagih WA</span>
                        </button>

                        <button 
                          @click="deleteOrder(order.id)"
                          class="p-2 rounded-xl text-slate-400 dark:text-zinc-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                          title="Hapus pesanan ini"
                        >
                          <Trash2 class="w-4 h-4" />
                        </button>
                      </div>
                    </div>

                  </div>
                </div>

              </div>
            </div>

          </div>
        </template>
      </main>

    </div>

    <!-- ==================== MODAL: BUAT ROOM BARU ==================== -->
    <div 
      v-if="showNewRoomModal" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
    >
      <div 
        class="bg-white dark:bg-zinc-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border-2 space-y-6 animate-in fade-in zoom-in-95 duration-200"
        :style="{ borderColor: currentTheme.border }"
      >
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
          <div class="flex items-center gap-2.5">
            <div 
              class="w-9 h-9 rounded-2xl flex items-center justify-center font-black"
              :style="{ backgroundColor: currentTheme.lightBg, color: currentTheme.primary }"
            >
              <Plus class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Buat Room Titipan Baru</h3>
              <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">Buat link pesanan bersama untuk grupmu.</p>
            </div>
          </div>
          <button 
            @click="showNewRoomModal = false"
            class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 font-bold p-1 cursor-pointer"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="createRoom" class="space-y-4 text-xs">
          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Nama Kegiatan / Room <span :style="{ color: currentTheme.primary }">*</span>
            </label>
            <input 
              v-model="newRoomName"
              type="text" 
              placeholder="Misal: Makan Siang Divisi IT, Nobar Final"
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
              required
            />
          </div>

          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Restoran / Tempat Makan <span :style="{ color: currentTheme.primary }">*</span>
            </label>
            <input 
              v-model="newRoomResto"
              type="text" 
              placeholder="Misal: Mie Gacoan Tebet, HokBen Mall"
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
              required
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Batas Jam Pemesanan
              </label>
              <input 
                v-model="newRoomTarget"
                type="text" 
                placeholder="Misal: 11:45 WIB"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
              />
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Tujuan Pembayaran (Bank/E-Wallet)
              </label>
              <select 
                v-model="newRoomBank"
                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 font-medium transition cursor-pointer"
              >
                <option value="BCA">BCA</option>
                <option value="Mandiri">Mandiri</option>
                <option value="BRI">BRI</option>
                <option value="BNI">BNI</option>
                <option value="GoPay">GoPay</option>
                <option value="OVO">OVO</option>
                <option value="Dana">Dana</option>
                <option value="QRIS">QRIS</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                No. Rekening / No. HP E-Wallet
              </label>
              <input 
                v-model="newRoomRek"
                type="text" 
                placeholder="1234567890"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
              />
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Atas Nama (a/n)
              </label>
              <input 
                v-model="newRoomRekName"
                type="text" 
                placeholder="Nama Pemilik"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:outline-none text-sm text-slate-800 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 font-medium transition"
              />
            </div>
          </div>

          <div class="pt-3 flex items-center justify-end gap-3">
            <button 
              type="button"
              @click="showNewRoomModal = false"
              class="px-5 py-2.5 rounded-2xl border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 font-extrabold text-xs uppercase tracking-wider transition cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              class="px-6 py-2.5 rounded-2xl text-white font-black text-xs uppercase tracking-wider transition cursor-pointer"
              :style="{
                backgroundImage: currentTheme.gradient,
                boxShadow: `0 10px 25px -5px ${currentTheme.shadow}`
              }"
            >
              Buat Room
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ==================== MODAL: KONFIRMASI HAPUS ROOM ==================== -->
    <div 
      v-if="showDeleteRoomModal && roomToDelete" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
    >
      <div class="bg-white dark:bg-zinc-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border-2 border-red-100 dark:border-red-900/40 space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 flex items-center justify-center font-black mx-auto">
          <Trash2 class="w-6 h-6" />
        </div>

        <div class="text-center space-y-2">
          <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Hapus Room Titipan?</h3>
          <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium leading-relaxed">
            Apakah Anda yakin ingin menghapus room <strong class="text-slate-800 dark:text-zinc-200">"{{ roomToDelete.name }}"</strong>? Seluruh data pesanan di dalam room ini akan dihapus secara permanen.
          </p>
        </div>

        <div class="pt-2 flex items-center justify-center gap-3">
          <button 
            type="button"
            @click="showDeleteRoomModal = false; roomToDelete = null"
            class="px-5 py-2.5 rounded-2xl border border-slate-200 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-extrabold text-xs uppercase tracking-wider transition cursor-pointer"
          >
            Batal
          </button>
          <button 
            type="button"
            @click="executeDeleteRoom"
            class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-500/30 transition cursor-pointer"
          >
            Ya, Hapus Room
          </button>
        </div>
      </div>
    </div>

    <!-- ==================== MODAL: PENGATURAN PROFIL ==================== -->
    <ProfileSettingModal
      :is-open="isProfileModalOpen"
      :user="profileData"
      @close="isProfileModalOpen = false"
      @save="handleProfileSave"
    />

  </div>
</template>
