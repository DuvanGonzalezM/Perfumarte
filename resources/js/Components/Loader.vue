<template>
  <div class="loader-container" :class="{'loader-mobile': isMobile}">
    <div class="loader"></div>
    <p id="message-1">Cargando{{ message1 }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const message1 = ref(null)
const isMobile = ref(false)

const messages = [
  '.',
  '..',
  '...'
]

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
  
  let i = 0
  setInterval(() => {
    message1.value = messages[i]
    i = (i + 1) % messages.length
  }, 2000)
})

function checkScreenSize() {
  isMobile.value = window.innerWidth < 768
}
</script>