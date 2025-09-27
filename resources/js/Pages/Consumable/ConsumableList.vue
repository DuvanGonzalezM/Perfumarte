<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    consumable: {
        type: Array,
    },
});

const columnsTable = [

    {
        data: "created_at",
        title: 'FECHA DE REGISTRO',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
    {
        data: 'warehouse.location.name',
        title: 'SEDE',
        render: function (data, type, row) {
            return row.warehouse?.location?.name ?? 'N/A';
        }
    },
    {
        data: 'inventory.product.commercial_reference',
        title: 'PRODUCTO'
    },

    {
        data: "consumable_quantity",
        title: 'CANTIDAD',
    },
    {
        data: "user.name",
        title: 'USUARIO',
    },
   
];

</script>


<template>

    <Head title="Consumibles" />
    <BaseLayout>
        <template #header>

            <h1>Consumibles</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Consumibles</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('consumable.create')" class="position-absolute"
                    v-if="can('Crear Consumibles')">
                    Nuevo Registro
                </PrimaryButton>
                <Table class="size-prais-5" :data="consumable" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>