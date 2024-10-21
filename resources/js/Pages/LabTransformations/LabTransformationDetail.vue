<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    LabtransformationDetail: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'transformation_id',
        title: 'CODIGO TRANSFORMACION',
    },
    {
        data: 'inventory.product.reference',
        title: 'PRODUCTO'
    },
    {
        data: 'escence',
        title: 'CANTIDAD ESCENCIA',
    },
    {
        data: 'dipropylene',
        title: 'CANTIDAD DIPROPILENO',
    },
    {
        data: 'solvent',
        title: 'CANTIDAD DISOLVENTE',
    },
  
];

</script>

<template>

    <Head :title = "'Lista de transformaciones laboratorio' + transformation.transformation_id" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de transformaciones laboratorio</h1>
        </template>

        <SectionCard :idSection="transformation.tranformation_id" :subtitle="moment(transformation.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Lista de transformaciones laboratorio</strong>

            </template>
            <div class="container">
                <div class="container">
                    <PrimaryButton :href="route('LabTransformation.List')" class="position-absolute">
                        Volver
                    </PrimaryButton>
                </div>
                <Table class="size-prais-5" :data="LabtransformationDetail" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>