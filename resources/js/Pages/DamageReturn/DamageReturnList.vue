<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    damageReturn: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'damage_return_id',
        title: 'CODIGO DEVOLUCION'
    },
    {
        data: "created_at",
        title: 'FECHA DE REGISTRO',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
    {
        data: 'damage_return_id',
        title: 'SEDE',
        visible: can('Confirmar Devoluciones'),
        render: function (data, type, row) {
            return row.damage_return_detail && row.damage_return_detail.length > 0
                ? row.damage_return_detail[0].warehouse.location.name
                : 'N/A';
        }
    },
    {
        data: 'status',
        title: 'ESTADO'
    },

    {
        data: "damage_return_id",
        title: 'DETALLE',
        render: function (data) {
            return `<a href="${route('damageReturn.detail', data)}">
                    <i class="fa-solid fa-eye"></i>
                </a>`;
        },
    },
];

</script>


<template>

    <Head title="Devoluciones" />
    <BaseLayout>
        <template #header>

            <h1>Devoluciones</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Devoluciones</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('damageReturn.create')" class="position-absolute"
                    v-if="can('Crear Devoluciones')">
                    Nueva Devolución
                </PrimaryButton>
                <Table class="size-prais-5" :data="damageReturn" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>