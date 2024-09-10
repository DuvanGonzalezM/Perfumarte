<script setup>
import { ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    labelValue: {
        type: String,
    },
    id: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
});

const hasFocus = ref(false);
const input = ref(null);
const label = ref(null);
</script>

<template>
    <div class="inputContainer position-relative">
        <input 
            :id="id"
            :name="name"
            :class="{ 'focus': hasFocus || (input && input.value.length) }"
            :type="type"
            @focus="hasFocus = true"
            @blur="hasFocus = false"
            v-model="model"
            ref="input"
        />
        <label class="position-absolute pe-none" ref="label" :for="id" v-if="labelValue">
            {{ labelValue }}
        </label>
    </div>
</template>