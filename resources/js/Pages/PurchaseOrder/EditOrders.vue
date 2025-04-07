<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const disableButton = ref(false);
const props = defineProps({
    purchaseOrder: {
        type: Object,
    },
});

// Inicializar el formulario con los datos existentes
const form = useForm({
    supplier_order: props.purchaseOrder.supplier_order,
    references: props.purchaseOrder.product_entry_order.map(entry => ({
        'reference': entry.product.product_id,
        'quantity': entry.quantity,
        'batch': entry.batch,
        'unity': entry.product.measurement_unit,
        'product_entry_id': entry.product_entry_id
    }))
});

// Obtener todos los productos del proveedor
const products = ref(props.purchaseOrder.product_entry_order[0]?.product.supplier?.products || []);
const showAddButtom = ref(true);

// Opciones para el SelectSearch
const optionProduts = computed(() => {
    return products.value.map(product => ({
        'title': product.reference,
        'value': product.product_id
    }));
});

// Actualizar unidad de medida cuando se selecciona un producto
const selectedProduct = (index) => {
    const selectedProduct = products.value.find(p => p.product_id === form.references[index].reference);
    if (selectedProduct) {
        form.references[index].unity = selectedProduct.measurement_unit;
    }
};

// Enviar el formulario
const submit = () => {
    disableButton.value = true;
    form.put(route('orders.update', props.purchaseOrder.purchase_order_id));
};

// Agregar nueva referencia
const addReference = () => {
    if (form.references.length < products.value.length) {
        form.references.push({
            'reference': '',
            'quantity': '',
            'unity': 'KG', // Valor por defecto
        });
    }
    // Actualizar visibilidad del botón
    showAddButtom.value = form.references.length < products.value.length;
};

// Remover referencia
const removeReference = (index) => {
    if (form.references.length > 1) { // Mantener al menos una referencia
        form.references.splice(index, 1);
    }
    // Actualizar visibilidad del botón
    showAddButtom.value = form.references.length < products.value.length;
};
</script>

<template>
    <Head title="Editar orden de compra" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Editar Orden de Compra</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Editar Orden de Compra</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                        <div class="col-6 p-2 cardboxprais cardpurcheorder">
                            {{ props.purchaseOrder.product_entry_order[0]?.product?.supplier?.name || 'Proveedor no disponible' }}
                        </div>
                        <div class="col-md-6 py-3 align-middle">
                            <TextInput type="number" name="supplier_order" id="supplier_order"
                                v-model="form.supplier_order" labelValue="Orden de compra - Proveedor"
                                :messageError="form.errors.supplier_order" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-4 mt-5">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD</th>
                                <th>UNIDAD DE MEDIDA</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references" :key="index">
                                <td>
                                    <SelectSearch v-model="reference.reference" :options="optionProduts"
                                        @change="selectedProduct(index)"
                                        :messageError="form.errors[`references.${index}.reference`]"
                                        placeholder="Seleccione una referencia" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="reference.quantity" step="0.01"
                                        :messageError="form.errors[`references.${index}.quantity`]" />
                                </td>
                                <td>
                                    <span class="unity-display">{{ reference.unity }}</span>
                                </td>
                                <td>
                                    <div class="removeItem" @click="removeReference(index)" v-if="form.references.length > 1">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addReference" v-if="showAddButtom">
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
                            <PrimaryButton type="submit" class="px-5" :class="disableButton ? 'disabled' : ''">
                                Actualizar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>