<script setup>
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/informationCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    warehouses: {
        type: Array,
    },
});

const icons = [
        'fa-warehouse',
        'fa-solid fa-box',
        'fa-box-archive',
        'fa-boxes-stacked',
];

</script>
<template>

    <Head title="Stock" />

    <BaseLayout>
        <template #header>
            <h1>Stock</h1>
        </template>
        <div class="row">
            <div class="col-6 p-5" v-for="(warehouse, index) in warehouses">
                <CardButton :link="route('stock.inventory', warehouse.warehouse_id)" :title="warehouse.name" :icon="icons[index > 3 ? 3 : index]" />
            </div>
            <div class="col-6 p-5">
                <CardButton :link="route('stock.multiple')" title="Sedes" :icon="icons[3]" />
            </div>
        </div>
    </BaseLayout>

</template>