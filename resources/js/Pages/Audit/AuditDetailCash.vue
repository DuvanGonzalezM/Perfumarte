<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    audit: {
        type: Object,
        required: true,
    },
    auditCash: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <Head :title="'Detalle de Auditoria Caja ' + audit.id_audits" />
    <BaseLayout>
        <template #header>
            <h1>Detalle de Auditoria Caja</h1>
        </template>
        <SectionCard :idSection="audit.id_audits" :subtitle="audit.location.name"
            :subextra="moment(audit.created_at).format('DD/MM/Y HH:mm')">
            <template #headerSection>
                <strong>Detalle de Auditoria Caja</strong>
            </template>
            <table class="table table-hover text-center dt-body-nowrap align-middle">
                <thead>
                    <tr>
                        <th>DINERO</th>
                        <th>CANTIDAD</th>
                        <th>CONFIRMACION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ 'Efectivo' }}</td>
                        <td>{{ auditCash.money_in_box.toLocaleString('es-CO', {
                            style: 'currency', currency: 'COP',
                            minimumFractionDigits: 0, maximumFractionDigits: 0 }) }}</td>
                        <td> <i v-if="auditCash.confirmation_cash === 1" class="fa-solid fa-circle-check"></i>
                            <i v-else class="fa-regular fa-circle"></i>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ 'Digital' }}</td>
                        <td>{{ auditCash.money_in_digital.toLocaleString('es-CO', {
                            style: 'currency', currency: 'COP',
                            minimumFractionDigits: 0, maximumFractionDigits: 0 }) }}</td>
                        <td> <i v-if="auditCash.confirmation_digital === 1" class="fa-solid fa-circle-check"></i>
                            <i v-else class="fa-regular fa-circle"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                <div class="col-8 p-1 cardboxprais cardpurcheorder ">
                    <h6>Observacion:</h6> {{ auditCash.observation }}
                </div>
            </div>

            <div class="row my-5 text-center">
                <div class="col">
                    <PrimaryButton :href="route('audits')" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
