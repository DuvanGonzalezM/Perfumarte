<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/informationCard.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="card card-login">
        <Link href="/">
            <ApplicationLogo />
            <InformationCard />
            <CardButton />
        </Link>
        <div class="card-body">
            <slot />
        </div>
    </div>
</template>
