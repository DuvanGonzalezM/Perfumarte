<script setup>
import Image from '@/Components/Image.vue';
import Loader from '@/Components/Loader.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const { props } = usePage();
const isMobile = ref(false);

const attributes = defineProps({
    loading: {
        type: Boolean,
        required: false
    },
    title: {
        type: String,
        required: false
    }
});

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}

</script>
<template>
    <Loader v-if="attributes.loading"/>
    <div class="container body-login d-flex align-items-center justify-content-center">
        <div class="card card-login align-items-center">
            <div class="over-card position-relative align-items-center d-flex flex-column">
                <Image src="\assets\images\Logo_3.avif" class="responsive-logo" />
                <h3 class="login-title">{{ attributes.title || 'Inicio de Sesión' }}</h3>
            </div>
            <div class="card-body position-relative">
                <slot />
            </div>
        </div>
    </div>
</template>