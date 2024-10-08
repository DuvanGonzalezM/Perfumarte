<script setup>
import Notification from './Notification.vue';
</script>
<template>
    <div class="accountControls row">
        <div class="dropstart col-6">
            <i class="fa-solid fa-comment" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <div class="dropdown-menu">
                <Notification />
            </div>
        </div>
        <div class="dropdown-center profile col-6">
            <i class="fa-solid fa-user" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <ul class="dropdown-menu dropdown-menu-end">
                <strong>{{ $page.props.auth.user.username }}</strong>
                <li class="my-2">
                    <a :href="route('dispatch.list')">
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