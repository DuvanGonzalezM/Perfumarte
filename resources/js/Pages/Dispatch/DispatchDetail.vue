<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import moment from 'moment';

const props = defineProps({
    detaildispatch: {
        type: Object,
        required: true,
    },
});

const groupedDispatches = computed(() => {
    const dispatches = {};
    props.detaildispatch.dispatchdetail.forEach(item => {
        const warehouseName = item.warehouse.location.name || 'No disponible';
        if (!dispatches[warehouseName]) {
            dispatches[warehouseName] = [];
        }
        dispatches[warehouseName].push(item);
    });
    return dispatches;
});
</script>

<template>
    <Head :title="'Detalle del despacho ' + detaildispatch.id" />

    <BaseLayout>
        <template #header>
            <h1>Detalle del despacho</h1>
        </template>

        <SectionCard :idSection="detaildispatch.id"
            :subtitle="moment(detaildispatch.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle del despacho</strong>
            </template>

            <div class="container">
                <div v-for="(items, warehouseName) in groupedDispatches" :key="warehouseName" class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <h5>Sede: {{ warehouseName }}</h5>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Referencia</th>
                                    <th>Cantidad Despachada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in items" :key="index">
                                    <td>{{ item.inventory.product.reference }}</td>
                                    <td>{{ item.dispatched_quantity }} {{ item.inventory.product.measurement_unit.replace('KG', 'ml') }}</td>
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
            </div>
        </SectionCard>
    </BaseLayout>
</template>
