<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    dispatch: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'dispatch_id',
        title: 'CODIGO DESPACHO'
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
        data: 'status',
        title: 'ESTADO'
    },

    // {
    //     data: "dispatch_id",
    //     title: 'DETALLE',
    //     render: function (data) {
    //         return '<a href="' + route("dispatch.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
    //     }
   
];

</script>

<template>

    <Head title="DEPACHOS" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Despachos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Despachos</strong>
            </template>
            <div class="container">
                <Table class="size-prais-5" :data="dispatch" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>