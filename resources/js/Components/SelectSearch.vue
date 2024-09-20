<script setup>
import VueSelect from 'vue-select';
const books = [
  { title: "Mosquera" },
  { title: "Funza" },
  { title: "Madrid" },
  { title: "Girardot" },
  { title: "Medellin" },
  { title: "Suba" }
];
</script>


<template>
  <div class="selectsearch">
    <VueSelect :options="books" label="title">
      <template v-slot:no-options>
        <div class="no-options-message">
          No hay opciones disponibles
        </div>
      </template>
    </VueSelect>
  </div>
</template>