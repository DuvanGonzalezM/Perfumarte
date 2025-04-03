<script setup>
import Notification from './Notification.vue';
import { is } from 'laravel-permission-to-vuejs';
import { ref, onMounted } from 'vue';

const isMobile = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>
<template>
    <div class="accountControls row">
        <div class="dropstart" :class="isMobile ? 'col-auto' : 'col-6'">
            <i class="fa-solid fa-comment" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <div class="dropdown-menu">
                <Notification />
            </div>
        </div>
        <div class="dropdown-center profile" :class="isMobile ? 'col-auto ms-2' : 'col-6'">
            <i class="fa-solid fa-user" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <ul class="dropdown-menu dropdown-menu-end">
                <strong>{{ $page.props.auth.user.name }}</strong>
                <li class="my-2">
                    <a :href="route('users.list')" v-if="is('Administrador')">
                        <i class="fa-solid fa-users me-2"></i>
                        Usuarios
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