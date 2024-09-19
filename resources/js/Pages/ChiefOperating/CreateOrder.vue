<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
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
    references: [{
        'reference': '',
        'quantity': ''
    }],
});
const selectSuplier = ref('0');
const selectProduct = ref('0');
var countProducts = 0;
const unity = ref('KG');
const productsAll = ref(props.suppliers[0].products);
const products = productsAll;
const selectedSupplier = () => {
    form.supplier = props.suppliers[selectSuplier.value].supplier_id;
    console.log(form);
    
    products.value = props.suppliers[selectSuplier.value].products;
}
const selectedProduct = () => {
    form.references[countProducts]['reference'] = selectProduct.value;
    unity.value = products.value.filter((product) => product.product_id == selectProduct.value)[0].measurement_unit;
}
const submit = () => {
    form.post(route('orders.store'));
};
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
            <div class="container">
                <form class="table-responsive table-prais m-auto">
                    <select name="supplier" id="supplier" v-model="selectSuplier" @change="selectedSupplier()">
                        <option v-for="(supplier, index) in suppliers" :value="index" data-index="index">{{
                            supplier.name }}
                        </option>
                    </select>
                    <input type="number" name="supplier_order" v-model="form.supplier_order">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD</th>
                                <th>UNIDAD DE MEDIDA</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr>
                                <td>
                                    <select name="reference[]" v-model="selectProduct" @change="selectedProduct()">
                                        <option value="0">Referencias</option>
                                        <option v-for="product in products" :value="product.product_id">{{
                                            product.reference }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" v-model="form.references[countProducts]['quantity']">
                                </td>
                                <td>
                                    {{ unity }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <i class="fa-solid fa-plus"></i>
                </form>
                <PrimaryButton :href="route('orders.list')">
                    Volver
                </PrimaryButton>
                <PrimaryButton @click="submit">
                    Enviar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>