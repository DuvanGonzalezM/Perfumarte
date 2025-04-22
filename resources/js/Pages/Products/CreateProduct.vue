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
    supplierProduct: {
        type: Array,
    },
});

const form = useForm({
    reference: '',
    measurement_unit: '',
    commercial_reference: '',
    category: '',
    supplier_id: '',
});

const listCategory = ref([
    { name: 'Hombre' },
    { name: 'Dama' },
    { name: 'Niño' },
    { name: 'Unisex' },
    { name: 'N/A' },
]);

const listMeasurement = ref([
    { name: 'KG' },
    { name: 'UNIDAD' },
]);

const optionMeasurement = ref(listMeasurement.value.map(measurement => ({ 'title': measurement.name, 'value': measurement.name })));

const optionCategory = ref(listCategory.value.map(category => ({ 'title': category.name, 'value': category.name })));

const optionSupplier = ref(props.supplierProduct.map(supplier => [{ 'title': supplier.name, 'value': supplier.supplier_id }][0]));

const showConfirmModal = ref(false);

const submit = () => {
    showConfirmModal.value = true;
}

const confirmCreate = () => {
    form.post(route('product.store'), {
        onFinish: () => {
            showConfirmModal.value = false;
        }
    });
}
</script>

<template>
    <Head title="Nuevo Producto" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nuevo producto</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Registro del producto</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <tbody id="productsList">
                            <tr>
                                <td>REFERENCIA</td>
                                <td>
                                    <TextInput type="text" name="reference[]" id="reference[]" v-model="form.reference"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.reference : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>UNIDAD DE MEDIDA</td>
                                <td>
                                    <SelectSearch v-model="form.measurement_unit" :options="optionMeasurement"
                                    :messageError="Object.keys(form.errors).length ? form.errors.measurement_unit : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>REFERENCIA COMERCIAL</td>
                                <td>
                                    <TextInput type="text" name="commercial_reference[]" id="commercial_reference[]" v-model="form.commercial_reference"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.commercial_reference : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>CATEGORIA</td>
                                <td>
                                    <SelectSearch v-model="form.category" :options="optionCategory"
                                    :messageError="Object.keys(form.errors).length ? form.errors.category : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>PROVEEDOR</td>
                                <td>
                                    <SelectSearch v-model="form.supplier_id" :options="optionSupplier"
                                    :messageError="Object.keys(form.errors).length ? form.errors.supplier_id : null" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                    </div>
                    <div class="row my-5 ">
                        <div class="col-6">
                            <PrimaryButton :href="route('products.list')" class="px-5">
                                VOLVER
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                REGISTRAR
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>

        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar Creación
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de crear este nuevo producto?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="confirmCreate" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>