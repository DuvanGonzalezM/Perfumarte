<template>
    <div class="alert alert-prais" :class="'alert-' + type" role="alert">
        <div :class="{'mb-2': isMobile}" class="alert-icon" v-if="icon">
            <i class="fas" :class="icon"></i>
        </div>
        <div class="ms-2">
            <template v-if="message">{{ message }}</template>
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
    // Sin texto por defecto: una alerta puede traer solo contenido en el slot,
    // y un marcador de ejemplo se colaba a la pantalla del usuario.
    message: {
        type: String,
        default: ''
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