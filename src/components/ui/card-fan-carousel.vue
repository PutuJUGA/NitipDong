<script setup>
import { ref, computed } from 'vue'
import { Check, ChevronLeft, ChevronRight, Sparkles, User, Palette } from 'lucide-vue-next'

const props = defineProps({
  cards: {
    type: Array,
    default: () => []
  },
  modelValue: {
    type: [String, Object],
    default: ''
  },
  userName: {
    type: String,
    default: 'Nama User'
  }
})

const emit = defineEmits(['update:modelValue', 'select'])

// Index card yang sedang di-hover atau aktif di fan
const hoveredIndex = ref(null)
const isFanHovered = ref(false)

// Cari index kartu yang saat ini aktif
const activeIndex = computed(() => {
  if (!props.cards || props.cards.length === 0) return 0
  const val = typeof props.modelValue === 'string' ? props.modelValue : props.modelValue?.url
  const idx = props.cards.findIndex(c => (c.url === val || c.imgUrl === val || c.id === props.modelValue?.id || c.id === props.modelValue))
  return idx !== -1 ? idx : 0
})

const selectedCard = computed(() => {
  if (!props.cards || props.cards.length === 0) return null
  return props.cards[activeIndex.value] || props.cards[0]
})

const selectCard = (card, index) => {
  const value = card.url || card.imgUrl || card.id
  emit('update:modelValue', value)
  emit('select', card)
}

const prevCard = () => {
  if (!props.cards.length) return
  const newIdx = (activeIndex.value - 1 + props.cards.length) % props.cards.length
  selectCard(props.cards[newIdx], newIdx)
}

const nextCard = () => {
  if (!props.cards.length) return
  const newIdx = (activeIndex.value + 1) % props.cards.length
  selectCard(props.cards[newIdx], newIdx)
}

// Menghitung transformasi matematika busur fanned arc untuk tiap kartu
const getCardStyle = (index) => {
  const total = props.cards.length
  if (total === 0) return {}

  const center = (total - 1) / 2
  const offset = index - center
  const isSelected = index === activeIndex.value
  const isHovered = hoveredIndex.value === index

  // Parameter penyebaran (spread) saat container di-hover vs normal
  const spreadMultiplier = isFanHovered.value ? 68 : 52
  const rotateStep = isFanHovered.value ? 8.5 : 6.5

  let tx = offset * spreadMultiplier
  let ty = Math.pow(Math.abs(offset), 1.6) * 7
  let rot = offset * rotateStep
  let scale = 0.94
  let zIndex = 10 + Math.round(10 - Math.abs(offset))

  if (isSelected) {
    ty -= 24
    scale = 1.05
    rot = rot * 0.4
    zIndex = 30
  }

  if (isHovered) {
    ty -= 32
    scale = 1.08
    rot = 0
    zIndex = 40
  }

  return {
    transform: `translateX(${tx}px) translateY(${ty}px) rotate(${rot}deg) scale(${scale})`,
    zIndex,
    transformOrigin: '50% 120%',
    transition: 'transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, z-index 0.2s ease'
  }
}
</script>

<template>
  <div class="w-full flex flex-col items-center select-none py-2">
    
    <!-- ==================== LIVE PREVIEW CARD ==================== -->
    <div 
      v-if="selectedCard" 
      class="w-full max-w-md mb-6 p-4 rounded-3xl border-2 transition-all duration-300 shadow-md flex items-center justify-between gap-4"
      :style="{
        backgroundColor: selectedCard.lightBg || 'rgba(249, 115, 22, 0.08)',
        borderColor: selectedCard.primary || '#f97316',
        boxShadow: `0 10px 25px -5px ${selectedCard.shadow || 'rgba(249, 115, 22, 0.25)'}`
      }"
    >
      <div class="flex items-center gap-3.5">
        <!-- Avatar Preview Gambar -->
        <div class="relative shrink-0">
          <img 
            :src="selectedCard.url || selectedCard.imgUrl" 
            :alt="selectedCard.label || selectedCard.alt || 'Avatar'"
            class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-800 object-cover shadow-sm border-2"
            :style="{ borderColor: selectedCard.primary || '#f97316' }"
          />
          <div 
            class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full text-white flex items-center justify-center text-[10px] font-black shadow-xs"
            :style="{ backgroundColor: selectedCard.primary || '#f97316' }"
          >
            ✓
          </div>
        </div>

        <!-- Nama User & Info Karakter/Tema -->
        <div class="text-left min-w-0">
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full text-white" :style="{ backgroundColor: selectedCard.primary || '#f97316' }">
              {{ selectedCard.label || selectedCard.alt || 'Avatar' }}
            </span>
            <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400">
              {{ selectedCard.role || 'Karakter Sirkel' }}
            </span>
          </div>

          <h4 class="text-base font-black text-slate-900 dark:text-white truncate mt-0.5">
            {{ userName || 'Putu Ganteng' }}
          </h4>

          <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-zinc-300">
            <Palette class="w-3.5 h-3.5" :style="{ color: selectedCard.primary || '#f97316' }" />
            <span>Tema: <strong>{{ selectedCard.themeName || 'Oranye Sunset' }}</strong></span>
          </div>
        </div>
      </div>

      <!-- Badge Indikator Tema -->
      <div class="hidden sm:flex flex-col items-end shrink-0 text-right">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500">
          Aksen Menu Utama
        </span>
        <div class="flex items-center gap-1.5 mt-1">
          <span 
            class="w-4 h-4 rounded-full shadow-xs border border-white dark:border-zinc-800"
            :style="{ backgroundColor: selectedCard.primary || '#f97316' }"
          />
          <span 
            class="w-4 h-4 rounded-full shadow-xs border border-white dark:border-zinc-800"
            :style="{ backgroundColor: selectedCard.secondary || '#f59e0b' }"
          />
        </div>
      </div>
    </div>

    <!-- ==================== FANNED ARC CAROUSEL DECK ==================== -->
    <div class="relative w-full flex items-center justify-center py-6 overflow-visible">
      
      <!-- Tombol Navigasi Kiri -->
      <button 
        type="button"
        @click="prevCard"
        class="absolute left-1 sm:left-4 z-40 p-2 rounded-full bg-white/90 dark:bg-zinc-800/90 shadow-md border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 hover:scale-110 active:scale-95 transition cursor-pointer"
        title="Avatar Sebelumnya"
      >
        <ChevronLeft class="w-5 h-5" />
      </button>

      <!-- Fan Cards Stage Container -->
      <div 
        class="relative h-64 sm:h-72 w-full max-w-xl flex items-center justify-center overflow-visible"
        @mouseenter="isFanHovered = true"
        @mouseleave="isFanHovered = false; hoveredIndex = null"
      >
        <div 
          v-for="(card, index) in cards" 
          :key="card.id || index"
          class="absolute cursor-pointer transition-transform duration-300"
          :style="getCardStyle(index)"
          @mouseenter="hoveredIndex = index"
          @mouseleave="hoveredIndex = null"
          @click="selectCard(card, index)"
        >
          <!-- Single Playing Card Frame -->
          <div 
            class="w-32 sm:w-36 h-48 sm:h-54 rounded-2xl p-2.5 flex flex-col justify-between backdrop-blur-md transition-all duration-300 border-2 shadow-lg bg-white dark:bg-zinc-800"
            :class="index === activeIndex ? 'ring-4 ring-offset-2 ring-offset-white dark:ring-offset-zinc-900' : 'hover:shadow-2xl'"
            :style="{
              borderColor: card.primary || '#f97316',
              boxShadow: index === activeIndex 
                ? `0 14px 28px -4px ${card.shadow || 'rgba(249, 115, 22, 0.35)'}` 
                : '0 8px 16px -2px rgba(0, 0, 0, 0.1)',
              '--tw-ring-color': card.primary || '#f97316'
            }"
          >
            <!-- Card Header: Pill Label & Theme Swatch -->
            <div class="flex items-center justify-between">
              <span 
                class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full text-white truncate max-w-[70px]"
                :style="{ backgroundColor: card.primary || '#f97316' }"
              >
                {{ card.label || card.alt || 'Avatar' }}
              </span>

              <div 
                v-if="index === activeIndex"
                class="w-4 h-4 rounded-full text-white flex items-center justify-center text-[9px] font-black shadow-2xs"
                :style="{ backgroundColor: card.primary || '#f97316' }"
              >
                ✓
              </div>
              <span 
                v-else
                class="w-2.5 h-2.5 rounded-full border border-slate-300 dark:border-zinc-600"
                :style="{ backgroundColor: card.primary || '#f97316' }"
              />
            </div>

            <!-- Card Body: Avatar Image & Glowing Halo -->
            <div class="my-auto flex flex-col items-center">
              <div 
                class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl p-1 bg-gradient-to-b shadow-inner flex items-center justify-center"
                :style="{
                  backgroundImage: `linear-gradient(to bottom, ${card.lightBg || 'rgba(249, 115, 22, 0.15)'}, rgba(255,255,255,0.1))`
                }"
              >
                <img 
                  :src="card.url || card.imgUrl" 
                  :alt="card.label || card.alt" 
                  class="w-full h-full rounded-xl object-cover drop-shadow-md"
                />
              </div>
            </div>

            <!-- Card Footer: Role & User Name -->
            <div class="pt-1.5 border-t border-slate-100 dark:border-zinc-700/60 text-center">
              <p class="text-[10px] font-black text-slate-800 dark:text-zinc-100 truncate">
                {{ userName || 'User' }}
              </p>
              <p class="text-[9px] font-bold text-slate-500 dark:text-zinc-400 truncate" :style="{ color: card.primary || '#f97316' }">
                {{ card.role || card.themeName || 'Sirkel Member' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol Navigasi Kanan -->
      <button 
        type="button"
        @click="nextCard"
        class="absolute right-1 sm:right-4 z-40 p-2 rounded-full bg-white/90 dark:bg-zinc-800/90 shadow-md border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-300 hover:scale-110 active:scale-95 transition cursor-pointer"
        title="Avatar Selanjutnya"
      >
        <ChevronRight class="w-5 h-5" />
      </button>
    </div>

    <!-- ==================== QUICK THUMBNAIL SELECTOR ==================== -->
    <div class="flex items-center gap-2 mt-2 flex-wrap justify-center">
      <button 
        v-for="(card, idx) in cards" 
        :key="'dot-' + (card.id || idx)"
        type="button"
        @click="selectCard(card, idx)"
        class="px-2.5 py-1 rounded-xl text-[11px] font-black flex items-center gap-1.5 transition-all cursor-pointer border"
        :class="idx === activeIndex 
          ? 'scale-105 shadow-xs text-white' 
          : 'bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-200 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-700'"
        :style="idx === activeIndex ? {
          backgroundColor: card.primary || '#f97316',
          borderColor: card.primary || '#f97316'
        } : {}"
      >
        <span 
          class="w-2 h-2 rounded-full" 
          :style="{ backgroundColor: idx === activeIndex ? '#ffffff' : (card.primary || '#f97316') }" 
        />
        <span>{{ card.label || card.alt }}</span>
      </button>
    </div>

  </div>
</template>
