<template>
    <div class="breadcrumbs" :class="{
        'breadcrumbs-mobile': isMobile, 
        'breadcrumbs-tablet': isTablet
    }">
        <i class="fa-solid fa-house"></i>
        <p v-if="breadcrumbs && breadcrumbs.length > 0">
            /
        </p>
        <template v-for="(crumb, index) in breadcrumbs" :key="index">
            <a :href="crumb.url" :class="{ 'active': index === breadcrumbs.length - 1 }">
                {{ crumb.name }}
            </a>
            <p v-if="index < breadcrumbs.length - 1">
                /
            </p>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => []
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