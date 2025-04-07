<script setup>
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/informationCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    inventory: {
        type: Array,
    },
    warehouse: {
        type: Object,
    },
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
        data: 'product.supplier.name',
        title: 'PROVEEDOR'
    },
];
</script>
<template>

    <Head :title="warehouse.name" />

    <BaseLayout>
        <template #header>
            <h1>{{ props.warehouse.name }}</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Stock {{ props.warehouse.name }}</strong>
            </template>
            <div class="container">
                <Table :data="inventory" :columns="columnsTable" />
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('stock.dashboard')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>

</template>