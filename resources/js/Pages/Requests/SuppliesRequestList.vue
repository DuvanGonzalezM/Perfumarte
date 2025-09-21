<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { can } from 'laravel-permission-to-vuejs';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
        data: "created_at",
        title: 'FECHA DE SOLICITUD',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
    {
        data: 'user.name',
        title: 'SOLICITADO POR'
    },
    // {
    //     data: 'user.location.name',
    //     title: 'SEDE'
    // },

    {
        data: "status",
        title: 'ESTADO',
        render: (data) => {
            switch (data) {
                case 'pending_approval':
                    return '<span class="text-warning">Pendiente de Aprobación</span>';
                case 'approved':
                    return '<span class="text-success">Aprobada</span>';
                case 'rejected':
                    return '<span class="text-danger">Rechazada</span>';
                default:
                    return data;
            }
        }
    },
    {
        data: "request_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="' + (can('Editar Solicitudes Insumos') ? route("requests.detail", data) : route("suppliesrequest.detail", data)) + '"><i class="fa-solid fa-eye"></i></a>';
        }
    },
];

</script>

<template>

    <Head title="Solicitud de Insumos" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Solicitud Insumos</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('suppliesrequest.store')" class="position-absolute"
                    v-if="can('Crear Solicitudes Insumos')">
                    Nuevo Registro
                </PrimaryButton>
                <Table class="size-prais-5" :data="suppliesRequest" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>