<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    auditInventoryDetail: {
        type: Object,
        required: true,
    },
});

const columnsTable = [

    {
        data: 'inventory.product.commercial_reference',
        title: 'PRODUCTO'
    },
    {
        data: 'quantity_system',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity_system + ' ' + row.inventory.product.measurement_unit.replace('KG', 'ml').replace('UNIDAD', 'unidad(es)');
        }
    },
    {
        data: 'confirmation_inventory' ,
        title: 'CANTIDAD CONFIRMADA',
        render: function (data, type, row) {
            return data ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-regular fa-circle"></i>';
        }, 
    },
    {
        data: 'observation',
        title: 'OBSERVACIONES',
    },

];

</script>

<template>

    <Head :title="'Detalle Auditoria de Inventario'" />

    <BaseLayout>
        <template #header>
            
            <h1>Detalle de Auditoria inventario</h1>
        </template>

        <SectionCard :idSection="props.auditInventoryDetail.id_audits" :subtitle="props.auditInventoryDetail.location ? props.auditInventoryDetail.location.name : 'Ubicación no disponible'"
            :subextra="moment(props.auditInventoryDetail.created_at).format('DD/MM/Y HH:mm')">
            <template #headerSection>
                <strong>Detalle de Auditoria inventario</strong>

            </template>
                <div class="container">
                    <Table :data="props.auditInventoryDetail.audit_inventory" :columns="columnsTable" />
                    <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('audits')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
                </div>
        </SectionCard>
    </BaseLayout>
</template>