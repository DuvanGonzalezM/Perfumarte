<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    type: {
        type: String,
        default: 'button',
    },
});

const isMobile = ref(false);
const isTablet = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 576;
    isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
}
</script>

<template>
    <button
        :type="type"
        class="btn-secondary"
        :class="{
            'btn-secondary-mobile': isMobile,
            'btn-secondary-tablet': isTablet
        }"
    >
        <slot />
    </button>
</template>
