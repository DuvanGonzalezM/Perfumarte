<script setup>
import VueSelect from 'vue-select';
import { ref } from 'vue';

const model = defineModel();
const props = defineProps({
  options: {
    type: Array,
  },
  labelValue: {
    type: String,
  },
  multiple: {
    type: Boolean,
  },
  changeFunction: {
    type: Function,
    required: false,
  },
  disabled: {
    type: Boolean,
  },
  messageError: {
    type: String,
  },
});

const hasFocus = ref(false);
const label = ref(null);
</script>

<template>
  <div class="selectsearch" :class="{ 'errorField': messageError && !(hasFocus || model) }">
    <VueSelect :multiple="multiple" :options="options" :disabled="disabled"
      @option:selected="props.changeFunction ? props.changeFunction() : ''"
      @option:deselected="props.changeFunction ? props.changeFunction() : ''" @search:blur="hasFocus = false"
      @search:focus="hasFocus = true" :value="model" :class="{ 'focus': hasFocus || model }"
      :reduce="(option) => option.value" label="title" v-model="model">
      <template v-slot:no-options>
        <div class="no-options-message">
          No hay opciones disponibles
        </div>
      </template>
    </VueSelect>
    <label class="position-absolute pe-none" ref="label" v-if="labelValue">
      {{ labelValue }}
    </label>
    <div class="mt-1" v-if="messageError && !(hasFocus || model)">
      <p>
        {{ messageError }}
      </p>
    </div>
  </div>
</template>