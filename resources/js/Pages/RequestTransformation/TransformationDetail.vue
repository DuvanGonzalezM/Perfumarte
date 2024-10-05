<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    transformationRequest: {
        type: Object,
        required: true,
    },
   
});

const columnsTable = [
    {
        data: 'inventory.product.reference',
        title: 'REFERENCIA',
    },
    {
        data: 'quantity',
        title: 'CANTIDAD'
    },
];

</script>

<template>

    <Head title="Detalle de Transformacion" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de Transformacion</h1>
        </template>

        <SectionCard :idSection="transformationRequest.request_id" :subtitle="moment(transformationRequest.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle de Transformacion</strong>
            </template>
            <div class="container">
                <div class="cardboxprais row mb-5">
                    <div class="col-4 py-3">
                        <strong>Sede</strong><br>
                        <span>{{ props.transformationRequest.user?.location?.name || 'No disponible' }}</span>
                    </div>
                    <div class="col-4 py-3">
                        <strong>Usuario</strong><br>
                        <span>{{ props.transformationRequest.user?.username || 'No disponible' }}</span>
                    </div>
                    <div class="col-4 py-3">
                        <strong>Fecha</strong><br>
                        <span>{{ moment(props.transformationRequest.created_at).format('DD/MM/Y') }}</span>
                    </div>
                </div>
                <Table :data="transformationRequest.detail_request" :columns="columnsTable" />
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('transformationRequest.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>