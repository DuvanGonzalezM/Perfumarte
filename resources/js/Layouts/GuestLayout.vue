<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Notification from '@/Components/Notification.vue';
import Alert from '@/Components/Alert.vue';

import { Link } from '@inertiajs/vue3';
</script>



<template>
    <div class="card card-login">

        <Link href="/">
        <ApplicationLogo />
        </Link>


        <div class="card-body">
            <Notification />
            <Alert />

            <slot />
        </div>

    </div>

</template>
