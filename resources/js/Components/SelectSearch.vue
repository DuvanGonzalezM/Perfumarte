<script setup>
import VueSelect from 'vue-select';
import { ref } from 'vue';

const model = defineModel();
const select = ref(null);
const props = defineProps({
  options: {
    type: Array,
  },
  labelValue: {
    type: String,
  },
});

const hasFocus = ref(model.value != null);
const label = ref(null);
console.log(hasFocus.value, (model.value != null));

</script>

<template>
  <div class="selectsearch">
    <VueSelect :options="options" :value="model" :reduce="(option) => option.value" label="title" v-model="model"ref="select">
      <!-- :blur="hasFocus = false" :focus="hasFocus = true" :class="{ 'focus': hasFocus }"> -->
      <template v-slot:no-options>
        <div class="no-options-message">
          No hay opciones disponibles
        </div>
      </template>
    </VueSelect>
    <!-- <label class="position-absolute pe-none" ref="label" :for="id" v-if="labelValue">
      {{ labelValue }}
    </label> -->
  </div>
</template>