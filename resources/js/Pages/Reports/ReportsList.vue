<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';
import ModalPrais from '@/Components/ModalPrais.vue';
import InputCalendar from '@/Components/InputCalendar.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import { ref } from 'vue';

const props = defineProps({
    reports: {
        type: Array,
    },
    warehouses: {
        type: Array,
    },
});
const form = useForm({
    type_report: '1',
    warehouse_id: '1',
    range_date: [new Date(), new Date()],
});
const optionWarehouse = ref([
    { 'title': 'Todas las sedes', 'value': '1' },
    ...props.warehouses.map(warehouse => ({ 'title': warehouse.name, 'value': warehouse.warehouse_id }))
]);
const progress = ref(false);
const showModal = ref(false);
const openModal = () => {
    showModal.value = true;
    form.type_report = '1';
    form.warehouse_id = '1';
    form.range_date = [new Date(), new Date()];
}
const optionsTypeReport = [
    { 'title': 'Total vendido', 'value': '1' },
    { 'title': 'Top 10 Fragancias', 'value': '2' },
    { 'title': 'Top 10 Fragancias con menor venta', 'value': '3' },
    { 'title': 'Picos altos de venta por hora y sucursal', 'value': '4' },
    { 'title': 'Top 10 Sucursales con mayor venta', 'value': '5' },
];
const columnsTable = [
    {
        data: 'report_id',
        title: 'CODIGO REPORTE'
    },
    {
        data: 'type_report',
        title: 'TIPO DE REPORTE'
    },
    {
        data: "start_date_report",
        title: 'FECHA DE INICIO'
    },
    {
        data: "end_date_report",
        title: 'FECHA DE FIN'
    },
    {
        data: "created_at",
        title: 'FECHA DE GENERACION DEL REPORTE',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y HH:mm');
            return formattedDate;
        }
    },
];

const submit = () => {
    progress.value = true;
    window.location.href = route('generate.report', {
        typeReport: form.type_report,
        range_date: [moment(form.range_date[0]).format('YYYY-MM-DD HH:mm:ss'), moment(form.range_date[1]).format('YYYY-MM-DD HH:mm:ss')],
        warehouseIds: form.warehouse_id
    });
    setTimeout(() => {
        window.location.reload();
    }, 2000);
}
</script>

<template>

    <Head title="Lista de reportes" />

    <BaseLayout :loading="progress">
        <template #header>
            <!-- <Alert /> -->
            <h1>Lista de reportes</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de reportes</strong>
            </template>
            <div class="container">
                <div class="container"> 
                    <PrimaryButton @click="openModal" class="position-absolute" v-if="can('Crear Reporte')">
                        Nuevo reporte
                    </PrimaryButton>
                </div>
                <Table class="size-prais-6" :data="reports" :columns="columnsTable" />
                
                <ModalPrais v-model="showModal" @close="showModal = false">
                    <template #header>
                        Nuevo reporte
                    </template>
                    <template #body>
                        <form @submit.prevent="submit" target="_blank">
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <SelectSearch v-model="form.type_report" :options="optionsTypeReport" labelValue="Tipo de reporte" />
                                </div>
                                <div class="col-md-12 mb-5" v-if="form.type_report !== '5'">
                                    <SelectSearch v-model="form.warehouse_id" :options="optionWarehouse" multiple labelValue="Sede" />
                                </div>
                                <div class="col-md-12">
                                    <InputCalendar v-model="form.range_date" rangeEnabled labelValue="Fecha" id="range_date"/>
                                </div>
                            </div>
                        </form>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="submit" class="px-5" :disabled="form.processing || !form.type_report || form.warehouse_id.length === 0 || !form.range_date">
                            Guardar
                        </PrimaryButton>
                    </template>
                </ModalPrais>
            </div>
        </SectionCard>
    </BaseLayout>
</template>