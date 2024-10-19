<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    getLabTransformation: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'transformation_id',
        title: 'CODIGO TRANSFORMACION'
    },
    {
        data: 'inventory.product.reference',
        title: 'PRODUCTO'
    },
    {
        data: 'quantity',
        title: 'CANTIDAD',
    },
    {
        data: "created_at",
        title: 'FECHA DE TRANSFORMACION',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
];

</script>

<template>

    <Head title="Lista de transformaciones laboratorio" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de transformaciones laboratorio</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de transformaciones laboratorio</strong>

            </template>
            <div class="container">
                <div class="container">
                    <PrimaryButton :href="route('LabTransformation.create')" class="position-absolute">
                        Nueva transformacion
                    </PrimaryButton>
                </div>
                <Table class="size-prais-5" :data="getLabTransformation" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>