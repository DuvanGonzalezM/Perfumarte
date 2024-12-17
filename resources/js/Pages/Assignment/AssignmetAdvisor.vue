<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';


const props = defineProps({
    dispatch: {
        type: Array,
       
    },
});

</script>

<template>

    <Head :title= "Asignar Asesores" />

    <BaseLayout>
        <template #header>
            <h1>Asignar Asesores</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Asesores en sede</strong>
            </template>

            <div class="container">
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