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
        title: 'ID '
    },
    {
    data: 'created_at',
    title: 'Fecha',
    render: function (data) {
        return new Date(data).toLocaleString('es-CO', {
            dateStyle: 'medium',
            timeStyle: 'short'
        });
    }
    },
    {
        data: (row) => row.total_collected - row.total_digital,
        title: 'Total'
    },
    {
    data: 'cash_register_id',
    title: 'Detalle',
    render: function (data, type, row) {
        return '<a href="' + route("locations.salesDetail", { 
            location_id: props.location.location_id, 
            cash_register_id: data 
        }) + '"><i class="fa-solid fa-eye"></i></a>';
    }
}
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
