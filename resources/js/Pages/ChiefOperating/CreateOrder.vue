<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
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
    references: [{
        'reference': '',
        'quantity': ''
    }],
});
const selectSuplier = ref('0');
var countProducts = 0;
const unity = ref('KG');
const productsAll = ref(props.suppliers[0].products);
const products = productsAll;
const selectProduct = ref(products.value.slice(0, 1)[0].product_id);
const selectedSupplier = () => {
    form.supplier = props.suppliers[selectSuplier.value].supplier_id;
    products.value = props.suppliers[selectSuplier.value].products;
    console.log(products.value.slice(0, 1)[0].product_id);
    selectProduct.value = products.value.slice(0, 1)[0].product_id;
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
            <div class="container px-0">
                <form class="table-responsive table-prais">
                    <div class="row">
                        <div class="col-md-6 py-3 align-middle">
                            <select class="selectsearch" name="supplier" id="supplier" v-model="selectSuplier" @change="selectedSupplier()"
                                placeholder="Proveedores">
                                <option v-for="(supplier, index) in suppliers" :value="index" data-index="index">{{
                                    supplier.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 py-3 align-middle">
                            <TextInput type="number" name="supplier_order" id="supplier_order"
                                v-model="form.supplier_order" labelValue="Orden de compra - Proveedor" />
                        </div>
                    </div>
                    <table class="table table-hover dt-body-nowrap">
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
                                    <select class="selectsearch" name="reference[]" v-model="selectProduct" @change="selectedProduct()"
                                        placeholder="Proveedores">
                                        <option v-for="product in products" :value="product.product_id">{{
                                            product.reference }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="form.references[countProducts]['quantity']" />
                                </td>
                                <td>
                                    {{ unity }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <i class="fa-solid fa-plus"></i>
                </form>
                <PrimaryButton :href="route('orders.list')"style="margin-right: 20px;">
                    Volver
                </PrimaryButton>
                <PrimaryButton @click="submit"style="margin-right: 20px;">
                    Enviar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>