<script setup>
import { ref } from 'vue'
import { Eye, EyeClosed } from 'lucide-vue-next'
import CardTiltEffect from './CardTiltEffect.vue'

const emit = defineEmits(['back-home', 'switch-to-login', 'auth-success'])

const fullName = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const confirmPassword = ref('')
const agreeTerms = ref(true)
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const handleRegister = async () => {
  if (!fullName.value || !email.value || !password.value) {
    errorMessage.value = 'Harap isi semua kolom wajib!'
    return
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Konfirmasi password tidak cocok!'
    return
  }

  if (!agreeTerms.value) {
    errorMessage.value = 'Harap setujui syarat dan ketentuan!'
    return
  }

  errorMessage.value = ''
  isLoading.value = true

  // Simulasi pendaftaran akun baru
  setTimeout(() => {
    isLoading.value = false
    emit('auth-success', {
      name: fullName.value,
      email: email.value,
      phone: phone.value,
      avatar: `https://api.dicebear.com/7.x/adventurer/svg?seed=${encodeURIComponent(fullName.value)}`,
    })
  }, 1000)
}

const handleGoogleRegister = async () => {
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
    emit('auth-success', {
      name: 'Budi Pratama',
      email: 'budi.pratama@gmail.com',
      avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi',
    })
  }, 900)
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 pt-24 pb-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <CardTiltEffect>
      <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 bg-white rounded-3xl border-2 border-orange-100 shadow-2xl overflow-hidden">
      
      <!-- SISI KIRI: FORM DAFTAR (7 Kolom) -->
      <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-between">
        <div>
          <!-- Tombol Kembali ke Beranda -->
          <button 
            @click="emit('back-home')"
            class="inline-flex items-center gap-2 text-xs font-black text-slate-500 hover:text-orange-500 mb-6 transition-colors group cursor-pointer"
          >
            <span class="text-base group-hover:-translate-x-1 transition-transform">←</span>
            <span>Kembali ke Beranda</span>
          </button>

          <!-- Header Form -->
          <div class="space-y-2 mb-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100/70 text-orange-600 text-xs font-black uppercase tracking-wider">
              <span></span>
              <span>Pendaftaran Akun Baru</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
              Bikin Akun NitipDong! ✨
            </h1>
            <p class="text-slate-500 text-sm font-medium">
              Gratis 100%! Mulai catat pesanan & bagi tagihan nongkrong tanpa drama.
            </p>
          </div>

          <!-- Tombol Daftar via Google -->
          <button
            type="button"
            @click="handleGoogleRegister"
            :disabled="isLoading"
            class="w-full py-3 px-4 rounded-2xl border-2 border-slate-200 hover:border-orange-400 bg-white hover:bg-orange-50/30 text-slate-700 font-bold text-sm flex items-center justify-center gap-3 transition-all shadow-xs cursor-pointer disabled:opacity-50"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Daftar Cepat dengan Google</span>
          </button>

          <!-- Divider -->
          <div class="relative my-6 flex items-center justify-center">
            <div class="border-t border-slate-200 w-full" />
            <span class="bg-white px-3 text-xs font-bold text-slate-400 uppercase tracking-wider absolute">
              atau isi data di bawah
            </span>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-bold flex items-center gap-2">
            <span>⚠️</span>
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Input Fields Form -->
          <form @submit.prevent="handleRegister" class="space-y-3.5">
            <div>
              <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">
                Nama Lengkap <span class="text-orange-500">*</span>
              </label>
              <input 
                v-model="fullName"
                type="text" 
                placeholder="Contoh: Fathan Pratama"
                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition"
                required
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">
                  Email <span class="text-orange-500">*</span>
                </label>
                <input 
                  v-model="email"
                  type="email" 
                  placeholder="nama@email.com"
                  class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition"
                  required
                />
              </div>

              <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">
                  No. WhatsApp <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input 
                  v-model="phone"
                  type="tel" 
                  placeholder="08123456789"
                  class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">
                  Password <span class="text-orange-500">*</span>
                </label>
                <div class="relative">
                  <input 
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'" 
                    placeholder="Min. 6 karakter"
                    class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition pr-8"
                    required
                  />
                  <button 
                   type="button"
                   @click="showPassword = !showPassword"
                   class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors cursor-pointer flex items-center justify-center"
                  >
                    <Eye v-if="showPassword" class="w-4 h-4" />
                    <EyeClosed v-else class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">
                  Konfirmasi Password <span class="text-orange-500">*</span>
                </label>
                <input 
                  v-model="confirmPassword"
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="Ulangi password"
                  class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition"
                  required
                />
              </div>
            </div>

            <!-- Syarat dan Ketentuan -->
            <div class="flex items-start gap-2 pt-1">
              <input 
                id="agreeTerms" 
                v-model="agreeTerms" 
                type="checkbox" 
                class="w-4 h-4 mt-0.5 rounded text-orange-500 focus:ring-orange-400 border-slate-300"
              />
              <label for="agreeTerms" class="text-xs text-slate-600 cursor-pointer">
                Saya menyetujui <a href="#" @click.prevent class="text-orange-500 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" @click.prevent class="text-orange-500 font-bold hover:underline">Kebijakan Privasi</a> NitipDong.
              </label>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 transition transform disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
            >
              <span>{{ isLoading ? 'Membuat Akun...' : 'Daftar Sekarang' }}</span>
              <span v-if="!isLoading"></span>
            </button>
          </form>
        </div>

        <!-- Footer Switch to Login -->
        <div class="pt-6 text-center text-xs font-medium text-slate-600 border-t border-slate-100 mt-4">
          Sudah punya akun NitipDong? 
          <button 
            type="button"
            @click="emit('switch-to-login')" 
            class="text-orange-500 font-black hover:underline ml-1 cursor-pointer"
          >
            Masuk di Sini
          </button>
        </div>
      </div>

      <!-- SISI KANAN: FITUR BENEFIT (5 Kolom) -->
      <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-amber-500 via-orange-500 to-orange-600 p-10 text-white flex-col justify-between relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full blur-2xl" />
        <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-amber-300/20 rounded-full blur-2xl" />

        <div class="relative z-10 space-y-6">
          <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/30 text-xs font-black uppercase tracking-wider">
            100% Gratis Selamanya
          </div>

          <h2 class="text-3xl font-black leading-tight">
            Kenapa Sirkel Nongkrong Pilih NitipDong?
          </h2>

          <div class="space-y-4 pt-2">
            <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15">
              <span class="text-xl">🔗</span>
              <div>
                <p class="font-extrabold text-sm text-white">Catat Bareng Tanpa Ribet</p>
                <p class="text-orange-100 text-xs">Share satu link ke grup WA, semua teman isi pesanannya sendiri.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15">
              <span class="text-xl">🧮</span>
              <div>
                <p class="font-extrabold text-sm text-white">Bagi Ongkir & Diskon Adil</p>
                <p class="text-orange-100 text-xs">Otomatis hitung proporsional sampai ke rupiah terakhir tanpa rugi.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15">
              <span class="text-xl">🔔</span>
              <div>
                <p class="font-extrabold text-sm text-white">Pantau Status Bayar</p>
                <p class="text-orange-100 text-xs">Tahu siapa yang sudah lunas dan siapa yang masih ngutang dengan jelas.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="relative z-10 text-xs text-orange-100 font-medium">
          Dibuat oleh 
          <img 
          src="/LOGO.svg" 
          alt="NitipDong Logo" 
          class="h-10 w-auto group-hover:scale-90 transition-transform object-contain"
        />untuk sirkel nongkrong se-Indonesia
        </div>
      </div>

    </div>
    </CardTiltEffect>
  </div>
</template>
