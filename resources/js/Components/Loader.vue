<template>
  <div class="loader-container">
    <div class="loader"></div>
    <p id="message-1">Cargando{{ message1 }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const message1 = ref(null)
const messages = [
  '.',
  '..',
  '...'
]
let i = 0
setInterval(() => {
  message1.value = messages[i]
  i = (i + 1) % messages.length
}, 2000);
</script>