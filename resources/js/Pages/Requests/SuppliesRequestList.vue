<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    suppliesRequest: {
        type: Array,
    },
});
    
const columnsTable = [
    {
        data: 'request_id',
        title: 'CODIGO SOLICITUD'
    },
    {
        data: 'user.username',
        title: 'SOLICITADO POR'
    },
    {
        data: 'user.location.name',
        title: 'SEDE'
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
        data: "request_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="' + route("suppliesrequest.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        }
    },
];

</script>

<template>

    <Head title="Solicitud de Insumos" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Solicitud Insumos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Solicitud Insumos</strong>
            </template>
            <div class="container">
                <Table class="size-prais-5" :data="suppliesRequest" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>