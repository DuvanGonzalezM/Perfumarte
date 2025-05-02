<script setup>
import Notification from './Notification.vue';
import { is } from 'laravel-permission-to-vuejs';
import { ref, onMounted, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const isMobile = ref(false);
const notifications = ref(usePage().props.auth.user.unread_notifications);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}

watchEffect(() => {
    Echo.private('App.Models.User.'+usePage().props.auth.user.user_id)
        .notification((notification) => {
            notifications.value.unshift(notification.newNotification);
        });
});

const readNotification = async (notification, redirect) => {
    await axios.post(route('notifications.read', notification.id))
        .then((response) => {
            notifications.value = response.data;
            if (redirect) {
                window.location.href = notification.data.url;
            }
        })
        .catch(error => {
            console.error(error);
        });
}
</script>
<template>
    <div class="accountControls row">
        <div class="dropstart col-6" :class="[notifications.length > 0 ? 'has-unread' : '']">
            <div class="notification-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-comment"></i>
                <span v-if="notifications.length > 0" class="notification-badge">
                    {{ notifications.length }}
                </span>
            </div>
            <div class="dropdown-menu">
                <Notification :notifications="notifications" :readNotification="readNotification"/>
            </div>
        </div>
        <div class="dropdown-center profile col-6" :class="[{'ms-2' : isMobile}]">
            <i class="fa-solid fa-user" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <ul class="dropdown-menu dropdown-menu-end">
                <strong>{{ $page.props.auth.user.name }}</strong>
                <li class="my-2">
                    <a :href="route('users.list')" v-if="is('Administrador | Gerencia')">
                        <i class="fa-solid fa-users me-2"></i>
                        Usuarios
                    </a>
                    <hr>
                    <a :href="route('locations.list')" v-if="is('Administrador | Gerencia')">
                        <i class="fa-solid fa-warehouse me-2"></i>
                        Sedes
                    </a>
                </li>
                <li class="mt-2 text-end">
                    <a :href="route('logout')" method="post" as="button">
                        Cerrar Sesión
                        <i class="fa-solid fa-door-open ms-2"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>