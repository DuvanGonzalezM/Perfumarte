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
    currentInventory: Object
});

const columnsTable = [
    {
        data: 'product.commercial_reference',
        title: 'REFERENCIAS'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity + ' ' + row.product.measurement_unit.replace('KG', 'ml');
        }
    },
    {
        data: 'product.category',
        title: 'GENERO'
    },
];
</script>

<template>
    <Head title="Detalle de Sede" />
    <BaseLayout>
        <template #header>
            <h1>Detalle de Sede</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ location.name }}</strong>
            </template>

            <div class="container">
                <Table :columns="columnsTable" :data="props.currentInventory" />
            </div>
                    
            <div class="container">
                <PrimaryButton :href="route('locations.detail', location.location_id)" class="px-5">
                    Regresar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
