<script setup>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';

const props = defineProps({
    location: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        required: true
    }
});

const columnsTable = [
    {
        data: 'username',
        title: 'USUARIO'
    },
    {
        data: 'name',
        title: 'NOMBRE'
    },
];
</script>

<template>
    <Head title="Personal de Sede" />
    <BaseLayout>
        <template #header>
            <h1>Personal de Sede</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ location.name }}</strong>
            </template>

            <div class="container">
                <Table :columns="columnsTable" :data="props.users" />
            </div>
                    
            <div class="container">
                <PrimaryButton :href="route('locations.detail', location.location_id)" class="px-5">
                    Regresar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
