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
    detailRequest: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: '',
        title: 'REFERENCIA / INSUMO'
    },
    {
        data: '',
        title: 'CANTIDAD'
    },
    {
        data: "created_at",
        title: 'Fecha de registro',
        render: function (data) {
            const formattedDate = moment(data).format('DD/MM/Y');
            return formattedDate;
        }
    },

];

</script>

<template>

    <Head title="Detalle de Solicitud" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Detalle de Solicitud</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Detalle de Solicitud</strong>
            </template>
            <div class="container">

                <Table :data="detailRequest" :columns="columnsTable" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>