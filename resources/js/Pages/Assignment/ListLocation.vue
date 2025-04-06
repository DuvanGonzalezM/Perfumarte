<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';


const props = defineProps({
    getSede: {
        type: Array,
    },
});
    
const columnsTable = [
    {
        data: 'name',
        title: 'SEDE'
    },
    {
        data: "location_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="' + route("assignment.Advisor", data) + '"><i class="fa-solid fa-eye"></i></a>';
        }
    },
    
];

</script>

<template>

    <Head title="Lista de sedes" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de sedes</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de sedes</strong>
            </template>
            <div class="container">
                <Table class="size-prais-5" :data="getSede" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>