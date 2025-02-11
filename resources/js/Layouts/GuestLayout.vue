<script setup>
import Image from '@/Components/Image.vue';
import Loader from '@/Components/Loader.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();

const attributes = defineProps({
    loading: {
        type: Boolean,
        required: false
    },
});

</script>
<template>
    <Loader v-if="attributes.loading"/>
    <div class="container body-login" v-cloak>
        <div class="card card-login align-items-center">
            <div class="over-card position-relative align-items-center d-flex flex-column">
                <Image src="assets\images\Logo_3.avif" />
                <h3>Inicio de Sesión</h3>
            </div>
            <div class="card-body position-relative">
                <slot />
            </div>
        </div>
    </div>
</template>