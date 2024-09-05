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

const input = ref(null);
const label = ref(null);
</script>

<template>
    <div class="inputContainer">
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
        <label ref="label" :for="id" v-if="labelValue">
            {{ labelValue }}
        </label>
    </div>
</template>

<script>
export default {
    data() {
        return {
            hasFocus: false
        }
    }
}
</script>