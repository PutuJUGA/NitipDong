<script setup>
import { ref } from 'vue'

const copiedLink = ref(false)

const copyLinkDemo = () => {
  copiedLink.value = true
  setTimeout(() => {
    copiedLink.value = false
  }, 2000)
}

// Mock Order List for Split-Bill Demo
const mockOrders = ref([
  { name: 'Rizky', item: 'Ayam Geprek Sambal Ijo + Es Teh', price: 28000, status: 'LUNAS' },
  { name: 'Siti', item: 'Ramen Tonkotsu Spicy', price: 42000, status: 'LUNAS' },
  { name: 'Budi', item: 'Pizza Personal Beef Pepperoni', price: 35000, status: 'NGUTANG' },
  { name: 'Dion', item: 'Boba Brown Sugar Milk', price: 22000, status: 'NGUTANG' }
])
</script>

<template>
  <div class="space-y-12 my-8">
    <!-- Header Section -->
    <div class="text-center space-y-3">
      <span class="px-4 py-1.5 rounded-full bg-orange-500/10 text-orange-600 border border-orange-500/30 text-xs font-black uppercase tracking-widest inline-block">
        FITUR ANDALAN SIRKEL
      </span>
      <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
        KENAPA HARUS <span class="text-orange-500">NITIPDONG?</span>
      </h2>
      <p class="text-slate-600 text-base max-w-lg mx-auto font-medium">
        Solusi cerdas biar acara makan-makan bareng teman tetap seru tanpa drama salah hitung atau lupa bayar.
      </p>
    </div>

    <!-- Bento Grid Showcase -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
      
      <!-- FITUR 1: Catat Bareng (Share Link) -->
      <div class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 transition-all group flex flex-col justify-between">
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-100 rounded-full blur-2xl group-hover:bg-orange-100 transition-colors" />

        <div class="relative z-10 space-y-4">
          <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-500/30">
            🔗
          </div>
          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Catat Bareng (Kolaborasi)</h3>
            <p class="text-slate-600 text-sm leading-relaxed font-medium">
              Tinggal share link ke grup WA, biarkan teman-teman sirkelmu isi pesanan makanannya sendiri. Gak perlu catat manual satu-satu!
            </p>
          </div>
        </div>

        <!-- Share Link Interactive Demo Box -->
        <div class="relative z-10 pt-6 mt-6 border-t border-slate-100">
          <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between gap-2">
            <span class="text-xs font-mono text-slate-600 truncate">nitipdong.com/join/makan-siang-sirkel</span>
            <button 
              @click="copyLinkDemo"
              class="px-3 py-1.5 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition shrink-0"
            >
              {{ copiedLink ? '✓ Tersalin!' : 'Copy Link' }}
            </button>
          </div>
        </div>
      </div>

      <!-- FITUR 2: Auto Split-Bill & Tax Calculator -->
      <div class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 transition-all group flex flex-col justify-between">
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-orange-100 rounded-full blur-2xl" />

        <div class="relative z-10 space-y-4">
          <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30">
            🧮
          </div>
          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Auto Split-Bill Cerdas</h3>
            <p class="text-slate-600 text-sm leading-relaxed font-medium">
              Otomatis bagi total harga pesanan, langsung dihitung secara proporsional dengan pajak resto + ongkir secara presisi.
            </p>
          </div>
        </div>

        <!-- Split-Bill Math Breakdown Demo -->
        <div class="relative z-10 pt-6 mt-6 border-t border-slate-100 space-y-2 text-xs">
          <div class="flex justify-between text-slate-500">
            <span>Total Makanan</span>
            <span class="font-semibold text-slate-800">Rp 127.000</span>
          </div>
          <div class="flex justify-between text-slate-500">
            <span>Ongkir + PB1 (10%)</span>
            <span class="font-semibold text-slate-800">Rp 18.500</span>
          </div>
          <div class="flex justify-between text-orange-600 font-bold text-sm pt-1 border-t border-dashed border-slate-200">
            <span>Total Otomatis Diterbagi</span>
            <span>Pas 100% </span>
          </div>
        </div>
      </div>

      <!-- FITUR 3: Radar Tagihan (Lunas vs Ngutang) -->
      <div class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 transition-all group flex flex-col justify-between">
        <div class="relative z-10 space-y-4">
          <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30">
            📡
          </div>
          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Radar Tagihan Real-Time</h3>
            <p class="text-slate-600 text-sm leading-relaxed font-medium">
              Pantau status tagihan real-time. Ketahuan jelas siapa yang sudah lunas dan siapa yang masih ngutang.
            </p>
          </div>
        </div>

        <!-- Live Status List Demo -->
        <div class="relative z-10 pt-4 mt-4 border-t border-slate-100 space-y-2">
          <div 
            v-for="(order, idx) in mockOrders" 
            :key="idx"
            class="flex items-center justify-between p-2 rounded-xl bg-slate-50 text-xs"
          >
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-800">{{ order.name }}</span>
              <span class="text-slate-400 text-[10px]">Rp {{ order.price.toLocaleString() }}</span>
            </div>
            <span 
              class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
              :class="order.status === 'LUNAS' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600 animate-pulse'"
            >
              {{ order.status }}
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
