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

// const groupedDispatches = computed(() => {
//     const dispatches = {};
//     props.dispatch.dispatchdetail.forEach(item => {
//         const warehouseName = item.warehouse.location.name || 'No disponible';
//         if (!dispatches[warehouseName]) {
//             dispatches[warehouseName] = [];
//         }
//         dispatches[warehouseName].push(item);
//     });
//     return dispatches;
// });
</script>

<template>

    <Head :title="'Detalle de Auditoria Inventario' + audits.id_audits" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de Auditoria Inventario</h1>
        </template>

        <SectionCard :idSection="audits.id_audits" :subtitle="audits.type_audit === '1' ? 'Arqueo' : 'Inventario'"
            :subextra="moment(audits.created_at).format('DD/MM/Y HH:mm')">
            <template #headerSection>
                <strong>Detalle de Auditoria Inventario</strong>
            </template>

            <!-- <div class="container">
                <div v-for="(items, warehouseName) in groupedDispatches" :key="warehouseName" class="my-4">
                    <div class="row">
                        <div class="col mb-2">
                            <div class="col-12 p-3 cardboxprais cardpurcheorder position-relative">
                                <h6>Sede: {{ warehouseName }}</h6>
                            </div>
                        </div>
                        <table class="table table-hover text-center dt-body-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>Referencia</th>
                                    <th>Cantidad Despachada</th>
                                    <th>Recibido</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in items" :key="index">
                                    <td>{{ item.inventory.product.reference }}</td>
                                    <td>{{ item.dispatched_quantity }} {{
                                        item.inventory.product.measurement_unit.replace('KG',
                                            'ml') }}</td>
                                    <th><i v-if="item.received === 1" class="fa-solid fa-circle-check"></i>
                                        <i v-else class="fa-regular fa-circle"></i>
                                    </th>
                                    <th>{{ item.observations }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('dispatch.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div> -->
        </SectionCard>
    </BaseLayout>
</template>