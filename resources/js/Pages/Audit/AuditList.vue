<script setup>
import { ref } from 'vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import Notification from '@/Components/Notification.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    audits: {
        type: Array,
    },
    locations: {
        type: Array,
    }
});

const routes = {
    detailCash: '/auditoria/detalle auditoria caja/', // Asegúrate de que la ruta sea correcta
    detailInventory: '/auditoria/detalle auditoria inventario/',
};
const showModal = ref(false);
const optionTypeAudit = ref(props.audits.map(audit => ({ 'title': audit.type_audit, 'value': audit.type_audit })));
const optionLocation = ref(props.locations.map(location => ({ 'title': location.name, 'value': location.location_id })));

const openModal = () => {
    showModal.value = true;
    typeAuditSeleted.value = null;
    locationSeleted.value = null;
}
const columnsTable = [
    {
        data: 'id_audits',
        title: 'CODIGO AUDITORIA'
    },
    {
        data: "created_at",
        title: 'FECHA AUDITORIA',
        render: function (data) {
            return moment(data).format('DD/MM/YYYY HH:mm'); // Formatea la fecha usando Moment.js
        },
    },
    {
        data: "user.location.name",
        title: 'SEDE',
    },
    {
        data: "user.name",
        title: 'AUDITOR',
    },
    {
        data: "type_audit",
        title: 'TIPO DE AUDITORIA',
        render: function (data) {
            if (data === '1') {
                return 'Arqueo';
            } else {
                return 'Inventario';
            }
        },
    },
    {
    data: "id_audits",
    title: 'DETALLE',
    render: function (data, type, row) {
        let route;

        switch (row.type_audit) {
            case '1':
                route = routes.detailCash + data; // Ruta para auditoría de caja
                break;
            case '2':
                route = routes.detailInventory + data; // Ruta para auditoría de inventario
                break;
            default:
                route = '#'; // Ruta por defecto si no coincide
        }

        return '<a href="' + route + '"><i class="fa-solid fa-eye"></i></a>';
    },
}

];

</script>
<template>

    <Head title="Auditorias" />
    <BaseLayout>
        <template #header>

            <h1>Auditorias</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Auditorias</strong>
            </template>
            <div class="container">
                <PrimaryButton @click="openModal" class="position-absolute">
                    Nueva Auditoria
                </PrimaryButton>
                <Table class="size-prais-5" :data="audits" :columns="columnsTable" />
            </div>
            <ModalPrais v-model="showModal" @close="showModal = false">
                <template #header>
                    Realizar Auditoria
                </template>
                <template #body>
                    <SelectSearch :options="optionTypeAudit" class="my-4" v-model="typeAuditSeleted"
                        labelValue="Selecciona un tipo de auditoria" />
                    <SelectSearch :options="optionLocation" class="my-4" v-model="locationSeleted"
                        labelValue="Selecciona una sede" />
                </template>
                <template #footer>
                    <PrimaryButton @click="addAudit" class="px-5">
                        Agregar
                    </PrimaryButton>
                </template>
            </ModalPrais>
        </SectionCard>
    </BaseLayout>
</template>