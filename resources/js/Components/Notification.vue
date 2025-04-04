<template>
    <div class="card notifications" :class="{'mobile-notifications': isMobile}">
        <div v-for="notification in notifications" :key="notification.id" 
            class="alert" :class="'alert-' + notification.type" role="alert">
            {{ notification.message }}
        </div>
        <div v-if="notifications.length === 0" class="alert alert-info">
            No tienes notificaciones nuevas.
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isMobile = ref(false);
const notifications = ref([
    {
        id: 1,
        type: 'warning',
        message: 'Este es un ejemplo de una notificación.'
    },
    {
        id: 2,
        type: 'info',
        message: 'Tienes mensajes pendientes por revisar.'
    }
]);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>