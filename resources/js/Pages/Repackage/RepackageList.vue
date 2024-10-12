<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    repackageList: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'change_warehouse_id',
        title: 'CODIGO REENVASE'
    },
    {
        data: 'inventory.product.reference',
        title: 'PRODUCTO'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity + ' ' + ('ml');
        }
    },
    {
        data: "created_at",
        title: 'FECHA DE REENVASE',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
];

</script>

<template>

    <Head title="Lista de reenvase" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de reenvase</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de reenvase</strong>

            </template>
            <div class="container">
                <div class="container">
                    <PrimaryButton :href="route('create.repackage')" class="position-absolute" v-if="can('Crear Reenvases')">
                        Nuevo Reenvase
                    </PrimaryButton>
                </div>
                <Table class="size-prais-5" :data="repackageList" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>