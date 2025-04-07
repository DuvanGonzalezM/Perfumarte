<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    message: {
        type: String,
    },
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
    <div v-show="message" class="input-error" :class="{'input-error-mobile': isMobile}">
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ message }}
        </p>
    </div>
</template>
