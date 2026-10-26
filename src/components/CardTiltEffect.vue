<script setup>
import { ref, computed } from 'vue'

const cardRef = ref(null)
const rotateX = ref(0)
const rotateY = ref(0)
const isHovered = ref(false)

const maxTilt = 7 // Derajat kemiringan maksimal agar tetap nyaman dibaca

const handleMouseMove = (e) => {
  if (!cardRef.value) return
  isHovered.value = true

  const rect = cardRef.value.getBoundingClientRect()
  const mouseX = e.clientX - rect.left - rect.width / 2
  const mouseY = e.clientY - rect.top - rect.height / 2

  // Normalisasi antara -1 sampai 1
  const normX = Math.max(-1, Math.min(1, mouseX / (rect.width / 2)))
  const normY = Math.max(-1, Math.min(1, mouseY / (rect.height / 2)))

  // Inversi Y untuk rotasi X agar bagian atas condong ke depan saat mouse di atas
  rotateX.value = -normY * maxTilt
  rotateY.value = normX * maxTilt
}

const handleMouseLeave = () => {
  isHovered.value = false
  rotateX.value = 0
  rotateY.value = 0
}

const cardTransformStyle = computed(() => {
  return {
    transform: `perspective(1500px) rotateX(${rotateX.value.toFixed(2)}deg) rotateY(${rotateY.value.toFixed(2)}deg)`,
    transformStyle: 'preserve-3d',
    transition: isHovered.value 
      ? 'transform 0.12s ease-out' 
      : 'transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)'
  }
})
</script>

<template>
  <div class="w-full max-w-4xl relative z-10" style="perspective: 1500px;">
    <div
      ref="cardRef"
      class="relative group"
      :style="cardTransformStyle"
      @mousemove="handleMouseMove"
      @mouseleave="handleMouseLeave"
    >
      <!-- Ambient Card Glow saat hover -->
      <div 
        class="absolute -inset-[3px] rounded-[28px] opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none -z-10"
        style="animation: card-glow-pulse 4s ease-in-out infinite;"
      />

      <!-- Traveling Light Beam Effect yang mengitari border card -->
      <div class="absolute -inset-[2px] rounded-[26px] overflow-hidden pointer-events-none z-20">
        <!-- Top Light Beam (Bergerak dari kiri ke kanan) -->
        <div class="absolute top-0 left-0 h-[3px] w-[50%] bg-gradient-to-r from-transparent via-orange-400 to-transparent animate-beam-top opacity-80 shadow-[0_0_8px_rgba(251,146,60,0.8)]" />
        
        <!-- Right Light Beam (Bergerak dari atas ke bawah) -->
        <div class="absolute top-0 right-0 h-[50%] w-[3px] bg-gradient-to-b from-transparent via-amber-400 to-transparent animate-beam-right opacity-80 shadow-[0_0_8px_rgba(251,191,36,0.8)]" />
        
        <!-- Bottom Light Beam (Bergerak dari kanan ke kiri) -->
        <div class="absolute bottom-0 right-0 h-[3px] w-[50%] bg-gradient-to-r from-transparent via-orange-400 to-transparent animate-beam-bottom opacity-80 shadow-[0_0_8px_rgba(251,146,60,0.8)]" />
        
        <!-- Left Light Beam (Bergerak dari bawah ke atas) -->
        <div class="absolute bottom-0 left-0 h-[50%] w-[3px] bg-gradient-to-b from-transparent via-amber-400 to-transparent animate-beam-left opacity-80 shadow-[0_0_8px_rgba(251,191,36,0.8)]" />

        <!-- Corner Subtle Glow Spots -->
        <div class="absolute top-0 left-0 h-2 w-2 rounded-full bg-orange-400/70 blur-[1px] animate-pulse" />
        <div class="absolute top-0 right-0 h-2.5 w-2.5 rounded-full bg-amber-400/80 blur-[1.5px] animate-pulse [animation-delay:500ms]" />
        <div class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-orange-400/80 blur-[1.5px] animate-pulse [animation-delay:1000ms]" />
        <div class="absolute bottom-0 left-0 h-2 w-2 rounded-full bg-amber-400/70 blur-[1px] animate-pulse [animation-delay:1500ms]" />
      </div>

      <!-- Card Border Shimmer Glow on hover -->
      <div class="absolute -inset-[1px] rounded-[26px] bg-gradient-to-r from-orange-400/10 via-amber-400/20 to-orange-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none z-10" />

      <!-- Konten Asli Card (Slot) -->
      <div class="relative z-10 w-full">
        <slot />
      </div>
    </div>
  </div>
</template>
