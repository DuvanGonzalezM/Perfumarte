<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    getProduct: {
        type: Array,
    },
});

const form = useForm({
    reference: '',
    quantity: '',
});

const optionProduts = ref(
    props.getProduct.map(inventory => ({
        title: inventory.product.reference,
        value: inventory.product_id,
    }))
);

const showConfirmModal = ref(false);

// Mostrar modal
const showConfirmation = () => {
    showConfirmModal.value = true;
};

// Enviar formulario
const submit = () => {
    form.post(route('store.repackage'), {
        onFinish: () => {
            showConfirmModal.value = false;
        }
    });
};
</script>

<template>

    <Head title="Nuevo reenvase" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nuevo reenvase</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nuevo reenvase</strong>
            </template>
            <div class="container px-0">
                <!-- Mostrar mensaje de error general (como "No hay suficiente stock") -->
                <div v-if="form.errors.quantity" class="alert alert-danger text-center my-3">
                    {{ form.errors.quantity }}
                </div>

                <form @submit.prevent="showConfirmation" class="table-prais">
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD (ml)</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr>
                                <td>
                                    <SelectSearch v-model="form.reference" :options="optionProduts"
                                        :messageError="form.errors.reference" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]" v-model="form.quantity"
                                        :required="true" :messageError="form.errors.quantity" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('repackage.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="showConfirmation" class="px-5"
                                :class="form.processing ? 'disabled' : ''">
                                Registrar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>

        <!-- Modal de Confirmación -->
        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar reenvase
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de crear este reenvase?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="submit" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
                <PrimaryButton @click="showConfirmModal = false" class="px-5">
                    Cancelar
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>