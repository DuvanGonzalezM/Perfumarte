<template>
    <div class="card notifications" :class="{'mobile-notifications': isMobile}">
        <a id="readAll" v-if="props.notifications.length > 0" href="#" @click="props.readNotification(null, false, true)">Marcar todo como leido</a><br>
        <a href="#" v-for="notification in props.notifications" :key="notification.id" 
            class="alert-prais" :class="'alert-info'" role="alert">
            <span @click="props.readNotification(notification, true)" >{{ notification.data.message }}</span>
            <button type="button" class="btn-close" aria-label="Close" @click="props.readNotification(notification, false)"></button>
        </a>
        <div v-if="props.notifications.length === 0" class="alert alert-info">
            <a>No tienes notificaciones nuevas.</a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isMobile = ref(false);

const props = defineProps({
    notifications: {
        type: Array,
    },
    readNotification: {
        type: Function,
    },
});

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}

</script>