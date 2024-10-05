<script setup>
import CardButton from '@/Components/CardButton.vue';
import InformationCard from '@/Components/informationCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    locations: {
        type: Array,
    },
});

const location = ref(null);
const inventory = ref([]);
const showTable = ref(false);
const warehouse = ref(null);
const locationSelect = [];
const locationFor = ref(props.locations.map((location) => location.warehouses.map((warehouse) => locationSelect.push({ 'title': warehouse.name + ' - ' + location.name, 'value': warehouse.warehouse_id }))));

const selectedWarehouse = async () => {
    try {
        const response = await axios.get(route('api.warehouse', location.value));
        inventory.value = response.data['inventory'];
        warehouse.value = response.data['warehouse'];
        showTable.value = true;
        console.log(warehouse.value.name);
        
    } catch (error) {
        showTable.value = false;
    }
}

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

    <Head :title="warehouse ? warehouse.name :  'Sedes'" />

    <BaseLayout>
        <template #header>
            <h1>{{ warehouse ? warehouse.name :  'Sedes' }}</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Stock {{ warehouse ? warehouse.name :  'Sedes' }}</strong>
            </template>
            <div class="container">
                <SelectSearch v-model="location" :options="locationSelect" :changeFunction="selectedWarehouse"
                    labelValue="Bodega" />
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