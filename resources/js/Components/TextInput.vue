<script setup>
import { ref } from 'vue';

const model = defineModel();

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
    required: {
        type: Boolean,
    },
    focus: {
        type: Boolean,
    },
    messageError: {
        type: String,
    },
});

const hasFocus = ref(false);
const input = ref(null);
const label = ref(null);
</script>

<template>
    <div class="inputContainer position-relative" :class="{'errorField': messageError && !(hasFocus || model || focus)}">
        <input 
            :id="id"
            :name="name"
            :class="{ 'focus': hasFocus || model || focus }"
            :type="type"
            @focus="hasFocus = true"
            @blur="hasFocus = false"
            v-model="model"
            :required="required"
        />
        <label class="position-absolute pe-none" ref="label" :for="id" v-if="labelValue">
            {{ labelValue }}
        </label>
        <div class="mt-1" v-if="messageError && !(hasFocus || model || focus)">
            <p>
                {{ messageError }}
            </p>
        </div>
    </div>
</template>