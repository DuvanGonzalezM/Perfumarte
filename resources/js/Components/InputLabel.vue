<script setup>
import { ref, onMounted, computed } from 'vue';

defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false
    }
});

const isMobile = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 input-label" :class="{ 
        'required': required, 
        'input-label-mobile': isMobile 
    }">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
