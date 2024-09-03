<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="card card-login">
        <Link href="/">
            <ApplicationLogo />
        </Link>
        <div class="card-body">
            <slot />
        </div>
    </div>
</template>
