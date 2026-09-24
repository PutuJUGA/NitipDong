<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import ScrollExpandMedia from './components/ScrollExpandMedia.vue'
import HeroBentoSection from './components/HeroBentoSection.vue'
import LoginPage from './components/LoginPage.vue'
import RegisterPage from './components/RegisterPage.vue'
import MainMenu from './components/MainMenu.vue'

// --- State Management ---
const currentPage = ref('home') // 'home' | 'login' | 'register' | 'menu'
const isLoading = ref(false)
const authUser = ref(null)
const activeSection = ref('utama')

// Auth Google Simulation
const loginWithGoogle = async () => {
  try {
    isLoading.value = true
    await new Promise((resolve) => setTimeout(resolve, 1000))
    authUser.value = {
      name: 'Budi Pratama',
      email: 'budi.pratama@gmail.com',
      avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi',
    }
    currentPage.value = 'menu'
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
}

// Navigation Helpers
const goToLogin = () => {
  currentPage.value = 'login'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const goToRegister = () => {
  currentPage.value = 'register'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const goToHome = () => {
  currentPage.value = 'home'
  activeSection.value = 'utama'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const goToMenu = () => {
  if (!authUser.value) {
    goToLogin()
    return
  }
  currentPage.value = 'menu'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleAuthSuccess = (userData) => {
  authUser.value = userData
  try {
    localStorage.setItem('nitipdong_profile', JSON.stringify(userData))
  } catch (e) {}
  currentPage.value = 'menu' // Langsung bawa ke Menu Utama setelah berhasil auth!
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleUpdateUser = (userData) => {
  if (!authUser.value) {
    authUser.value = { ...userData }
  } else {
    authUser.value = { ...authUser.value, ...userData }
  }
  try {
    localStorage.setItem('nitipdong_profile', JSON.stringify(authUser.value))
  } catch (e) {}
}

const handleLogout = () => {
  authUser.value = null
  currentPage.value = 'home'
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Smooth Scroll Navigation Helper
const scrollToSection = async (sectionId) => {
  activeSection.value = sectionId
  if (currentPage.value !== 'home') {
    currentPage.value = 'home'
    await nextTick()
  }
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

// Accurate Scrollspy Implementation
const handleScrollSpy = () => {
  if (currentPage.value !== 'home') return

  const scrollPosition = window.scrollY || document.documentElement.scrollTop
  const windowHeight = window.innerHeight
  const scrollHeight = document.documentElement.scrollHeight

  // 1. If user is at or near the bottom of the page
  if (scrollPosition + windowHeight >= scrollHeight - 80) {
    activeSection.value = 'cara-kerja'
    return
  }

  // 2. Detect current section based on viewport position
  const sectionIds = ['fitur', 'cara-kerja']
  const threshold = windowHeight * 0.35 // 35% from top of viewport

  for (const id of sectionIds) {
    const el = document.getElementById(id)
    if (el) {
      const rect = el.getBoundingClientRect()
      if (rect.top <= threshold && rect.bottom > threshold) {
        activeSection.value = id
        return
      }
    }
  }

  // 3. Fallback to 'utama' for the hero / intro card area
  activeSection.value = 'utama'
}

let ticking = false
const onScroll = () => {
  if (!ticking) {
    window.requestAnimationFrame(() => {
      handleScrollSpy()
      ticking = false
    })
    ticking = true
  }
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('resize', onScroll, { passive: true })
  handleScrollSpy()

  try {
    const saved = localStorage.getItem('nitipdong_profile')
    if (saved) {
      const parsed = JSON.parse(saved)
      if (authUser.value) {
        authUser.value = { ...authUser.value, ...parsed }
      }
    }
  } catch (e) {}
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onScroll)
})
</script>

<template>
  <div class="bg-slate-50 text-slate-900 min-h-screen font-sans selection:bg-orange-500 selection:text-white">
    
    <!-- Active Navigation Bar (Hanya tampil di luar Menu Utama) -->
    <nav v-if="currentPage !== 'menu'" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-orange-100 px-4 sm:px-8 py-3 flex items-center justify-between shadow-xs">
      <div 
        @click="goToHome" 
        class="flex items-center gap-3 cursor-pointer group"
      >
        <img 
          src="/LOGO.svg" 
          alt="NitipDong Logo" 
          class="h-10 w-auto group-hover:scale-105 transition-transform object-contain"
        />
        <span class="font-black text-2xl tracking-tight text-slate-900">
          Nitip<span class="text-orange-500">Dong</span>
        </span>
      </div>

      <!-- Navigation Links with Active Indicator -->
      <div class="hidden md:flex items-center gap-1.5 bg-slate-100/80 p-1.5 rounded-full border border-slate-200">
        <button 
          @click="goToHome"
          class="px-4 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer"
          :class="currentPage === 'home' && activeSection === 'utama' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'text-slate-600 hover:text-orange-500'"
        >
          Beranda
        </button>

        <button 
          @click="scrollToSection('fitur')"
          class="px-4 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer"
          :class="currentPage === 'home' && activeSection === 'fitur' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'text-slate-600 hover:text-orange-500'"
        >
          Fitur Sirkel
        </button>

        <button 
          @click="scrollToSection('cara-kerja')"
          class="px-4 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer"
          :class="currentPage === 'home' && activeSection === 'cara-kerja' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'text-slate-600 hover:text-orange-500'"
        >
          Cara Kerja
        </button>

        <!-- Menu Utama (Hanya tampil ketika user sudah login) -->
        <button 
          v-if="authUser"
          @click="goToMenu"
          class="px-4 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer flex items-center gap-1.5"
          :class="currentPage === 'menu' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' : 'text-slate-800 hover:text-orange-600'"
        >
          <span>🍱</span>
          <span>Menu Utama</span>
        </button>
      </div>

      <!-- Auth Action Buttons (Bersebelahan: Masuk & Daftar / User Profile) -->
      <div v-if="!authUser" class="flex items-center gap-2 sm:gap-3">
        <!-- Tombol Masuk -->
        <button 
          @click="goToLogin"
          :class="currentPage === 'login' ? 'border-orange-500 text-orange-500 bg-orange-50 shadow-xs' : 'border-slate-300 hover:border-orange-500 text-slate-700 hover:text-orange-500 hover:bg-orange-50/50'"
          class="px-4 sm:px-5 py-2 rounded-full border font-extrabold text-xs uppercase tracking-wider transition disabled:opacity-50 cursor-pointer"
        >
          Masuk
        </button>

        <!-- Tombol Daftar -->
        <button 
          @click="goToRegister"
          :class="currentPage === 'register' ? 'ring-2 ring-orange-500 ring-offset-2' : ''"
          class="px-5 sm:px-6 py-2 rounded-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-md shadow-orange-500/20 hover:shadow-lg hover:shadow-orange-500/30 transition transform hover:-translate-y-0.5 disabled:opacity-50 cursor-pointer"
        >
          Daftar
        </button>
      </div>
      
      <div v-else class="flex items-center gap-2 sm:gap-3">
        <!-- Tombol Masuk ke Menu Dashboard -->
        <button 
          @click="goToMenu"
          class="flex items-center gap-2 bg-white px-3 sm:px-4 py-1.5 rounded-full border border-orange-200 shadow-xs hover:border-orange-400 transition cursor-pointer"
          :class="currentPage === 'menu' ? 'ring-2 ring-orange-500 bg-orange-50/50' : ''"
          title="Buka Menu Utama"
        >
          <img :src="authUser.avatar" class="w-6 h-6 rounded-full bg-orange-100" />
          <span class="text-xs font-black text-slate-800 hidden sm:inline">{{ authUser.name }}</span>
          <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 uppercase">Dashboard</span>
        </button>

        <!-- Tombol Logout -->
        <button 
          @click="handleLogout" 
          title="Keluar / Logout"
          class="px-3 py-1.5 rounded-full border border-slate-200 hover:border-red-300 text-xs text-slate-500 hover:text-red-500 font-extrabold transition bg-white cursor-pointer"
        >
          Logout
        </button>
      </div>
    </nav>

    <!-- MAIN VIEW ROUTING -->
    <template v-if="currentPage === 'login'">
      <LoginPage 
        @back-home="goToHome"
        @switch-to-register="goToRegister"
        @auth-success="handleAuthSuccess"
      />
    </template>

    <template v-else-if="currentPage === 'register'">
      <RegisterPage 
        @back-home="goToHome"
        @switch-to-login="goToLogin"
        @auth-success="handleAuthSuccess"
      />
    </template>

    <template v-else-if="currentPage === 'menu'">
      <MainMenu 
        :user="authUser || { name: 'Budi Pratama', email: 'budi.pratama@gmail.com', avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=Budi' }"
        @back-home="goToHome"
        @logout="handleLogout"
        @update-user="handleUpdateUser"
      />
    </template>

    <template v-else>
      <!-- SECTION 1: HERO SECTION WITH NEW CUSTOM GEN Z FOOD ILLUSTRATION -->
      <section id="hero" class="pt-12 relative">
        <ScrollExpandMedia
          mediaType="image"
          mediaSrc="/hero-bg.jpg"
          bgImageSrc="/hero-fs.jpg"
          title="NITIP MAKANAN GAMPANG SPLIT BILL TENANG!"
          scrollToExpand="Scroll Down"
        >
          <!-- Content Revealed After Scroll Expansion -->
          <div id="utama" class="max-w-4xl mx-auto space-y-8 text-center bg-white/95 backdrop-blur-xl p-8 sm:p-12 rounded-3xl border-2 border-orange-100 shadow-2xl my-4 scroll-mt-24 sm:scroll-mt-28">
            <span class="px-4 py-1.5 rounded-full bg-orange-500/10 text-orange-600 border border-orange-500/30 text-xs font-extrabold uppercase tracking-widest inline-block">
               GAK ZAMAN LAGI PUSING NIKMATIN NONGKRONG
            </span>

            <h2 class="text-3xl sm:text-5xl font-black text-slate-900 leading-tight">
              "Gak Pusing Lagi Ngurusin Titipan Makanan Temen!"
            </h2>

            <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto font-medium">
              Aplikasi andalan buat catat pesanan sirkelmu, bagi harga otomatis termasuk ongkir & pajak, sampai pantau siapa yang belum bayar. Semua beres dalam satu web.
            </p>

            <div class="pt-4 flex flex-wrap justify-center gap-4">
              <button 
                @click="authUser ? goToMenu() : goToRegister()"
                class="px-8 py-4 rounded-full bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black text-sm shadow-xl shadow-orange-500/30 hover:scale-105 transition transform cursor-pointer flex items-center justify-center gap-2"
              >
                <span>{{ authUser ? 'Buka Menu Utama' : 'Mulai Sekarang — FREE 100%' }}</span>
                <span v-if="authUser">🍱</span>
              </button>
            </div>
          </div>
        </ScrollExpandMedia>
      </section>

      <!-- SECTION 2: BENTO GRID FEATURE SHOWCASE -->
      <section id="fitur" class="px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto py-20 scroll-mt-20 relative z-20">
        <HeroBentoSection />
      </section>

      <!-- SECTION 3: CARA KERJA (HOW IT WORKS) -->
      <section id="cara-kerja" class="py-20 bg-white border-y border-orange-100 px-4 sm:px-6 lg:px-8 scroll-mt-20 relative z-20">
        <div class="max-w-6xl mx-auto space-y-12">
          <div class="text-center space-y-3">
            <span class="px-4 py-1.5 rounded-full bg-orange-500/10 text-orange-600 border border-orange-500/30 text-xs font-extrabold uppercase tracking-widest inline-block">
              HANYA 3 LANGKAH MUDAH
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
              CARA KERJA <span class="text-orange-500">NITIPDONG</span>
            </h2>
            <p class="text-slate-600 text-base max-w-lg mx-auto font-medium">
              Tiga langkah simpel dari titip makanan sampai tagihan lunas tanpa canggung.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-50 border-2 border-orange-100 p-8 rounded-3xl relative space-y-4 hover:border-orange-400 transition-colors">
              <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                1
              </div>
              <h3 class="text-xl font-black text-slate-900">Buat Room & Share Link</h3>
              <p class="text-slate-600 text-sm font-medium leading-relaxed">
                Bikin room titipan makanan dan bagikan tautan unik ke grup WhatsApp sirkelmu.
              </p>
            </div>

            <div class="bg-slate-50 border-2 border-orange-100 p-8 rounded-3xl relative space-y-4 hover:border-orange-400 transition-colors">
              <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                2
              </div>
              <h3 class="text-xl font-black text-slate-900">Teman Isi Pesanan Sendiri</h3>
              <p class="text-slate-600 text-sm font-medium leading-relaxed">
                Temanmu tinggal buka link dan memilih menu makanannya masing-masing secara langsung.
              </p>
            </div>

            <div class="bg-slate-50 border-2 border-orange-100 p-8 rounded-3xl relative space-y-4 hover:border-orange-400 transition-colors">
              <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                3
              </div>
              <h3 class="text-xl font-black text-slate-900">Auto Split & Pantau Lunas</h3>
              <p class="text-slate-600 text-sm font-medium leading-relaxed">
                Sistem menghitung total ongkir + pajak, dan memperbarui status Lunas/Ngutang secara real-time.
              </p>
            </div>
          </div>
        </div>
      </section>
    </template>

    <!-- FOOTER (Hanya tampil di Beranda) -->
    <footer v-if="currentPage === 'home'" class="border-t border-orange-100 bg-white py-12 px-4 sm:px-8 text-center sm:text-left relative z-20">
      <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
        <div @click="goToHome" class="flex items-center gap-2 cursor-pointer group">
          <img 
            src="/LOGO.svg" 
            alt="NitipDong Logo" 
            class="h-10 w-auto group-hover:scale-105 transition-transform object-contain"
          />
          <span class="font-black text-xl text-slate-900">Nitip<span class="text-orange-500">Dong</span></span>
        </div>
        <p class="text-xs font-medium text-slate-500">© 2026 NitipDong. Nitip makanan gampang, split bill tenang! Built By Putu, Fathan, & Radiz (GOOGLE CARRY WE US).</p>
      </div>
    </footer>
  </div>
</template>
