<template>
    <div class="card notifications" :class="{'mobile-notifications': isMobile}">
        <div v-for="notification in notifications" :key="notification.id" 
            class="alert" :class="'alert-info'" role="alert">
            <a href="#">{{ notification.data.message }}</a>
        </div>
        <div v-if="notifications.length === 0" class="alert alert-info">
            No tienes notificaciones nuevas.
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const isMobile = ref(false);
const notifications = ref(usePage().props.auth.user.unread_notifications);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>