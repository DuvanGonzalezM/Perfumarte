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
        data: 'name',
        title: 'NOMBRE'
    },
    {
        data: 'roles',
        title: 'ROLES',
        render: function (data) {
            return data.map(role => role.name).join(', ');
        },
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
            {{ console.log(props.users) }}
            <div class="container">
                <Table :columns="columnsTable" :data="props.users" />
            </div>
                    
            <div class="row my-5 text-center">
                <div class="container">
                    <PrimaryButton :href="route('locations.detail', location.location_id)" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
