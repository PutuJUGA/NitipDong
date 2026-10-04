<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Monitor, Sun, MoonStar } from 'lucide-vue-next'

const currentTheme = ref('system') // 'system' | 'light' | 'dark'

const THEME_OPTIONS = [
  {
    value: 'system',
    label: 'Ikuti Sistem PC',
    icon: Monitor
  },
  {
    value: 'light',
    label: 'Mode Terang',
    icon: Sun
  },
  {
    value: 'dark',
    label: 'Mode Gelap',
    icon: MoonStar
  }
]

// Terapkan class .dark ke <html>
const applyTheme = (theme) => {
  const root = document.documentElement
  const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
  const isDark = theme === 'dark' || (theme === 'system' && systemPrefersDark)

  if (isDark) {
    root.classList.add('dark')
  } else {
    root.classList.remove('dark')
  }
}

const setTheme = (val) => {
  currentTheme.value = val
  try {
    localStorage.setItem('nitipdong_theme', val)
  } catch (e) {}
  applyTheme(val)
}

// Listener untuk perubahan prefers-color-scheme dari OS saat mode 'system'
let mediaQuery = null
const handleSystemThemeChange = () => {
  if (currentTheme.value === 'system') {
    applyTheme('system')
  }
}

onMounted(() => {
  try {
    const saved = localStorage.getItem('nitipdong_theme')
    if (saved && ['system', 'light', 'dark'].includes(saved)) {
      currentTheme.value = saved
    }
  } catch (e) {}

  applyTheme(currentTheme.value)

  if (window.matchMedia) {
    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    mediaQuery.addEventListener('change', handleSystemThemeChange)
  }
})

onUnmounted(() => {
  if (mediaQuery) {
    mediaQuery.removeEventListener('change', handleSystemThemeChange)
  }
})
</script>

<template>
  <div
    class="inline-flex items-center p-0.5 rounded-full bg-white ring-1 ring-zinc-200 shadow-2xs dark:bg-zinc-950 dark:ring-zinc-700 transition-colors"
    role="radiogroup"
    aria-label="Pilih Tema Tampilan"
  >
    <button
      v-for="opt in THEME_OPTIONS"
      :key="opt.value"
      type="button"
      role="radio"
      :aria-checked="currentTheme === opt.value"
      :aria-label="opt.label"
      :title="opt.label"
      @click="setTheme(opt.value)"
      class="relative flex w-7 h-7 sm:w-8 sm:h-8 cursor-pointer items-center justify-center rounded-full transition-all duration-200 text-xs"
      :class="[
        currentTheme === opt.value
          ? 'text-zinc-950 dark:text-zinc-50 font-bold bg-zinc-100 dark:bg-zinc-800 shadow-xs border border-zinc-200/80 dark:border-zinc-700'
          : 'text-zinc-400 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200'
      ]"
    >
      <component :is="opt.icon" class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform active:scale-90" />
    </button>
  </div>
</template>
