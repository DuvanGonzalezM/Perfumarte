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
        data: 'inventory[0].product[0].reference',
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

        <SectionCard>
            <template #headerSection>
                <strong>Detalle de Solicitud</strong>
            </template>
            <div class="container">
                <div class="cardboxprais row" >
                    <div class="col-md-4 py-3">
                        <strong>Sede</strong><br>
                        <span>{{ props.requestPrais.user?.location?.name || 'No disponible' }}</span>
                    </div>
                    <div class="col-md-4 py-3">
                        <strong>Usuario</strong><br>
                        <span>{{ props.requestPrais.user?.username || 'No disponible' }}</span>
                    </div>
                    <div class="col-md-4 py-3">
                        <strong>Fecha</strong><br>
                        <span>{{ moment(props.requestPrais.created_at).format('DD/MM/Y')
                            }}</span>
                    </div>
                </div>
                <Table :data="details" :columns="columnsTable" />
            </div>
            <PrimaryButton :href="route('suppliesrequest.list')" style="margin-right: 20px;">
                Volver
            </PrimaryButton>
        </SectionCard>
    </BaseLayout>
</template>