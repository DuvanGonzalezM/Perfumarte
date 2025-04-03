<template>
    <a class="btn btn-prais btn-primary" 
       :class="{
           'btn-primary-mobile': isMobile,
           'btn-primary-tablet': isTablet
       }">
        <slot />
    </a>
</template>

<script setup>
import { ref, onMounted } from 'vue';

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