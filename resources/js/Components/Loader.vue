<template>
  <div class="loader-container" :class="{ 'loader-mobile': isMobile }">
    <div class="fragrance-bottle">
      <svg :width="isMobile ? 80 : 120" :height="isMobile ? 140 : 200" viewBox="0 0 120 200" class="bottle-svg">
        <path d="M30 70 L30 160 Q30 175 45 175 L75 175 Q90 175 90 160 L90 70 Q90 55 75 55 L45 55 Q30 55 30 70"
          class="bottle-body" />

        <rect x="50" y="35" width="20" height="20" class="bottle-neck" rx="2" />

        <rect x="46" y="25" width="28" height="15" class="bottle-cap" rx="4" />

        <rect x="48" y="28" width="24" height="2" class="cap-detail" rx="1" />
        <rect x="48" y="32" width="24" height="2" class="cap-detail" rx="1" />

        <defs>
          <clipPath id="bottleShape">
            <path d="M32 72 L32 158 Q32 173 47 173 L73 173 Q88 173 88 158 L88 72 Q88 57 73 57 L47 57 Q32 57 32 72" />
          </clipPath>

          <linearGradient id="liquidGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" class="liquid-top" />
            <stop offset="50%" class="liquid-middle" />
            <stop offset="100%" class="liquid-bottom" />
          </linearGradient>

          <linearGradient id="shimmerGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" class="shimmer-start" />
            <stop offset="50%" class="shimmer-middle" />
            <stop offset="100%" class="shimmer-end" />
          </linearGradient>

          <linearGradient id="glassGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="glass-highlight" />
            <stop offset="100%" class="glass-shadow" />
          </linearGradient>
        </defs>
        <rect x="32" y="72" width="56" height="101" fill="url(#liquidGradient)" clip-path="url(#bottleShape)"
          class="liquid-fill" />
        <rect x="38" y="100" width="44" height="60" class="bottle-label" rx="4" />
      </svg>

      <div class="sparkles">
        <div class="sparkle sparkle-1"></div>
        <div class="sparkle sparkle-2"></div>
        <div class="sparkle sparkle-3"></div>
        <div class="sparkle sparkle-4"></div>
        <div class="sparkle sparkle-5"></div>
        <div class="sparkle sparkle-6"></div>
      </div>
    </div>
    <img class="logo" src="/assets/images/Logo_5.avif" />

    <p class="loading-text">
      {{ message || 'Cargando' }}{{ dots }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

interface Props {
  message?: string
}

const props = withDefaults(defineProps<Props>(), {
  message: 'Cargando'
})

const dots = ref('')
const isMobile = ref(false)

const dotMessages = ['.', '..', '...']
let dotInterval: number | null = null

const checkScreenSize = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)

  let i = 0
  dotInterval = setInterval(() => {
    dots.value = dotMessages[i]
    i = (i + 1) % dotMessages.length
  }, 2000)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize)
  if (dotInterval) {
    clearInterval(dotInterval)
  }
})
</script>