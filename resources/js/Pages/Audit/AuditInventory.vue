<script setup>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';


const props = defineProps({
    dispatch: {
        type: Array,
    },
});


</script>
<template>

    <Head title="Auditoria de Inventario" />
    <BaseLayout>
        <template #header>
            <h1>Auditoria de Inventario</h1>
        </template>
    </BaseLayout>
</template>