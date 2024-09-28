<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    suppliers: {
        type: Array,
    },
});
const form = useForm({
    supplier: props.suppliers[0].supplier_id,
    supplier_order: '',
    references: [
        {
            'reference': '',
            'quantity': '',
            'batch': '',
        }
    ],
});
const unity = ref('KG');
const productsAll = ref(props.suppliers[0].products);
const products = productsAll;
const selectProduct = ref(products.value.slice(0, 1)[0].product_id);
const optionSuppliers = ref(props.suppliers.map((supplier, index) => [{ 'title': supplier.name, 'value': supplier.supplier_id }][0]));
const optionProduts = ref(props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.map(product => [{ 'title': product.reference, 'value': product.product_id }][0]));
const selectedSupplier = () => {
    if (form.supplier != null) {
        optionProduts.value = props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.map(product => [{ 'title': product.reference, 'value': product.product_id }][0]);
    }
}
const submit = () => {
    form.post(route('orders.store'));
};

const addReference = () => {
    form.references.push({
        'reference': '',
        'quantity': '',
        'batch': '',
    });
}

const removeReference = (index) => {
    form.references.splice(index, 1);
}
</script>

<template>

    <Head title="Nueva registro" />

    <BaseLayout>
        <template #header>
            <h1>Nueva registro</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva registro</strong>
            </template>
            <div class="container px-0">
                <form class="table-prais">
                    <div class="row">
                        <div class="col-md-6 py-3 align-middle">
                            <SelectSearch v-model="form.supplier" :options="optionSuppliers"
                                :change="selectedSupplier()" labelValue="Proveedor"/>
                        </div>
                        <div class="col-md-6 py-3 align-middle">
                            <TextInput type="number" name="supplier_order" id="supplier_order"
                                v-model="form.supplier_order" labelValue="Orden de compra - Proveedor" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-3 align-middle">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD (KG)</th>
                                <th>N° LOTE</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>
                                    <SelectSearch v-model="form.references[index]['reference']"
                                        :options="optionProduts" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="form.references[index]['quantity']" />
                                </td>
                                <td>
                                    <TextInput type="number" name="batch[]" id="batch[]"
                                        v-model="form.references[index]['batch']" />
                                </td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addReference">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('orders.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5">
                                Enviar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>