<script setup>
import Alert from '@/Components/Alert.vue';
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
            return row.quantity + ' ' + row.product.measurement_unit.replace('KG', 'ml');
        }
    },
    {
        data: 'batch',
        title: 'N° LOTE'
    },
];

</script>

<template>

    <Head :title="'Orden de compra ' + purchaseOrder.purchase_order_id" />

    <BaseLayout>
        <template #header>
            <h1>Orden de compra</h1>
        </template>

        <SectionCard :idSection="purchaseOrder.purchase_order_id" :subtitle="moment(purchaseOrder.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Orden de compra</strong>
            </template>
            <div class="detail-container">
                <div class="supplier-info">
                    <div class="info-card">
                        <strong>Proveedor: </strong>
                        <span>{{ props.purchaseOrder.product_entry_order[0].product.supplier.name }}</span><br>
                        <strong>Nit: </strong>
                        <span>{{ props.purchaseOrder.product_entry_order[0].product.supplier.nit }}</span><br>
                        <strong>Pais: </strong>
                        <span>{{ props.purchaseOrder.product_entry_order[0].product.supplier.country }}</span><br>
                        <strong>Contacto: </strong>
                        <a :href="'mailto:'+purchaseOrder.product_entry_order[0].product.supplier.email">{{ props.purchaseOrder.product_entry_order[0].product.supplier.email }}</a><br>
                        <strong>Orden de compra: </strong>
                        <span>{{ props.purchaseOrder.supplier_order }}</span><br>
                    </div>
                </div>
                <Table :data="purchaseOrder.product_entry_order" :columns="columnsTable" />
                <div class="actions-container">
                    <PrimaryButton :href="route('orders.list')" class="action-button">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>