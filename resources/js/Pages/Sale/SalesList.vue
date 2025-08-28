<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { can } from 'laravel-permission-to-vuejs';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    sales: {
        type: Array,
    },
    confirmationclosingcash: {
        type: Object,
    }
});

const isCashClosed = () => {
    return props.confirmationclosingcash == "1";
};
const columnsTable = [
{
        data: "created_at",
        title: 'HORA DE LA VENTA',
        render: function (data) {
            const formattedDate = moment(data).format('hh:mm');
            return formattedDate;
        }
    },
    {
        data: 'user.name',
        title: 'VENDEDOR'
    },
    {
        data: "payment_method",
        title: 'METODO DE PAGO'
    },
    {
        data: "total",
        title: 'TOTAL VENTA',
        render: function (data, type, row) {
            return new Intl.NumberFormat('es-CO', { style:'currency',currency:'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(data);
        }
    },

    {
        data: "sale_id",
        title: 'DETALLE',
        render: function (data) {
            return '<a href="' + route("sales.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        },
    }
];

</script>

<template>

    <Head title="Ventas" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
        </template>

        <SectionCard :subextra="'Total de ventas: ' + new Intl.NumberFormat('es-CO', { style:'currency',currency:'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(sales.reduce((acc, sale) => acc + Number(sale.total), 0))">
            <template #headerSection>
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Ventas</strong>
                </div>
            </template>
            <div class="container">
                <div class="mb-4">
                    <span class="text-muted">Estado de la caja:</span>
                    <span :class="'badge ms-2 ' + (isCashClosed() ? 'bg-danger' : 'bg-success')">
                        {{ isCashClosed() ? 'Cerrada' : 'Abierta' }}
                    </span>
                </div>
                <PrimaryButton :href="route('sales.create')" :class="isCashClosed() ? 'disabled' : ''" class="position-absolute">
                    Nueva venta
                </PrimaryButton>
            </div>
            <Table class="size-prais-5" :data="sales" :columns="columnsTable" />
            <div class="row my-5 text-center">
                <div class="col-12">
                    <PrimaryButton :href="route('cash_register.close')" :class="isCashClosed() ? 'disabled' : ''">
                        Cerrar Caja
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
