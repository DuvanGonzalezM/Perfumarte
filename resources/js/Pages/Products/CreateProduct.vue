<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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

const optionSupplier = ref(props.supplierProduct.map(supplier => [{ 'title': supplier.name, 'value': supplier.supplier_id }][0]));

const submit = () => {
    form.post(route('product.store'));
}

</script>

<template>

    <Head title="Nuevo Producto" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <!-- <Alert /> -->
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
                                    <TextInput type="text" name="measurement_unit[]" id="measurement_unit[]" v-model="form.measurement_unit"
                                        :required="true"
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
                                    <TextInput type="category" name="category[]" id="category[]" v-model="form.category"
                                        :required="true"
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
                    <div class="row my-5 text-center">
                        <div class="col">
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Registrar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>