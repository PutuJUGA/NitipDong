<script setup>
import { ref } from 'vue'
import { Eye, EyeClosed } from 'lucide-vue-next'
import CardTiltEffect from './CardTiltEffect.vue'

const emit = defineEmits(['back-home', 'switch-to-register', 'auth-success'])

const emailOrUsername = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  if (!emailOrUsername.value || !password.value) {
    errorMessage.value = 'Silakan isi email/username dan password!'
    return
  }

  errorMessage.value = ''
  isLoading.value = true

  // Simulasi API login
  setTimeout(() => {
    isLoading.value = false
    const userName = emailOrUsername.value.includes('@') 
      ? emailOrUsername.value.split('@')[0] 
      : emailOrUsername.value
    
    emit('auth-success', {
      name: userName.charAt(0).toUpperCase() + userName.slice(1),
      email: emailOrUsername.value.includes('@') ? emailOrUsername.value : `${emailOrUsername.value}@gmail.com`,
      avatar: `https://api.dicebear.com/7.x/adventurer/svg?seed=${userName}`,
    })
  }, 1000)
}

const handleGoogleLogin = async () => {
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
      
      <!-- SISI KIRI: FORM LOGIN (7 Kolom) -->
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
          <div class="space-y-2 mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100/70 text-orange-600 text-xs font-black uppercase tracking-wider">
              <span></span>
              <span>Halaman Masuk</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
              Selamat Datang Kembali! 👋
            </h1>
            <p class="text-slate-500 text-sm font-medium">
              Masuk ke akun NitipDong buat lanjut catat pesanan & split bill bareng teman sirkelmu.
            </p>
          </div>

          <!-- Tombol Masuk via Google -->
          <button
            type="button"
            @click="handleGoogleLogin"
            :disabled="isLoading"
            class="w-full py-3 px-4 rounded-2xl border-2 border-slate-200 hover:border-orange-400 bg-white hover:bg-orange-50/30 text-slate-700 font-bold text-sm flex items-center justify-center gap-3 transition-all shadow-xs cursor-pointer disabled:opacity-50"
          >
            <!-- Google SVG Icon -->
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Masuk Cepat dengan Google</span>
          </button>

          <!-- Divider -->
          <div class="relative my-6 flex items-center justify-center">
            <div class="border-t border-slate-200 w-full" />
            <span class="bg-white px-3 text-xs font-bold text-slate-400 uppercase tracking-wider absolute">
              atau dengan email
            </span>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-bold flex items-center gap-2">
            <span>⚠️</span>
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Input Fields Form -->
          <form @submit.prevent="handleLogin" class="space-y-4">
            <div>
              <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                Email atau Username
              </label>
              <input 
                v-model="emailOrUsername"
                type="text" 
                placeholder="nama@email.com atau username"
                class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition"
                required
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                  Password
                </label>
                <a href="#" @click.prevent class="text-xs font-bold text-orange-500 hover:text-orange-600">
                  Lupa password?
                </a>
              </div>
              <div class="relative">
                <input 
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="Masukkan password"
                  class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:outline-none text-sm text-slate-800 font-medium transition pr-10"
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

            <!-- Remember Me -->
            <div class="flex items-center gap-2 pt-1">
              <input 
                id="rememberMe" 
                v-model="rememberMe" 
                type="checkbox" 
                class="w-4 h-4 rounded text-orange-500 focus:ring-orange-400 border-slate-300"
              />
              <label for="rememberMe" class="text-xs font-medium text-slate-600 cursor-pointer">
                Ingat saya di perangkat ini
              </label>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 transition transform disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
            >
              <span>{{ isLoading ? 'Memverifikasi...' : 'Masuk' }}</span>
              <span v-if="!isLoading"></span>
            </button>
          </form>
        </div>

        <!-- Footer Switch to Register -->
        <div class="pt-8 text-center text-xs font-medium text-slate-600 border-t border-slate-100 mt-6">
          Belum punya akun NitipDong? 
          <button 
            type="button"
            @click="emit('switch-to-register')" 
            class="text-orange-500 font-black hover:underline ml-1 cursor-pointer"
          >
            Daftar Sekarang Gratis
          </button>
        </div>
      </div>

      <!-- SISI KANAN: PREVIEW BRANDING & HIGHLIGHT (5 Kolom) -->
      <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-orange-500 via-amber-500 to-orange-600 p-10 text-white flex-col justify-between relative overflow-hidden">
        <!-- Background Glow Accent -->
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full blur-2xl" />
        <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-amber-400/20 rounded-full blur-2xl" />

        <div class="relative z-10 space-y-6">
          <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/30 text-xs font-black uppercase tracking-wider">
            ✨ Sirkel Anti Ribet
          </div>

          <h2 class="text-3xl font-black leading-tight">
            Nongkrong Lebih Asik Tanpa Drama Bon Makanan
          </h2>

          <p class="text-orange-100 text-sm font-medium leading-relaxed">
            Satu link pesanan untuk semua orang di grupmu. Bebas salah pesan, hitung otomatis, bayar gampang.
          </p>
        </div>

        <!-- Mockup Card Testimonial / Activity -->
        <div class="relative z-10 space-y-3 bg-white/15 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-lg text-xs">
          <div class="flex items-center justify-between pb-2 border-b border-white/10">
            <span class="font-extrabold text-white">Pesanan Terakhir:</span>
            <span class="bg-emerald-500/80 text-white px-2 py-0.5 rounded-full font-black text-[10px]">LUNAS</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white text-orange-600 flex items-center justify-center font-black">
              🍗
            </div>
            <div>
              <p class="font-bold text-white">Ayam Geprek + Es Teh</p>
              <p class="text-orange-100 text-[11px]">Rp 28.000 • Split 4 orang</p>
            </div>
          </div>
        </div>

        <div class="relative z-10 text-xs text-orange-100 font-medium">
          © 2026 NitipDong • Solusi Cerdas Titip Makanan
        </div>
      </div>

    </div>
    </CardTiltEffect>
  </div>
</template>
