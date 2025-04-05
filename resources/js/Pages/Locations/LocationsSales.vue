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
    cashRegisters: {
        type: Array,
        required: true
    }
});

const columnsTable = [
    {
        data: 'cash_register_id',
        title: 'ID'
    },
    {
        data: 'created_at',
        title: 'Fecha'
    },
    
];

</script>

<template>
    <Head title="Ventas de Sede" />
    <BaseLayout>
        <template #header>
            <h1>Ventas de Sede</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ location.name }}</strong>
            </template>

            <div class="container">
                <Table :columns="columnsTable" :data="props.cashRegisters" />
            </div>
                    
            <div class="container">
                <PrimaryButton :href="route('locations.detail', location.location_id)" class="px-5">
                    Regresar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
