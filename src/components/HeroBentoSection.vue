<script setup>
import { ref } from 'vue'
import CardDetailModal from './CardDetailModal.vue'
import { Sparkles, ArrowRight, ExternalLink } from 'lucide-vue-next'

const copiedLink = ref(false)
const selectedModalCard = ref(null)
const isModalOpen = ref(false)

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

// Card Data for Modal Details
const bentoCardsData = [
  {
    id: 'fitur-1',
    type: 'feature',
    badge: 'FITUR SIRKEL #1',
    icon: '🔗',
    colorClass: 'bg-orange-500 text-white shadow-orange-500/30',
    title: 'Catat Bareng (Kolaborasi Real-Time)',
    subtitle: 'Tinggal share link ke grup WA, biarkan teman-teman sirkelmu isi pesanan makanannya sendiri.',
    description: 'Gak perlu pusing mencatat manual satu per satu di kertas atau obrolan grup. Cukup buat room titip makanan dalam 5 detik, sebar link, dan perhatikan daftar pesanan terisi otomatis secara real-time.',
    interactiveType: 'link-copy',
    highlights: [
      { title: 'Bebas Login Untuk Teman', desc: 'Anggota sirkel yang dititipkan tidak perlu registrasi akun. Cukup klik link dan pilih menu.' },
      { title: '1-Click WhatsApp Share', desc: 'Tersedia template pesan WA otomatis berisi link room yang rapi dan siap kirim ke grup.' },
      { title: 'Sinkronisasi Real-Time', desc: 'Setiap tambahan makanan atau revisi jumlah langsung ter-update di layar Host tanpa refresh.' },
      { title: 'Catatan Khusus Sesuai Selera', desc: 'Teman bisa menambahkan catatan level pedas, es dikit, bumbu terpisah, dll.' }
    ],
    tips: [
      'Sematkan link room di deskripsi grup WhatsApp sirkel jika rutinitas pesan makan siang harian.',
      'Atur batas waktu penutupan room agar pesanan bisa segera diproses ke driver ojol.'
    ]
  },
  {
    id: 'fitur-2',
    type: 'feature',
    badge: 'FITUR SIRKEL #2',
    icon: '🧮',
    colorClass: 'bg-amber-500 text-white shadow-amber-500/30',
    title: 'Auto Split-Bill & Tax Calculator',
    subtitle: 'Otomatis bagi total harga pesanan, langsung dihitung secara proporsional dengan pajak resto + ongkir.',
    description: 'Sistem perhitungan cerdas NitipDong mengeliminasi salah hitung pecahan rupiah. Pajak restoran 10% dan biaya ongkir ojol dapat disesuaikan proporsinya per makanan.',
    interactiveType: 'split-calculator',
    highlights: [
      { title: 'Perhitungan Presisi 100%', desc: 'Bagi ongkir & voucher diskon secara rasional sesuai nominal pesanan masing-masing.' },
      { title: 'Pajak Resto (PB1) Otomatis', desc: 'Menghitung persen pajak langsung dari subtotal per orang tanpa pembulatan liar.' },
      { title: 'Bebas Nombok/Rugi Bagi Host', desc: 'Koordinator yang membelikan makanan tidak pernah rugi akibat selisih seribu-dua ribu rupiah.' },
      { title: 'Rincian Nota Transparan', desc: 'Tiap anggota sirkel bisa melihat perincian harga item + pajak + ongkir mereka secara transparan.' }
    ],
    tips: [
      'Kamu bisa memilih apakah ongkir mau dibagi rata persis atau dibagi proposional sesuai porsi harga makanan.',
      'Gunakan fitur diskon voucher promo untuk memotong total tagihan bersama.'
    ]
  },
  {
    id: 'fitur-3',
    type: 'feature',
    badge: 'FITUR SIRKEL #3',
    icon: '📡',
    colorClass: 'bg-emerald-500 text-white shadow-emerald-500/30',
    title: 'Radar Tagihan Real-Time',
    subtitle: 'Pantau status tagihan real-time. Ketahuan jelas siapa yang sudah lunas dan siapa yang masih ngutang.',
    description: 'Lacak alur dana titipan dengan visual jelas. Tagihan yang sudah ditransfer akan langsung terverifikasi, sementara penunggak tagihan bisa diingatkan lewat pengingat ramah.',
    interactiveType: 'radar-tracker',
    highlights: [
      { title: 'Pengingat Colek WA Sopan', desc: 'Kirim reminder pesan WhatsApp gaul & santun agar tidak canggung menagih utang ke teman.' },
      { title: 'Tampilan QRIS & No Rekening', desc: 'Host bisa menampilkan QRIS/Nomor Rekening di halaman pembayaran agar transaksi kilat.' },
      { title: 'Status Badge Visual Live', desc: 'Warna Hijau (LUNAS) & Merah Soft Pulse (NGUTANG) memudahkan monitoring status bayar.' },
      { title: 'Bebas Double Payment', desc: 'Catatan keuangan tersimpan rapi sehingga tidak ada kebingungan uang kembalian.' }
    ],
    tips: [
      'Klik tombol "Colek WA" untuk menyalin pesan pengingat siap kirim ke teman yang belum bayar.',
      'Host dapat langsung mengklik badge status untuk mengubahnya menjadi LUNAS begitu bukti transfer masuk.'
    ]
  }
]

const openCardDetail = (cardIndex) => {
  selectedModalCard.value = bentoCardsData[cardIndex]
  isModalOpen.value = true
}
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
        Solusi cerdas biar acara makan-makan bareng teman tetap seru tanpa drama salah hitung. <span class="text-orange-600 font-bold underline decoration-wavy decoration-orange-400">Klik card untuk detail! ✦</span>
      </p>
    </div>

    <!-- Bento Grid Showcase (Cards with Interactive Motion Popups) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
      
      <!-- FITUR 1: Catat Bareng (Share Link) -->
      <div 
        @click="openCardDetail(0)"
        class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 hover:shadow-2xl hover:shadow-orange-500/10 transition-all duration-300 group flex flex-col justify-between cursor-pointer hover:-translate-y-1.5"
      >
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-100 rounded-full blur-2xl group-hover:bg-orange-200/60 transition-colors" />

        <div class="relative z-10 space-y-4">
          <div class="flex items-center justify-between">
            <div class="w-12 h-12 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
              🔗
            </div>
            <span class="px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-200 text-[10px] font-extrabold flex items-center gap-1 group-hover:bg-orange-500 group-hover:text-white transition-colors">
              <Sparkles class="w-3 h-3" /> Detail Fitur ✦
            </span>
          </div>

          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 group-hover:text-orange-600 transition-colors">
              Catat Bareng (Kolaborasi)
            </h3>
            <p class="text-slate-600 text-sm leading-relaxed font-medium">
              Tinggal share link ke grup WA, biarkan teman-teman sirkelmu isi pesanan makanannya sendiri. Gak perlu catat manual satu-satu!
            </p>
          </div>
        </div>

        <!-- Share Link Interactive Demo Box -->
        <div class="relative z-10 pt-6 mt-6 border-t border-slate-100">
          <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between gap-2 group-hover:border-orange-200 transition-colors">
            <span class="text-xs font-mono text-slate-600 truncate">nitipdong.com/join/makan-siang</span>
            <button 
              @click.stop="copyLinkDemo"
              class="px-3 py-1.5 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition shrink-0 cursor-pointer"
            >
              {{ copiedLink ? '✓ Tersalin!' : 'Copy Link' }}
            </button>
          </div>

          <!-- Click Hint Footer -->
          <div class="mt-4 pt-2 flex items-center justify-between text-xs font-bold text-orange-500 opacity-90 group-hover:opacity-100">
            <span class="flex items-center gap-1">Lihat Simulasi Live & Detail <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" /></span>
          </div>
        </div>
      </div>

      <!-- FITUR 2: Auto Split-Bill & Tax Calculator -->
      <div 
        @click="openCardDetail(1)"
        class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 hover:shadow-2xl hover:shadow-orange-500/10 transition-all duration-300 group flex flex-col justify-between cursor-pointer hover:-translate-y-1.5"
      >
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-orange-100 rounded-full blur-2xl group-hover:bg-amber-200/60 transition-colors" />

        <div class="relative z-10 space-y-4">
          <div class="flex items-center justify-between">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
              🧮
            </div>
            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-200 text-[10px] font-extrabold flex items-center gap-1 group-hover:bg-amber-500 group-hover:text-white transition-colors">
              <Sparkles class="w-3 h-3" /> Detail Fitur ✦
            </span>
          </div>

          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 group-hover:text-amber-600 transition-colors">
              Auto Split-Bill Cerdas
            </h3>
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

          <!-- Click Hint Footer -->
          <div class="mt-4 pt-2 flex items-center justify-between text-xs font-bold text-amber-600 opacity-90 group-hover:opacity-100">
            <span class="flex items-center gap-1">Kalkulator Simulasi Live <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" /></span>
          </div>
        </div>
      </div>

      <!-- FITUR 3: Radar Tagihan (Lunas vs Ngutang) -->
      <div 
        @click="openCardDetail(2)"
        class="bg-white border-2 border-orange-100 rounded-3xl p-8 relative overflow-hidden shadow-xl hover:border-orange-400 hover:shadow-2xl hover:shadow-orange-500/10 transition-all duration-300 group flex flex-col justify-between cursor-pointer hover:-translate-y-1.5"
      >
        <div class="relative z-10 space-y-4">
          <div class="flex items-center justify-between">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
              📡
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-extrabold flex items-center gap-1 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
              <Sparkles class="w-3 h-3" /> Detail Fitur ✦
            </span>
          </div>

          <div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 group-hover:text-emerald-600 transition-colors">
              Radar Tagihan Real-Time
            </h3>
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

          <!-- Click Hint Footer -->
          <div class="mt-4 pt-2 flex items-center justify-between text-xs font-bold text-emerald-600 opacity-90 group-hover:opacity-100">
            <span class="flex items-center gap-1">Demo Colek WA & Tracking <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" /></span>
          </div>
        </div>
      </div>

    </div>

    <!-- Animated Card Detail Modal -->
    <CardDetailModal 
      :isOpen="isModalOpen" 
      :cardData="selectedModalCard" 
      @close="isModalOpen = false" 
    />
  </div>
</template>
