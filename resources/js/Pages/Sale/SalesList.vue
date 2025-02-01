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
    {
        data: 'sale_id',
        title: 'CODIGO VENTA'
    },
    {
        data: 'user.name',
        title: 'SOLICITADO POR'
    },
    {
        data: "created_at",
        title: 'FECHA DE SOLICITUD',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
    {
        data: "sale_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="' + route("dispatch.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        },
    }
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