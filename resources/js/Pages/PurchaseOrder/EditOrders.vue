<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const disableButton = ref(false);
const props = defineProps({
    purchaseOrder: {
        type: Object,
    },
});

const form = useForm({
    supplier_order: props.purchaseOrder.supplier_order,
    references: props.purchaseOrder.product_entry_order.map(entry => [{ 'reference': entry.product.product_id, 'quantity': entry.quantity, 'product_entry_id': entry.product_entry_id }])[0],
});

const unity = ref('KG');
const products = ref(props.purchaseOrder.product_entry_order[0].product.supplier.products);
const selectedProduct = (index) => {
    unity.value = products.value.filter((product) => product.product_id == form.references[index]['reference'])[0].measurement_unit;
}
const submit = () => {
    disableButton.value = true;
    form.put(route('orders.update', props.purchaseOrder.purchase_order_id));
};

</script>

<template>

    <Head title="Nueva registro" />

    <BaseLayout>
        
        <template #header>
            <h1>Editar Orden de Compra</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Editar Orden de Compra</strong>
            </template>
            <div class="container px-0">
                <form class="table-responsive table-prais">
                    <div class="row">
                        <div class="col-md-6 py-3 align-middle">
                            {{ props.purchaseOrder.product_entry_order[0].product.supplier.name }}
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
                            <tr v-for="(product_entry_order, index) in purchaseOrder.product_entry_order">
                                <td>
                                    <select class="selectsearch" name="reference[]"
                                        v-model="form.references[index]['reference']" @change="selectedProduct(index)"
                                        placeholder="Proveedores">
                                        <option v-for="product in products" :value="product.product_id">{{
                                            product.reference }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="form.references[index]['quantity']" />
                                </td>
                                <td>
                                    {{ unity }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <i class="fa-solid fa-plus"></i>
                </form>
                <PrimaryButton :href="route('orders.list') "style="margin-right: 20px;">
                    Volver
                </PrimaryButton>

                <PrimaryButton @click="submit"style="margin-right: 20px;" :class="disableButton ? 'disabled' : ''">
                    Actualizar
                </PrimaryButton>
                
            </div>
        </SectionCard>
    </BaseLayout>
</template>