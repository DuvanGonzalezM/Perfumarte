<script setup>
import { computed, ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
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

const classes = computed(() => {
    let baseClass = 'responsive-nav-link';
    
    if (props.active) {
        baseClass += ' active';
    }
    
    if (isMobile.value) {
        baseClass += ' responsive-nav-link-mobile';
    } else if (isTablet.value) {
        baseClass += ' responsive-nav-link-tablet';
    }
    
    return baseClass;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
