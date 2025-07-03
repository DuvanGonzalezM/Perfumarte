<template>
    <button v-if="disabled" 
            class="btn btn-prais btn-primary" 
            :class="{
                'btn-primary-mobile': isMobile,
                'btn-primary-tablet': isTablet,
                'opacity-50 cursor-not-allowed': disabled
            }"
            :disabled="disabled">
        <slot />
    </button>
    <a v-else 
       class="btn btn-prais btn-primary" 
       :class="{
           'btn-primary-mobile': isMobile,
           'btn-primary-tablet': isTablet
       }"
       :href="href">
        <slot />
    </a>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false
    },
    href: {
        type: String,
        default: '#'
    }
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