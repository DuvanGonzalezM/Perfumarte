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
        purchaseOrder: {
        type: Object,
    },
});

const columnsTable = [
    {
        data: 'product.reference',
        title: 'REFERENCIA'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return row.quantity + ' ' + row.product.measurement_unit;
        }
    },
    {
        data: 'batch',
        title: 'N° LOTE'
    },
];

</script>

<template>

    <Head title="Ordenes de compra" />

    <BaseLayout>
        <template #header>
            <h1>Ordenes de compra</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Ordenes de compra</strong>
            </template>
            <div class="container">
                <Table :data="purchaseOrder.product_entry_order" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>