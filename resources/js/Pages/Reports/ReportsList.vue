<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';
import TextInput from '@/Components/TextInput.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import { ref } from 'vue';

const props = defineProps({
    reports: {
        type: Array,
    },
});
const form = useForm({
    type_report: '',
    start_date_report: null,
    end_date_report: null,
});
const showModal = ref(false);
const openModal = () => {
    showModal.value = true;
    form.type_report = '';
    form.start_date_report = null;
    form.end_date_report = null;
}
const optionsTypeReport = [{ 'title': 'Reporte de venta por fragancia', 'value': 'Reporte de venta por fragancia' }, { 'title': 'Reporte de venta por punto', 'value': 'Reporte de venta por punto' }];
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
        title: 'FECHA DE INICIO',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },
    {
        data: "end_date_report",
        title: 'FECHA DE FIN',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
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
    form.post(route('store.report'));
}
</script>

<template>

    <Head title="Lista de reportes" />

    <BaseLayout :loading="form.processing ? true : false">
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
                    <PrimaryButton :href="route('download.report')" class="position-absolute" v-if="can('Crear Reporte')">
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
                                <div class="col-md-6">
                                    <TextInput type="date" name="start_date_report" id="start_date_report" v-model="form.start_date_report" labelValue="Fecha de inicio de reporte" :focus="true" />
                                </div>
                                <div class="col-md-6">
                                    <TextInput type="date" name="end_date_report" id="end_date_report" v-model="form.end_date_report" labelValue="Fecha de fin de reporte" :focus="true" />
                                </div>
                            </div>
                        </form>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="submit" class="px-5">
                            Guardar
                        </PrimaryButton>
                    </template>
                </ModalPrais>
            </div>
        </SectionCard>
    </BaseLayout>
</template>