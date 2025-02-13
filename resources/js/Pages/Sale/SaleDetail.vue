<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    sale: {
        type: Object,
    }
});

</script>

<template>

    <Head title="Venta" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Venta</h1>
        </template>

        <SectionCard :subextra="'Valor total: $' + sale.total" :subtitle="sale.payment_method">
            <template #headerSection>
                <strong>Venta</strong>
            </template>
            <div class="container">
                <table class="table table-hover text-center dt-body-nowrap size-prais-5 mt-5">
                    <thead>
                        <tr>
                            <th>REFERENCIA</th>
                            <th>PRESENTACION</th>
                            <th>UNIDAD(ES)</th>
                            <th>N° GOTAS</th>
                            <th>PRECIO</th>
                        </tr>
                    </thead>
                    <tbody id="productsList">
                        <tr v-for="(reference, index) in props.sale.sale_details">
                            <td>{{ reference.inventory.product.commercial_reference }}</td>
                            <td>{{ reference.quantity }}</td>
                            <td>{{ reference.units }}</td>
                            <td>{{ reference.drops }}</td>
                            <td>$ {{ reference.price }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row my-5 justify-content-center">
                    <div class="col-6 d-flex justify-content-center">
                        <PrimaryButton :href="route('sales.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>