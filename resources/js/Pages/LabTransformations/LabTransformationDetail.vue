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

    <Head :title="'Lista de transformaciones laboratorio' + LabtransformationDetail.transformation_id" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de transformaciones laboratorio</h1>
        </template>

        <SectionCard :idSection="LabtransformationDetail.transformation_id"
            :subtitle="moment(LabtransformationDetail.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Lista de transformaciones laboratorio</strong>

            </template>
            <div class="container">
                <div class="container">
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle mb-5">
                        <tbody id="productsList">
                            <tr>
                                <td>REFERENCIA: </td>
                                <td>
                                    {{ LabtransformationDetail.inventory.product.reference }}
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD ESCENCIA : </td>
                                <td>
                                    {{ LabtransformationDetail.escence }} ml
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DIPROPILENO : </td>
                                <td>
                                    {{ LabtransformationDetail.dipropylene }} ml
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DISOLVENTE : </td>
                                <td>
                                    {{ LabtransformationDetail.solvent }} ml
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD TOTAL : </td>
                                <td>
                                    {{ LabtransformationDetail.escence + LabtransformationDetail.dipropylene + LabtransformationDetail.solvent }} ml
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('LabTransformation.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>