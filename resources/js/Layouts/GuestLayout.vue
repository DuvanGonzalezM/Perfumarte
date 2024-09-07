<script setup>
import Notification from '@/Components/Notification.vue';
import Alert from '@/Components/Alert.vue';

import Image from '@/Components/Image.vue';
</script>
<<<<<<< HEAD



<template>
    <div class="card card-login">
        <div class="login-logo">
=======
<template>
    <div class="card card-login align-items-center">
        <div class="over-card position-relative align-items-center d-flex flex-column">
>>>>>>> 842d2e2755172ca9d826ef2e6cf3f0d17538d224
            <Image src="assets\images\Logo_3.png" />
            <h3>Inicio de Sesión</h3>
        </div>
        <div class="card-body position-relative">
            <slot />
            <Notification />

            <Alert />
        </div>

    </div>

</template>
