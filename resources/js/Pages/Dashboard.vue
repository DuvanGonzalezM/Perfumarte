<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Inicio" />

    <BaseLayout>
        <template #header>
            <h1>Inicio</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ $page.props.auth.user.username }}</strong>
            </template>
            <div class="container">
                <div>Hola, Iniciaste Sesión</div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>