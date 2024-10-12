<script setup>
import Notification from '@/Components/Notification.vue';
import Alert from '@/Components/Alert.vue';

import Image from '@/Components/Image.vue';
</script>
<template>
    <div class="container body-login">
        <div class="card card-login align-items-center">
            <div class="over-card position-relative align-items-center d-flex flex-column">
                <Image src="assets\images\Logo_3.png" />
                <h3>Inicio de Sesión</h3>
            </div>
            <div class="card-body position-relative">
                <slot />
            </div>
        </div>
    </div>
</template>
