<script setup>
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/informationCard.vue';
import Image from '@/Components/Image.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="card card-login">
        <Link href="/">
            <InformationCard />
            <CardButton />
        </Link>
        <div class="login-logo">
            <Image  src="assets\images\Logo_3.png" />
            <h3>Inicio de Sesión</h3>
        </div>
        <div class="card-body">
            <slot />
        </div>
    </div>
</template>
