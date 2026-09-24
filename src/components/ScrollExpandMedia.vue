<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  mediaType: {
    type: String,
    default: 'image'
  },
  mediaSrc: {
    type: String,
    required: true
  },
  posterSrc: {
    type: String,
    default: ''
  },
  bgImageSrc: {
    type: String,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  date: {
    type: String,
    default: ''
  },
  scrollToExpand: {
    type: String,
    default: ''
  }
})

const scrollProgress = ref(0)
const showContent = ref(false)
const mediaFullyExpanded = ref(false)
const touchStartY = ref(0)
const isMobileState = ref(false)

const sectionRef = ref(null)

const firstWord = computed(() => props.title ? props.title.split(' ')[0] : '')
const restOfTitle = computed(() => props.title ? props.title.split(' ').slice(1).join(' ') : '')

const mediaWidth = computed(() => {
  const width = 320 + scrollProgress.value * (isMobileState.value ? 450 : 900)
  return Math.min(width, window.innerWidth * 0.92)
})

const mediaHeight = computed(() => {
  const height = 360 + scrollProgress.value * (isMobileState.value ? 220 : 360)
  return Math.min(height, window.innerHeight * 0.78)
})

// Clean horizontal title separation without excessive translation
const textTranslateX = computed(() => {
  return scrollProgress.value * (isMobileState.value ? 80 : 120)
})

watch(() => props.mediaType, () => {
  scrollProgress.value = 0
  showContent.value = false
  mediaFullyExpanded.value = false
})

const handleWheel = (e) => {
  // If user scrolls down past hero section, allow normal page scroll
  if (window.scrollY > 50) {
    scrollProgress.value = 1
    mediaFullyExpanded.value = true
    showContent.value = true
    return
  }

  if (mediaFullyExpanded.value && e.deltaY < 0 && window.scrollY <= 10) {
    mediaFullyExpanded.value = false
    e.preventDefault()
  } else if (!mediaFullyExpanded.value) {
    e.preventDefault()
    const scrollDelta = e.deltaY * 0.0012
    const newProgress = Math.min(Math.max(scrollProgress.value + scrollDelta, 0), 1)
    scrollProgress.value = newProgress

    if (newProgress >= 0.85) {
      mediaFullyExpanded.value = true
      showContent.value = true
    } else if (newProgress < 0.6) {
      showContent.value = false
    }
  }
}

const handleTouchStart = (e) => {
  if (e.touches && e.touches[0]) {
    touchStartY.value = e.touches[0].clientY
  }
}

const handleTouchMove = (e) => {
  if (window.scrollY > 50) {
    scrollProgress.value = 1
    mediaFullyExpanded.value = true
    showContent.value = true
    return
  }

  if (!touchStartY.value || !e.touches || !e.touches[0]) return

  const touchY = e.touches[0].clientY
  const deltaY = touchStartY.value - touchY

  if (mediaFullyExpanded.value && deltaY < -20 && window.scrollY <= 10) {
    mediaFullyExpanded.value = false
    e.preventDefault()
  } else if (!mediaFullyExpanded.value) {
    e.preventDefault()
    const scrollFactor = deltaY < 0 ? 0.008 : 0.005
    const scrollDelta = deltaY * scrollFactor
    const newProgress = Math.min(Math.max(scrollProgress.value + scrollDelta, 0), 1)
    scrollProgress.value = newProgress

    if (newProgress >= 0.85) {
      mediaFullyExpanded.value = true
      showContent.value = true
    } else if (newProgress < 0.6) {
      showContent.value = false
    }

    touchStartY.value = touchY
  }
}

const handleTouchEnd = () => {
  touchStartY.value = 0
}

const handleScroll = () => {
  // Automatically fully expand if user scrolls past top of hero
  if (window.scrollY > 50) {
    scrollProgress.value = 1
    mediaFullyExpanded.value = true
    showContent.value = true
  }
}

const checkIfMobile = () => {
  isMobileState.value = window.innerWidth < 768
}

onMounted(() => {
  checkIfMobile()
  window.addEventListener('resize', checkIfMobile)
  window.addEventListener('wheel', handleWheel, { passive: false })
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('touchstart', handleTouchStart, { passive: false })
  window.addEventListener('touchmove', handleTouchMove, { passive: false })
  window.addEventListener('touchend', handleTouchEnd)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkIfMobile)
  window.removeEventListener('wheel', handleWheel)
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('touchstart', handleTouchStart)
  window.removeEventListener('touchmove', handleTouchMove)
  window.removeEventListener('touchend', handleTouchEnd)
})
</script>

<template>
  <div ref="sectionRef" class="relative overflow-x-hidden">
    <section class="relative flex flex-col items-center justify-start min-h-[100dvh]">
      <div class="relative w-full flex flex-col items-center min-h-[100dvh]">
        
        <!-- Scoped Absolute Background Image (Fixed leak bug: Does NOT bleed into lower sections) -->
        <div 
          class="absolute inset-0 z-0 h-full transition-opacity duration-300 pointer-events-none"
          :style="{ opacity: Math.max(1 - scrollProgress * 1.5, 0) }"
        >
          <img
            :src="bgImageSrc"
            alt="Background"
            class="w-full h-full object-cover object-center filter brightness-90 saturate-110"
          />
          <div class="absolute inset-0 bg-gradient-to-b from-slate-950/60 via-slate-950/40 to-slate-950/90" />
        </div>

        <div class="container mx-auto flex flex-col items-center justify-start relative z-10 w-full px-4">
          <div class="flex flex-col items-center justify-center w-full h-[100dvh] relative">
            
            <!-- Expanding Media Card -->
            <div
              class="absolute z-0 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-3xl overflow-hidden border-2 border-amber-400/60 shadow-2xl transition-all duration-75 bg-slate-900"
              :style="{
                width: `${mediaWidth}px`,
                height: `${mediaHeight}px`
              }"
            >
              <div v-if="mediaType === 'video'" class="relative w-full h-full pointer-events-none">
                <video
                  :src="mediaSrc"
                  :poster="posterSrc"
                  autoplay
                  muted
                  loop
                  playsinline
                  class="w-full h-full object-cover rounded-2xl"
                />
                <div 
                  class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/40 rounded-2xl transition-opacity"
                  :style="{ opacity: 0.6 - scrollProgress * 0.4 }"
                />
              </div>

              <div v-else class="relative w-full h-full">
                <img
                  :src="mediaSrc"
                  :alt="title || 'Media content'"
                  class="w-full h-full object-cover rounded-2xl"
                />
                <div 
                  class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-black/30 to-black/20 rounded-2xl transition-opacity"
                  :style="{ opacity: 0.6 - scrollProgress * 0.4 }"
                />
              </div>

              <!-- Inner Media Badges -->
              <div class="absolute bottom-6 left-0 right-0 z-20 flex flex-col items-center text-center text-xs font-semibold px-4 pointer-events-none">
                <p 
                  v-if="date" 
                  class="text-base sm:text-xl text-amber-300 font-extrabold tracking-wider transition-transform drop-shadow-[0_2px_8px_rgba(0,0,0,0.9)]"
                  :style="{ transform: `translateX(-${textTranslateX}px)` }"
                >
                  {{ date }}
                </p>
                <p 
                  v-if="scrollToExpand && scrollProgress < 0.8" 
                  class="text-orange-200 font-bold text-center animate-bounce mt-2 transition-all bg-slate-950/80 px-4 py-1.5 rounded-full border border-orange-400/40 backdrop-blur-md shadow-lg"
                  :style="{ transform: `translateX(${textTranslateX}px)` }"
                >
                  {{ scrollToExpand }}
                </p>
              </div>
            </div>

            <!-- Crisp Clean High-Contrast Title Overlay (Fixed unreadable text blend bug) -->
            <div class="flex items-center justify-center text-center gap-3 w-full relative z-10 flex-col pointer-events-none max-w-5xl mx-auto px-4">
              <h1
                class="text-4xl sm:text-6xl lg:text-7xl font-black text-white uppercase tracking-tight transition-all leading-tight drop-shadow-[0_4px_16px_rgba(0,0,0,0.9)]"
                :style="{ 
                  transform: `translateX(-${textTranslateX}px)`,
                  opacity: Math.max(1 - scrollProgress * 1.2, 0)
                }"
              >
                {{ firstWord }}
              </h1>
              <h1
                class="text-2xl sm:text-5xl lg:text-6xl font-black text-center text-white uppercase tracking-tight transition-all leading-tight drop-shadow-[0_4px_16px_rgba(0,0,0,0.9)]"
                :style="{ 
                  transform: `translateX(${textTranslateX}px)`,
                  opacity: Math.max(1 - scrollProgress * 1.2, 0)
                }"
              >
                {{ restOfTitle }}
              </h1>
            </div>
          </div>

          <!-- Revealed Slot Content -->
          <section
            class="flex flex-col w-full max-w-4xl px-4 sm:px-8 py-6 transition-opacity duration-700 relative z-20"
            :style="{ opacity: showContent ? 1 : 0 }"
          >
            <slot />
          </section>
        </div>
      </div>
    </section>
  </div>
</template>
