<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { 
  X, Sparkles, Check, Copy, Share2, Calculator, Users, 
  Receipt, ArrowRight, ShieldCheck, Bell, QrCode, Lightbulb,
  CheckCircle2, Clock, Plus, Minus, MessageSquare, Flame
} from 'lucide-vue-next'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  cardData: {
    type: Object,
    default: () => null
  }
})

const emit = defineEmits(['close'])

// Close modal handler
const closeModal = () => {
  emit('close')
}

// Handle Keyboard ESC
const handleKeyDown = (e) => {
  if (e.key === 'Escape' && props.isOpen) {
    closeModal()
  }
}

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
  document.body.style.overflow = ''
})

// --- Interactive Simulation States ---

// 1. Link Copy & WA Share Widget State
const customRoomName = ref('Makan Siang Sirkel Tech')
const linkCopied = ref(false)
const waShared = ref(false)

const generatedLink = computed(() => {
  const slug = customRoomName.value.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-') || 'room-titip'
  return `nitipdong.com/join/${slug}`
})

const copyGeneratedLink = () => {
  navigator.clipboard?.writeText(`https://${generatedLink.value}`)
  linkCopied.value = true
  setTimeout(() => linkCopied.value = false, 2500)
}

const simulateWAShare = () => {
  waShared.value = true
  setTimeout(() => waShared.value = false, 3000)
}

// 2. Split Bill Calculator Widget State
const calcSubtotal = ref(120000)
const calcOngkir = ref(15000)
const calcTaxPercent = ref(10)
const calcPeopleCount = ref(3)

const calcTaxAmount = computed(() => Math.round((calcSubtotal.value * calcTaxPercent.value) / 100))
const calcTotalAll = computed(() => calcSubtotal.value + calcOngkir.value + calcTaxAmount.value)
const calcPerPerson = computed(() => Math.round(calcTotalAll.value / (calcPeopleCount.value || 1)))

// 3. Radar Payment Status Tracker Widget State
const trackerOrders = ref([
  { id: 1, name: 'Budi Pratama', item: 'Ayam Geprek Sambal Ijo + Es Teh', price: 28000, status: 'LUNAS', avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi' },
  { id: 2, name: 'Siti Rahma', item: 'Ramen Tonkotsu Spicy (Level 3)', price: 45000, status: 'LUNAS', avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Siti' },
  { id: 3, name: 'Dion Permana', item: 'Boba Brown Sugar + Toast', price: 32000, status: 'NGUTANG', avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Dion' },
  { id: 4, name: 'Fathan Tech', item: 'Nasi Goreng Seafood Extra Telur', price: 35000, status: 'NGUTANG', avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Fathan' }
])

const alertMessage = ref('')

const toggleOrderStatus = (id) => {
  const target = trackerOrders.value.find(o => o.id === id)
  if (target) {
    target.status = target.status === 'LUNAS' ? 'NGUTANG' : 'LUNAS'
    alertMessage.value = `Status ${target.name} diubah jadi ${target.status}! ✨`
    setTimeout(() => alertMessage.value = '', 2500)
  }
}

const sendRemindAlert = (name) => {
  alertMessage.value = `🔔 Pesan Pengingat WA terisi otomatis untuk ${name}: "Woi, tagihan makan siang ditunggu ya bro/sis! 🥑"`
  setTimeout(() => alertMessage.value = '', 3500)
}

// 4. Guest Order Picker Simulation State
const selectedMenu = ref('Ayam Bakar Madu')
const iceNote = ref('Less Ice')
const customText = ref('Bumbu kecap pisah ya mas')
const orderSuccess = ref(false)

const submitSimulatedOrder = () => {
  orderSuccess.value = true
  setTimeout(() => orderSuccess.value = false, 3000)
}

// 5. Settlement QRIS State
const qrScanned = ref(false)
const simulateQRScan = () => {
  qrScanned.value = true
  setTimeout(() => qrScanned.value = false, 3000)
}
</script>

<template>
  <Teleport to="body">
    <Transition name="motion-backdrop">
      <div 
        v-if="isOpen && cardData" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10 overflow-y-auto bg-slate-950/70 backdrop-blur-md transition-all duration-300"
        @click.self="closeModal"
      >
        <!-- Modal Card Container with Motion Spring Physics -->
        <Transition name="motion-card" appear>
          <div 
            v-if="isOpen"
            class="relative w-full max-w-3xl bg-white dark:bg-slate-900 rounded-3xl border-2 border-orange-200/80 shadow-2xl overflow-hidden my-auto flex flex-col max-h-[90vh]"
            @click.stop
          >
            <!-- Header Glow Beam Background -->
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-orange-500 via-amber-400 to-emerald-400" />
            
            <!-- Modal Header -->
            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-start justify-between gap-4 bg-slate-50/50">
              <div class="flex items-center gap-4">
                <div 
                  class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-lg shrink-0"
                  :class="cardData.colorClass || 'bg-orange-500 text-white shadow-orange-500/30'"
                >
                  <span>{{ cardData.icon }}</span>
                </div>
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-0.5 rounded-full bg-orange-100 text-orange-700 font-extrabold text-[11px] uppercase tracking-wider">
                      {{ cardData.badge || 'DETAIL FITUR' }}
                    </span>
                    <span class="flex items-center gap-1 text-[11px] font-bold text-slate-400">
                      <Sparkles class="w-3.5 h-3.5 text-amber-500 animate-spin-slow" /> Interactive Demo
                    </span>
                  </div>
                  <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ cardData.title }}
                  </h3>
                </div>
              </div>

              <!-- Close Button -->
              <button 
                @click="closeModal"
                class="p-2.5 rounded-full bg-slate-100 hover:bg-orange-100 text-slate-500 hover:text-orange-600 transition duration-200 cursor-pointer shrink-0 hover:rotate-90 transform"
                title="Tutup (Esc)"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Modal Content Body (Scrollable) -->
            <div class="p-6 sm:p-8 space-y-8 overflow-y-auto custom-scrollbar flex-1">
              
              <!-- Tagline & Description -->
              <div class="space-y-3 bg-gradient-to-r from-orange-50/60 to-amber-50/40 p-5 rounded-2xl border border-orange-100">
                <p class="text-slate-800 text-base sm:text-lg font-bold leading-relaxed">
                  "{{ cardData.subtitle }}"
                </p>
                <p class="text-slate-600 text-sm leading-relaxed font-medium">
                  {{ cardData.description }}
                </p>
              </div>

              <!-- INTERACTIVE DEMO WIDGET SECTION -->
              <div class="bg-slate-900 text-slate-100 p-6 rounded-3xl shadow-xl border border-slate-800 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping" />
                    <span class="text-xs font-black uppercase tracking-wider text-slate-300">Live Interactive Demo</span>
                  </div>
                  <span class="text-[11px] font-bold text-slate-400">Cobain Langsung ✦</span>
                </div>

                <!-- WIDGET 1: Link Generator & WA Share -->
                <div v-if="cardData.interactiveType === 'link-copy'" class="space-y-4 pt-1">
                  <label class="block text-xs font-bold text-slate-400">Ketik Nama Room / Sesi Titip Makanan:</label>
                  <div class="flex flex-col sm:flex-row gap-2">
                    <input 
                      v-model="customRoomName" 
                      type="text"
                      class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold text-white focus:outline-none focus:border-orange-500"
                      placeholder="Contoh: Makan Siang Resto Bebek"
                    />
                    <button 
                      @click="copyGeneratedLink"
                      class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs flex items-center justify-center gap-2 transition shadow-lg cursor-pointer"
                    >
                      <Copy v-if="!linkCopied" class="w-4 h-4" />
                      <Check v-else class="w-4 h-4 text-emerald-300" />
                      <span>{{ linkCopied ? 'Tersalin ke Clipboard!' : 'Salin Link Instan' }}</span>
                    </button>
                  </div>

                  <!-- Live Link Result Box -->
                  <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between text-xs font-mono">
                    <span class="text-orange-400 truncate">https://{{ generatedLink }}</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase shrink-0">Aktif & Siap Share</span>
                  </div>

                  <!-- WA Share Demo Box -->
                  <div class="bg-emerald-950/40 border border-emerald-800/60 p-4 rounded-xl space-y-2">
                    <div class="flex items-center justify-between text-xs">
                      <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                        <MessageSquare class="w-4 h-4" /> Preview Pesan WhatsApp Sirkel:
                      </span>
                      <button 
                        @click="simulateWAShare"
                        class="text-[11px] font-extrabold bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 px-3 py-1 rounded-lg border border-emerald-500/40 transition cursor-pointer"
                      >
                        {{ waShared ? '✓ Simulasi WhatsApp Terkirim!' : 'Simulasi Share WA' }}
                      </button>
                    </div>
                    <p class="text-xs text-slate-300 bg-slate-900/90 p-3 rounded-lg border border-slate-800 font-sans italic">
                      "Guys, nitip <span class="font-bold text-orange-400">{{ customRoomName || 'Makan Siang' }}</span> yuk! Langsung klik link ini buat pilih pesanan lo sendiri ya: https://{{ generatedLink }}"
                    </p>
                  </div>
                </div>

                <!-- WIDGET 2: Split Bill Calculator -->
                <div v-else-if="cardData.interactiveType === 'split-calculator'" class="space-y-4 pt-1">
                  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700">
                      <span class="text-slate-400 block text-[10px] font-bold">Subtotal Makanan</span>
                      <input 
                        v-model.number="calcSubtotal" 
                        type="number" step="5000"
                        class="w-full bg-slate-900 border border-slate-700 text-orange-400 font-extrabold text-sm px-2 py-1 rounded mt-1 focus:outline-none"
                      />
                    </div>
                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700">
                      <span class="text-slate-400 block text-[10px] font-bold">Ongkir Ojol</span>
                      <input 
                        v-model.number="calcOngkir" 
                        type="number" step="1000"
                        class="w-full bg-slate-900 border border-slate-700 text-amber-400 font-extrabold text-sm px-2 py-1 rounded mt-1 focus:outline-none"
                      />
                    </div>
                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700">
                      <span class="text-slate-400 block text-[10px] font-bold">Pajak Resto (PB1)</span>
                      <div class="flex items-center gap-1 mt-1">
                        <input 
                          v-model.number="calcTaxPercent" 
                          type="number" min="0" max="25"
                          class="w-full bg-slate-900 border border-slate-700 text-emerald-400 font-extrabold text-sm px-2 py-1 rounded focus:outline-none"
                        />
                        <span class="text-xs font-bold text-slate-400">%</span>
                      </div>
                    </div>
                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700">
                      <span class="text-slate-400 block text-[10px] font-bold">Jumlah Anggota</span>
                      <input 
                        v-model.number="calcPeopleCount" 
                        type="number" min="1" max="15"
                        class="w-full bg-slate-900 border border-slate-700 text-blue-400 font-extrabold text-sm px-2 py-1 rounded mt-1 focus:outline-none"
                      />
                    </div>
                  </div>

                  <!-- Calculation Result Banner -->
                  <div class="bg-gradient-to-r from-orange-500/20 to-amber-500/20 p-4 rounded-2xl border border-orange-500/40 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                      <span class="text-xs font-bold text-slate-300 block">Total Keseluruhan + Pajak:</span>
                      <span class="text-xl font-black text-white">Rp {{ calcTotalAll.toLocaleString() }}</span>
                    </div>
                    <div class="text-right sm:text-right w-full sm:w-auto bg-slate-950 p-3 rounded-xl border border-slate-800">
                      <span class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider block">Est. Tagihan Per-Orang</span>
                      <span class="text-2xl font-black text-orange-400">Rp {{ calcPerPerson.toLocaleString() }}</span>
                    </div>
                  </div>
                </div>

                <!-- WIDGET 3: Radar Payment Tracker -->
                <div v-else-if="cardData.interactiveType === 'radar-tracker'" class="space-y-4 pt-1">
                  <p class="text-xs text-slate-400">Klik tombol status untuk mengubah status bayar secara simulasi:</p>
                  
                  <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                    <div 
                      v-for="order in trackerOrders" 
                      :key="order.id"
                      class="bg-slate-800/80 p-3 rounded-xl border border-slate-700 flex items-center justify-between gap-2"
                    >
                      <div class="flex items-center gap-3">
                        <img :src="order.avatar" class="w-8 h-8 rounded-full bg-slate-700" />
                        <div>
                          <div class="text-xs font-extrabold text-white">{{ order.name }}</div>
                          <div class="text-[11px] text-slate-400">{{ order.item }} • <span class="text-orange-400 font-bold">Rp {{ order.price.toLocaleString() }}</span></div>
                        </div>
                      </div>

                      <div class="flex items-center gap-2">
                        <button 
                          v-if="order.status === 'NGUTANG'"
                          @click="sendRemindAlert(order.name)"
                          class="p-1.5 rounded-lg bg-orange-500/20 hover:bg-orange-500/40 text-orange-300 text-[10px] font-extrabold transition cursor-pointer"
                          title="Kirim pengingat WA"
                        >
                          <Bell class="w-3.5 h-3.5" />
                        </button>
                        <button 
                          @click="toggleOrderStatus(order.id)"
                          class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase transition cursor-pointer"
                          :class="order.status === 'LUNAS' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-400 border border-rose-500/40 animate-pulse'"
                        >
                          {{ order.status }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Alert Toast -->
                  <div v-if="alertMessage" class="p-3 bg-orange-500/20 border border-orange-500/50 rounded-xl text-xs font-bold text-orange-300 animate-fadeIn">
                    {{ alertMessage }}
                  </div>
                </div>

                <!-- WIDGET 4: Step 1 Room Setup Visualizer -->
                <div v-else-if="cardData.interactiveType === 'room-builder'" class="space-y-4 pt-1">
                  <div class="space-y-3 bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs">
                    <div class="flex items-center justify-between text-slate-400">
                      <span>Metode Pembayaran Host:</span>
                      <span class="font-bold text-orange-400">QRIS / Transfer Bank</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-400">
                      <span>Batas Waktu Order:</span>
                      <span class="font-bold text-amber-400">12:30 WIB (Otomatis Lock)</span>
                    </div>
                    <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                      <span class="font-bold text-slate-300">Status Room:</span>
                      <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold uppercase">● Menerima Pesanan</span>
                    </div>
                  </div>
                </div>

                <!-- WIDGET 5: Step 2 Guest Menu Order Simulation -->
                <div v-else-if="cardData.interactiveType === 'order-picker'" class="space-y-3 pt-1">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700 space-y-2">
                      <span class="text-slate-400 text-[10px] font-bold block">Pilih Menu Makanan:</span>
                      <select v-model="selectedMenu" class="w-full bg-slate-900 text-white font-bold px-3 py-2 rounded-lg border border-slate-700 focus:outline-none">
                        <option>Ayam Bakar Madu (Rp 28.000)</option>
                        <option>Nasi Goreng Gila (Rp 25.000)</option>
                        <option>Es Teh Manis Jumbo (Rp 6.000)</option>
                      </select>
                    </div>

                    <div class="bg-slate-800 p-3 rounded-xl border border-slate-700 space-y-2">
                      <span class="text-slate-400 text-[10px] font-bold block">Catatan Khusus Sesuai Selera:</span>
                      <input 
                        v-model="customText" 
                        type="text" 
                        class="w-full bg-slate-900 text-slate-200 text-xs px-3 py-2 rounded-lg border border-slate-700 focus:outline-none"
                        placeholder="Misal: pedas sedang, bumbu pisah"
                      />
                    </div>
                  </div>

                  <button 
                    @click="submitSimulatedOrder"
                    class="w-full py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs transition cursor-pointer flex items-center justify-center gap-2"
                  >
                    <span>{{ orderSuccess ? '✓ Pesanan Berhasil Dimasukkan ke Room!' : 'Simulasi Tambah Pesanan Saya' }}</span>
                  </button>
                </div>

                <!-- WIDGET 6: Step 3 Settlement & QRIS Mockup -->
                <div v-else-if="cardData.interactiveType === 'payment-settle'" class="space-y-4 pt-1">
                  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-950 p-4 rounded-xl border border-slate-800">
                    <div class="space-y-1 text-center sm:text-left">
                      <span class="text-xs font-bold text-slate-400 block">QRIS Pembayaran Host Room</span>
                      <span class="text-base font-black text-orange-400 block">NIM: 0812-3456-7890 (BCA/Gopay)</span>
                      <span class="text-[11px] text-emerald-400 font-bold block">Status: Verifikasi Instan ⚡</span>
                    </div>

                    <div class="relative group cursor-pointer" @click="simulateQRScan">
                      <div class="w-24 h-24 bg-white p-2 rounded-xl flex items-center justify-center shadow-lg border-2 border-orange-500">
                        <QrCode class="w-20 h-20 text-slate-900" />
                      </div>
                      <div class="absolute inset-0 bg-orange-500/20 backdrop-blur-xs rounded-xl flex items-center justify-center text-[10px] font-bold text-white text-center p-1 opacity-0 group-hover:opacity-100 transition">
                        Klik Scan QRIS Demo
                      </div>
                    </div>
                  </div>

                  <div v-if="qrScanned" class="p-3 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-xs font-bold text-emerald-300 text-center animate-fadeIn">
                    ✓ Pembayaran Berhasil Dikonfirmasi Host! Status Berubah Jadi LUNAS.
                  </div>
                </div>

              </div>

              <!-- KEY HIGHLIGHTS / KEUNGGULAN UTAMA -->
              <div class="space-y-4">
                <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                  <ShieldCheck class="w-5 h-5 text-orange-500" />
                  Keunggulan & Manfaat Utama:
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div 
                    v-for="(item, idx) in cardData.highlights" 
                    :key="idx"
                    class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-orange-300 transition space-y-1.5"
                  >
                    <div class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                      <span class="text-orange-500 text-base">✦</span>
                      <span>{{ item.title }}</span>
                    </div>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">
                      {{ item.desc }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- TIPS & TRICKS SECTION -->
              <div v-if="cardData.tips && cardData.tips.length" class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200/80 space-y-2">
                <div class="flex items-center gap-2 font-black text-amber-900 text-xs uppercase tracking-wider">
                  <Lightbulb class="w-4 h-4 text-amber-600" /> Tips Penggunaan Sirkel:
                </div>
                <ul class="space-y-1.5 pl-5 list-disc text-xs text-amber-800 font-medium">
                  <li v-for="(tip, tIdx) in cardData.tips" :key="tIdx">
                    {{ tip }}
                  </li>
                </ul>
              </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-6 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-4">
              <span class="text-xs text-slate-500 font-medium hidden sm:inline">Tekan Esc atau klik luar area untuk menutup</span>
              
              <button 
                @click="closeModal"
                class="w-full sm:w-auto px-8 py-3 rounded-full bg-slate-900 hover:bg-orange-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg transition duration-200 cursor-pointer text-center"
              >
                Tutup Detail
              </button>
            </div>

          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Motion Framer Style Transitions */
.motion-backdrop-enter-active,
.motion-backdrop-leave-active {
  transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.motion-backdrop-enter-from,
.motion-backdrop-leave-to {
  opacity: 0;
}

.motion-card-enter-active {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.motion-card-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.motion-card-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}

.motion-card-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

@keyframes spinSlow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin-slow {
  animation: spinSlow 8s linear infinite;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(249, 115, 22, 0.25);
  border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(249, 115, 22, 0.5);
}
</style>
