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
        data: 'product.reference',
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
    <Head title="Inventario" />
    <BaseLayout>
        <SectionCard>
            <template #headerSection>
                <strong>Inventario</strong>
            </template>
            
            <div class="container">
                <Table :data="props.currentInventory" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>
