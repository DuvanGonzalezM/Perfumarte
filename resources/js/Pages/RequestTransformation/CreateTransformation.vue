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
    inventories: {
        type: Array,
    },
    errors: Object // Asegura recibir errores del backend
});

const form = useForm({
    references: [
        {
            reference: '',
            quantity: '',
        }
    ],
});

// Opciones para el select
const optionProduts = ref(
    props.inventories.map(inventory => ({
        title: inventory.product.commercial_reference,
        value: inventory.inventory_id
    }))
);

const showAddButtom = ref(form.references.length < optionProduts.value.length);
const showConfirmModal = ref(false);

const addRow = () => {
    if (form.references.length < optionProduts.value.length) {
        form.references.push({ reference: '', quantity: '' });
        showAddButtom.value = form.references.length < optionProduts.value.length;
    }
};

const removeReference = (index) => {
    form.references.splice(index, 1);
    showAddButtom.value = form.references.length < optionProduts.value.length;
};

const showConfirmation = () => {
 
    showConfirmModal.value = true;
};

const submit = () => {
    form.post(route('transformation.store'), {
        onSuccess: () => {
            showConfirmModal.value = false;
        },
        onError: () => {
            showConfirmModal.value = false;
        }
    });
};
</script>

<template>
    <Head title="Nueva Transformación" />

    <BaseLayout :loading="form.processing">
        <template #header>
            <h1>Nueva Transformación</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva Transformación</strong>
            </template>

            <div class="container px-0">

                <form @submit.prevent="showConfirmation" class="table-prais">
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD (ml)</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references" :key="index">
                                <td>
                                    <SelectSearch v-model="reference.reference" :options="optionProduts"
                                        :messageError="form.errors[`references.${index}.reference`] || null" />
                                </td>
                                <td>
                                    <TextInput type="number" v-model="reference.quantity" :required="true"
                                        :messageError="form.errors[`references.${index}.quantity`] || null" />
                                </td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addRow" v-if="showAddButtom">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>

                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('transformationRequest.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="showConfirmation" class="px-5"
                                :class="{ disabled: form.processing }">
                                Enviar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>

        <!-- Modal de Confirmación -->
        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar solicitud
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de registrar esta solicitud de transformación?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="submit" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>