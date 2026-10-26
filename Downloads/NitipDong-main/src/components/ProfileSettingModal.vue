<script setup>
import { ref, watch, computed } from 'vue'
import {
  X,
  User,
  Phone,
  Mail,
  CreditCard,
  Sparkles,
  Check,
  Smile,
  Palette
} from 'lucide-vue-next'
import SocialCards from '@/components/ui/card-fan-carousel.vue'
import { AVATAR_THEMES, getThemeByAvatarUrl } from '@/utils/theme'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'save'])

// Form state lokal
const form = ref({
  name: '',
  username: '',
  email: '',
  phone: '',
  bio: '',
  avatar: '',
  themeId: 'budi',
  themeColor: '#f97316',
  themeName: 'Oranye Nitip (Sunset)',
  defaultBank: 'BCA / QRIS',
  defaultAccountNumber: '',
  defaultAccountName: ''
})

const isAvatarPickerOpen = ref(false)

const currentCardTheme = computed(() => {
  return getThemeByAvatarUrl(form.value.avatar)
})

// Sinkronisasi data saat modal dibuka atau props berubah
watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    const currentTheme = getThemeByAvatarUrl(props.user?.avatar || AVATAR_THEMES[0].url)
    form.value = {
      name: props.user?.name || 'Budi Pratama',
      username: props.user?.username || 'budipratama',
      email: props.user?.email || 'budi.pratama@gmail.com',
      phone: props.user?.phone || '081234567890',
      bio: props.user?.bio || 'Pecinta kuliner pedas & anti ribet hitung bon! 🌶️',
      avatar: props.user?.avatar || AVATAR_THEMES[0].url,
      themeId: props.user?.themeId || currentTheme.id,
      themeColor: props.user?.themeColor || currentTheme.primary,
      themeName: props.user?.themeName || currentTheme.themeName,
      defaultBank: props.user?.defaultBank || 'BCA / QRIS',
      defaultAccountNumber: props.user?.defaultAccountNumber || '8271928391',
      defaultAccountName: props.user?.defaultAccountName || props.user?.name || 'Budi Pratama'
    }
  }
}, { immediate: true })

const handleAvatarSelect = (card) => {
  form.value.avatar = card.url || card.imgUrl
  form.value.themeId = card.id
  form.value.themeColor = card.primary
  form.value.themeName = card.themeName
}

const handleSave = () => {
  if (!form.value.name.trim()) {
    alert('Nama tidak boleh kosong!')
    return
  }
  emit('save', { ...form.value })
  emit('close')
}
</script>

<template>
  <div 
    v-if="isOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/60 backdrop-blur-xs overflow-y-auto"
    @click.self="emit('close')"
  >
    <div 
      class="bg-white dark:bg-zinc-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border-2 my-auto flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-200 text-slate-900 dark:text-zinc-100"
      :style="{ borderColor: form.themeColor ? `${form.themeColor}40` : undefined }"
    >
      <!-- Header Modal -->
      <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-zinc-800 shrink-0">
        <div class="flex items-center gap-3">
          <div 
            class="w-10 h-10 rounded-2xl flex items-center justify-center font-black transition-colors"
            :style="{ 
              backgroundColor: currentCardTheme.lightBg || 'rgba(249, 115, 22, 0.15)',
              color: form.themeColor || '#f97316'
            }"
          >
            <User class="w-5 h-5" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
              Pengaturan Profil Akun
            </h2>
            <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
              Atur avatar foto profil, kontak WhatsApp, dan rekening pembayaranmu
            </p>
          </div>
        </div>

        <button 
          @click="emit('close')"
          class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
          title="Tutup"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Konten Scrollable -->
      <div class="overflow-y-auto pr-1 py-4 space-y-6 flex-1 text-xs">
        
        <!-- ==================== BAGIAN 1: AVATAR & TEMA AKTIF (PENCET AVATAR UNTUK MENGGANTI) ==================== -->
        <div 
          class="p-4 sm:p-5 rounded-3xl border transition-all duration-300 flex flex-col sm:flex-row items-center justify-between gap-4"
          :style="{
            backgroundColor: currentCardTheme.lightBg || 'rgba(249, 115, 22, 0.08)',
            borderColor: form.themeColor || '#f97316'
          }"
        >
          <div class="flex items-center gap-4 w-full sm:w-auto">
            <!-- Pencet Avatar untuk Mengganti -->
            <div 
              @click="isAvatarPickerOpen = true"
              class="relative group cursor-pointer shrink-0"
              title="Pencet avatar ini untuk memilih karakter & kustomisasi warna tema"
            >
              <img 
                :src="form.avatar" 
                :alt="form.name"
                class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 shadow-md group-hover:scale-105 group-hover:brightness-90 transition-all duration-200"
                :style="{ borderColor: form.themeColor || '#f97316' }"
              />
              <div 
                class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white transition-opacity duration-200"
              >
                <Palette class="w-5 h-5 mb-0.5" />
                <span class="text-[9px] font-black uppercase">Ganti</span>
              </div>
              <div 
                class="absolute -bottom-1 -right-1 p-1 rounded-full text-white shadow-xs"
                :style="{ backgroundColor: form.themeColor || '#f97316' }"
              >
                <Sparkles class="w-3.5 h-3.5" />
              </div>
            </div>

            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <span 
                  class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full text-white"
                  :style="{ backgroundColor: form.themeColor || '#f97316' }"
                >
                  {{ currentCardTheme.label || 'Avatar Terpilih' }}
                </span>
                <span class="text-[11px] font-bold text-slate-500 dark:text-zinc-400">
                  {{ currentCardTheme.role || 'Karakter Sirkel' }}
                </span>
              </div>
              <h3 class="text-base font-black text-slate-900 dark:text-white mt-1 truncate">
                {{ form.name || 'Putu Ganteng' }}
              </h3>
              <p class="text-[11px] font-medium text-slate-600 dark:text-zinc-300 flex items-center gap-1.5 mt-0.5">
                <Palette class="w-3.5 h-3.5" :style="{ color: form.themeColor || '#f97316' }" />
                <span>Tema: <strong :style="{ color: form.themeColor || '#f97316' }">{{ form.themeName }}</strong></span>
              </p>
              <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-semibold block mt-0.5">
                💡 Pencet foto avatar untuk mengganti tema & karakter
              </span>
            </div>
          </div>

          <!-- Tombol Aksi Buka Pilihan Avatar -->
          <button 
            type="button"
            @click="isAvatarPickerOpen = true"
            class="w-full sm:w-auto px-4 py-2.5 rounded-2xl text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-105 active:scale-95 transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
            :style="{
              backgroundColor: form.themeColor || '#f97316',
              boxShadow: `0 8px 16px -2px ${form.themeColor || '#f97316'}55`
            }"
          >
            <Palette class="w-4 h-4" />
            <span>Pilih Avatar & Warna</span>
          </button>
        </div>

        <!-- ==================== BAGIAN 2: DATA PROFIL & KONTAK ==================== -->
        <div class="space-y-4">
          <div class="flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800 pb-2">
            <User class="w-4 h-4 text-orange-500" />
            <span class="font-black text-slate-900 dark:text-white uppercase tracking-wider text-xs">
              Informasi Pribadi & Kontak
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Nama Lengkap <span class="text-orange-500">*</span>
              </label>
              <input 
                v-model="form.name"
                type="text" 
                placeholder="Contoh: Budi Pratama"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
                required
              />
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Username / Panggilan Sirkel
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">@</span>
                <input 
                  v-model="form.username"
                  type="text" 
                  placeholder="budipratama"
                  class="w-full pl-8 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Nomor WhatsApp <span class="text-orange-500">*</span>
              </label>
              <div class="relative">
                <Phone class="w-4 h-4 text-emerald-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
                <input 
                  v-model="form.phone"
                  type="tel" 
                  placeholder="081234567890"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
                  required
                />
              </div>
              <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-1 font-medium">
                Penting: digunakan teman sirkel untuk kirim rincian pesanan & bukti transfer.
              </p>
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Email Akun
              </label>
              <div class="relative">
                <Mail class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                <input 
                  v-model="form.email"
                  type="email" 
                  placeholder="budi.pratama@gmail.com"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
              Catatan Sirkel / Makanan Favorit (Bio)
            </label>
            <input 
              v-model="form.bio"
              type="text" 
              placeholder="Contoh: Pecinta kuliner pedas & anti ribet hitung bon! 🌶️"
              class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
            />
          </div>
        </div>

        <!-- ==================== BAGIAN 3: INFO REKENING DEFAULT ==================== -->
        <div class="space-y-4 pt-2">
          <div class="flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800 pb-2">
            <CreditCard class="w-4 h-4 text-orange-500" />
            <span class="font-black text-slate-900 dark:text-white uppercase tracking-wider text-xs">
              Info Pembayaran Default (Saat Kamu Jadi Host Titipan)
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Bank / E-Wallet
              </label>
              <select 
                v-model="form.defaultBank"
                class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition cursor-pointer text-slate-900 dark:text-white"
              >
                <option value="BCA">BCA</option>
                <option value="BCA / QRIS">BCA / QRIS</option>
                <option value="Mandiri">Mandiri</option>
                <option value="BRI">BRI</option>
                <option value="BNI">BNI</option>
                <option value="BSI">BSI</option>
                <option value="GoPay">GoPay</option>
                <option value="OVO">OVO</option>
                <option value="Dana">Dana</option>
                <option value="ShopeePay">ShopeePay</option>
                <option value="QRIS">QRIS Semua Bank</option>
              </select>
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                No. Rekening / No. HP
              </label>
              <input 
                v-model="form.defaultAccountNumber"
                type="text" 
                placeholder="Misal: 8271928391"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <label class="block font-black uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1">
                Atas Nama (a/n)
              </label>
              <input 
                v-model="form.defaultAccountName"
                type="text" 
                placeholder="Budi Pratama"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 focus:border-orange-500 focus:bg-white dark:focus:bg-zinc-700 focus:outline-none text-sm font-medium transition text-slate-900 dark:text-white"
              />
            </div>
          </div>
          <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-medium">
            Info ini akan otomatis terisi saat kamu membuat room titipan baru agar temanmu gampang transfer.
          </p>
        </div>

      </div>

      <!-- Footer Tombol Simpan -->
      <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-end gap-3 shrink-0">
        <button 
          type="button"
          @click="emit('close')"
          class="px-5 py-2.5 rounded-2xl border border-slate-300 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 font-extrabold text-xs uppercase tracking-wider transition cursor-pointer"
        >
          Batal
        </button>

        <button 
          type="button"
          @click="handleSave"
          class="px-6 py-2.5 rounded-2xl text-white font-black text-xs uppercase tracking-wider shadow-lg hover:-translate-y-0.5 transition transform cursor-pointer flex items-center gap-1.5"
          :style="{
            backgroundColor: form.themeColor || '#f97316',
            boxShadow: `0 10px 20px -4px ${form.themeColor || '#f97316'}66`
          }"
        >
          <Check class="w-4 h-4" />
          <span>Simpan Perubahan</span>
        </button>
      </div>

    </div>

    <!-- ==================== SUB-MODAL: PILIH AVATAR & TEMA WARNA ==================== -->
    <div 
      v-if="isAvatarPickerOpen" 
      class="fixed inset-0 z-60 flex items-center justify-center p-3 sm:p-4 bg-slate-950/75 backdrop-blur-sm overflow-y-auto animate-in fade-in duration-200"
      @click.self="isAvatarPickerOpen = false"
    >
      <div 
        class="bg-white dark:bg-zinc-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border-2 my-auto flex flex-col max-h-[94vh] animate-in zoom-in-95 duration-200 text-slate-900 dark:text-zinc-100"
        :style="{ borderColor: form.themeColor ? `${form.themeColor}55` : '#f97316' }"
      >
        <!-- Header Sub-modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-zinc-800 shrink-0">
          <div class="flex items-center gap-3">
            <div 
              class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white shadow-md transition-colors"
              :style="{ backgroundColor: form.themeColor || '#f97316' }"
            >
              <Palette class="w-5 h-5" />
            </div>
            <div>
              <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                Pilih Avatar & Tema Warna Sirkel
              </h2>
              <p class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                Pilih karakter sirkelmu. Warna Menu Utama akan otomatis mengikuti kartu avatar yang dipilih
              </p>
            </div>
          </div>

          <button 
            @click="isAvatarPickerOpen = false"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
            title="Tutup Pilihan Avatar"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Konten Avatar Picker dengan Card Fan Carousel -->
        <div class="overflow-y-auto pr-1 py-4 flex-1">
          <SocialCards 
            :cards="AVATAR_THEMES" 
            v-model="form.avatar" 
            :user-name="form.name" 
            @select="handleAvatarSelect"
          />
        </div>

        <!-- Footer Sub-modal -->
        <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-between gap-3 shrink-0">
          <div class="text-xs font-bold text-slate-500 dark:text-zinc-400 flex items-center gap-2">
            <span 
              class="w-3.5 h-3.5 rounded-full inline-block shadow-xs"
              :style="{ backgroundColor: form.themeColor || '#f97316' }"
            />
            <span>Tema: <strong :style="{ color: form.themeColor || '#f97316' }">{{ form.themeName }}</strong></span>
          </div>

          <button 
            type="button"
            @click="isAvatarPickerOpen = false"
            class="px-6 py-2.5 rounded-2xl text-white font-black text-xs uppercase tracking-wider shadow-lg hover:-translate-y-0.5 transition transform cursor-pointer flex items-center gap-1.5"
            :style="{
              backgroundColor: form.themeColor || '#f97316',
              boxShadow: `0 10px 20px -4px ${form.themeColor || '#f97316'}66`
            }"
          >
            <Check class="w-4 h-4" />
            <span>Terapkan Avatar & Tema Ini</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
