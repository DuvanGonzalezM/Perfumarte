<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import Table from '@/Components/Table.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    currentInventory: Object,
});

const columnsTable = [
    {
        data: 'position',
        title: 'POSICIÓN',
        render: function (data, type, row) {
            return data ? data.toString().padStart(3, '0') : 'N/A';
        }
    },
    {
        data: 'product.commercial_reference',
        title: 'REFERENCIAS'
    },
    {
        data: 'product.category',
        title: 'GENERO'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity + ' ' + row.product.measurement_unit.replace('KG', 'ml');
        }
    },
];
</script>

<template>
    <Head title="Inventario" />
    <BaseLayout>
        <SectionCard>
            <template #headerSection>
                <strong>Inventario</strong>
            </template>
            
            <div class="container">
                <Table :data="props.currentInventory" :columns="columnsTable" :order="[[0, 'asc']]" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>
