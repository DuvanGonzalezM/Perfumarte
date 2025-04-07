<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import moment from 'moment';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    dispatchDetails: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    products: props.dispatchDetails.map(detail => ({
        dispatchs_detail_id: detail.dispatchs_detail_id || '',
        dispatch_id: detail.dispatch_id || '',
        name: detail.inventory?.product?.reference || '',
        quantity: detail.dispatched_quantity || 0,
        received: detail.received || false,
        observation: detail.observations || ''
    }))
});
const showConfirmModal = ref(false);
const allProductsReceived = computed(() => {
    return form.products.every(product => product.received);
});

const submit = () => {
    form.post(route('dispatch.receive'), {
        onSuccess: () => {
            // Manejar éxito
        },
    });
};
</script>

<template>
    <Head title="Recepción de Despacho" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
        </template>
        <SectionCard :idSection="props.dispatchDetails[0].dispatch_id"
            :subtitle="props.dispatchDetails[0].dispatch.status + (props.dispatchDetails[0].dispatch.status.trim().toLowerCase() === 'pendiente' ? ' por despachar' : '')"
            :subextra="moment(props.dispatchDetails[0].dispatch.created_at).format('DD/MM/Y')" v-if="props.dispatchDetails.length > 0">
            <template #headerSection>
                <strong>Detalles del Despacho</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Recibido</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in form.products" :key="index">
                                            <template v-if="props.dispatchDetails[0].dispatch.status.trim().toLowerCase() === 'en ruta'">
                                                <td>{{ product.name }}</td>
                                                <td>{{ product.quantity }}</td>
                                                <td>
                                                    <input type="checkbox" v-model="form.products[index].received">
                                                </td>
                                                <td>
                                                    <TextInput v-model="form.products[index].observation"
                                                        placeholder="Agregar observación" />
                                                </td>
                                            </template>
                                            <template v-else>
                                                <td>{{ product.name }}</td>
                                                <td>{{ product.quantity }}</td>
                                                <td>
                                                    <input type="checkbox" v-model="form.products[index].received"
                                                        disabled>
                                                </td>
                                                <td>
                                                    {{ form.products[index].observation }}
                                                </td>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-12">
                            <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
                                <template #header>
                                    Confirmar Recepción
                                </template>
                                <template #body>
                                    ¿Está seguro que desea confirmar la recepción de estos productos e insumos?
                                </template>
                                <template #footer>
                                    <PrimaryButton @click="submit">
                                        Confirmar
                                    </PrimaryButton>
                                </template>
                            </ModalPrais>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-12 d-flex justify-content-between">
                            <PrimaryButton v-if="props.dispatchDetails[0].dispatch.status.trim().toLowerCase() === 'en ruta'"
                                @click="showConfirmModal = true" class="px-5" @close="showConfirmModal = false"
                                type="submit" :disabled="!allProductsReceived">
                                Confirmar Recepción
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
        <SectionCard v-if="props.dispatchDetails.length == 0">
            <template #headerSection>
                <strong>Detalles del Despacho</strong>
            </template>
            <div class="container px-0">
                <p>No se tiene ningún despacho <b>En Ruta</b></p>
            </div>
        </SectionCard>
    </BaseLayout>
</template>