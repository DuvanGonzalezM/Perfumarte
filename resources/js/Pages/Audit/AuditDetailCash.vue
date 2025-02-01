<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import moment from 'moment';

const props = defineProps({
    audits: {
        type: Object,
        required: true,
    },
});
</script>
<template>

    <Head :title="'Detalle de Auditoria Caja ' + audits.id_audits" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de Auditoria Caja</h1>
        </template>

        <SectionCard :idSection="audits.id_audits" :subtitle="audits.type_audit === '1' ? 'Arqueo' : 'Inventario'"
            :subextra="moment(audits.created_at).format('DD/MM/Y HH:mm')">
            <template #headerSection>
                <strong>Detalle de Auditoria Caja</strong>
            </template>

            <div class="col mb-2">
                <div class="col-12 p-3 cardboxprais cardpurcheorder text-center">
                    <h5>Fragancias Vendidas</h5>
                </div>
            </div>
            <table class="table table-hover text-center dt-body-nowrap align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad 30 ML</th>
                        <th>Cantidad 50 ML</th>
                        <th>Cantidad 100 ML</th>
                        <th>Total Efectivo</th>
                        <th>Total Digital</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in audits.audit_detail_cash">
                        <td>{{ item.name }}</td>
                        <td>{{ item.quantity_30ml }}</td>
                        <td>{{ item.quantity_50ml }}</td>
                        <td>{{ item.quantity_100ml }}</td>
                        <td>{{ item.total_cash }}</td>
                        <td>{{ item.total_digital }}</td>
                    </tr>
                </tbody>
            </table>

            <div>
                <div class="col mb-2">
                    <div class="col-12 p-3 cardboxprais cardpurcheorder text-center">
                        <h5>Dinero Reportado</h5>
                    </div>
                </div>
                <table class="table table-hover text-center dt-body-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Total Efectivo</th>
                            <th>Total Digital</th>
                            <th>Total General</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ }}</td>
                            <td>{{ }}</td>
                            <td>{{ }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('audits')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
