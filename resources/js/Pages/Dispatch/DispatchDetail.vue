<script setup>
import Alert from '@/Components/Alert.vue';
import Notification from '@/Components/Notification.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    detaildispatch: {
        type: Object,
        required: true,
    },
});

const columnsTable = [
    {
        data: 'inventory.product.reference',
        title: 'REFERENCIA'
    },
    {
        data: 'inventory.quantity',
        title: 'CANTIDAD DESPACHADA',
        render: function (data, type, row) {
            return row.dispatched_quantity + ' ' + row.inventory.product.measurement_unit.replace('KG', 'ml');
        }
    },

];

</script>

<template>

    <Head :title="'Detalle del despacho ' + detaildispatch.dispatch_id" />

    <BaseLayout>
        <template #header>
            <h1>Detalle del despacho</h1>
        </template>

        <SectionCard :idSection="detaildispatch.dispatch_id"
            :subtitle="moment(detaildispatch.dispatch.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle del despacho</strong>
            </template>

            <div class="container">
                <div class="row">
                    <div class="col-6 p-2 cardboxprais cardpurcheorder">
                        <strong>Sede: </strong>
                        <span>{{ detaildispatch.inventory.warehouse.location.name || 'No disponible' }}</span><br>
                        <strong>Fecha del Despacho: </strong>
                        <span>{{ moment(detaildispatch.dispatch.created_at).format('DD/MM/Y') }}</span><br>
                        <strong>Estado del Despacho: </strong>
                        <span>{{ detaildispatch.dispatch.status }}</span><br>
                    </div>
                </div>

                <Table class="size-prais-5" :data="detaildispatch" :columns="columnsTable" />

                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('dispatch.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>