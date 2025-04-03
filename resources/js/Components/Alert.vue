<template>
    <div class="alert alert-prais" :class="'alert-' + type" role="alert">
        <div :class="{'mb-2': isMobile}" class="alert-icon" v-if="icon">
            <i class="fas" :class="icon"></i>
        </div>
        <div class="ms-2">
            {{ message }}
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success'
    },
    message: {
        type: String,
        default: 'Este es un ejemplo de una alerta para Perfumarte'
    },
    icon: {
        type: String,
        default: 'fa-check-circle'
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