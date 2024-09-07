<script setup>
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/InformationCard.vue';
import ButtonSidebar from '@/Components/ButtonSidebar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Image from '@/Components/Image.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="card">
        <div class="login-logo">
            <!--<Image src="assets\images\Logo_3.png" />-->
            <h3>Inicio de Sesión</h3>
        </div>
        <div class="card-body">
            <slot />

        </div>
        <Link href="/">
        <InformationCard />
        <CardButton />
        <ButtonSidebar />
        <Sidebar />
        </Link>
    </div>
</template>
