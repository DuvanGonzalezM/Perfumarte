<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    requestPrais: {
        type: Object,
        required: true,
    },
    details: {
        type: Array,
        required: true,
    },
});

const columnsTable = [
    {
        data: 'inventory.product.0.reference',
        title: 'REFERENCIA / INSUMO',
    },
    {
        data: 'quantity',
        title: 'CANTIDAD'
    },
];

</script>

<template>

    <Head title="Detalle de Solicitud" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de Solicitud</h1>
        </template>

        <SectionCard :idSection="requestPrais.request_id" :subtitle="moment(requestPrais.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle de Solicitud</strong>
            </template>
            <div class="container">
                <div class="col-6 p-2 cardboxprais cardpurcheorder">
                    <strong>Sede: </strong>
                    <span>{{ props.requestPrais.user?.location?.name || 'No disponible' }}</span><br>
                    <strong>Usuario: </strong>
                    <span>{{ props.requestPrais.user?.username || 'No disponible' }}</span><br>
                    <strong>Fecha: </strong>
                    <span>{{ moment(props.requestPrais.created_at).format('DD/MM/Y') }}</span>
                </div>
            </div>
            <Table :data="details" :columns="columnsTable" />
            <div class="row my-5 text-center">
                <div class="col">
                    <PrimaryButton :href="route('suppliesrequest.list')" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>

        </SectionCard>
    </BaseLayout>
</template>