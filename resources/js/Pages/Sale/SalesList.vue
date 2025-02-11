<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { can } from 'laravel-permission-to-vuejs';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    sales: {
        type: Array,
    },
});
const columnsTable = [
    // {
    //     data: 'sale_id',
    //     title: 'CODIGO VENTA'
    // },
    {
        data: 'user.name',
        title: 'VENDEDOR'
    },
    {
        data: "total",
        title: 'TOTAL VENTA',
        render: function (data) {
            return '$' + data;
        }
    },
    {
        data: "created_at",
        title: 'HORA DE LA VENTA',
        render: function (data) {
            const formattedDate = moment(data).format('hh:mm');
            return formattedDate;
        }
    },
    // {
    //     data: "sale_id",
    //     title: 'DETALLE',
    //     render: function (data) {
    //         return '<a href="' + route("dispatch.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
    //     },
    // }
];

</script>

<template>

    <Head title="Ventas" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Ventas</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Ventas</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('sales.create')" class="position-absolute"
                    v-if="can('Crear Ventas')">
                    Nueva venta
                </PrimaryButton>
                <Table class="size-prais-5" :data="sales" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>