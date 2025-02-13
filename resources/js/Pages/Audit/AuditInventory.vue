<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import moment from 'moment';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    productsAudit: {
        type: Array,
        required: true,
    },
    location_id: {
        type: String,
        required: true,
    },
    location_name: {
        type: String,
        required: true,
    },
});

const form = useForm({
    products: props.productsAudit.map(product => ({
        inventory_id: product.inventory_id,
        commercial_reference: product.product?.commercial_reference || 'N/A',
        measurement_unit: product.product?.measurement_unit || 'N/A',
        quantity: product.quantity || 0,
        confirmed: false,
        observations: product.observations || '',
    })),
    location_id: props.location_id,
});


const disableButton = ref(false);

const submit = () => {
    console.log('location_id:', form.location_id);
    form.post(route('audit.storeInventory'), {
        onSuccess: () => {
            router.visit(route('audits'));
        },

    });
};
</script>

<template>

    <Head title="Auditoría de Inventario" />

    <BaseLayout>
        <template #header>
            <h1>Auditoría de Inventario</h1>
        </template>
        <SectionCard :subtitle="moment(props.productsAudit.created_at).format('DD/MM/Y HH:mm')"
            :subextra="props.location_name">
            <template #headerSection>
                <strong>Registrar Auditoría</strong>
            </template>
            <div class="container">
                <table class="table table-hover text-center dt-body-nowrap align-middle">
                    <thead>
                        <tr>
                            <th>Referencia</th>
                            <th>Cantidad en Inventario</th>
                            <th>Confirmado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody v-if="form.products.length > 0">
                        <tr v-for="(product, index) in form.products" :key="product.inventory_id">
                            <td>{{ product.commercial_reference }}</td>
                            <td>{{ product.quantity }}  {{ product.measurement_unit.replace('KG', 'ml') }}</td>
                            <td>
                                <input type="checkbox" v-model="product.confirmed">
                            </td>
                            <td>
                                <TextInput type="text" v-model="form.products[index].observations"
                                    :name="'products[' + index + '][observations]'"
                                    :id="'product_observations_' + index"
                                    :messageError="form.errors[`products.${index}.observations`] ? form.errors[`products.${index}.observations`] : null" />
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="4">No hay productos disponibles.</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('audits')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton @click="submit" class="px-5" :disabled="disableButton"
                            :class="disableButton ? 'disabled' : ''">
                            Registrar Auditoría
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>