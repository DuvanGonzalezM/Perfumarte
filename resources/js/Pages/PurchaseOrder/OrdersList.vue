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
        purchaseOrders: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'purchase_order_id',
        title: 'CODIGO'
    },
    {
        data: 'product_entry_order.0.product.supplier.name',
        title: 'PROVEEDOR'
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
        data: "purchase_order_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="'+ route("orders.detail", data) +'"><i class="fa-solid fa-eye"></i></a>';
        }
    },
];

</script>

<template>

    <Head title="Ordenes de compra" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Ordenes de compra</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Ordenes de compra</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('orders.create')" class="position-absolute">
                    Nuevo registro
                </PrimaryButton>
                <Table :data="purchaseOrders" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>