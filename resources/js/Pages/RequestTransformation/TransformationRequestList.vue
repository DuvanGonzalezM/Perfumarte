<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    transformationRequest: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'request_id',
        title: 'CODIGO SOLICITUD'
    },
    {
        data: 'status',
        title: 'ESTADO'
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
            return '<a href="' + route("transformation.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        }
    },
];

</script>

<template>

    <Head title="Solicitud Transformaciones" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Solicitud Transformaciones</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Solicitud Transformaciones</strong>

            </template>
            <div class="container">
                <PrimaryButton :href="route('transformation.create')" class="position-absolute"
                    v-if="can('Crear Solicitudes Transformacion')">
                    Nuevo Registro
                </PrimaryButton>
                <Table class="size-prais-5" :data="transformationRequest" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>