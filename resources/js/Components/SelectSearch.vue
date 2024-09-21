<script setup>
import VueSelect from 'vue-select';
const books = [
  { title: "Mosquera" , value: "Mosquera"},
  { title: "Funza" , value: "Funza"},
  { title: "Madrid" , value: "Madrid"},
  { title: "Girardot" , value: "Girardot"},
  { title: "Medellin" , value: "Medellin"},
  { title: "Suba" , value: "Suba"}
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