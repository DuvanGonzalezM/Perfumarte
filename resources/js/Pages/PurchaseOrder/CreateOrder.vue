<script setup>
import ModalPrais from '@/Components/ModalPrais.vue';
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
            'unity': '',
        }
    ],
});
const products = ref(props.suppliers[0].products);
const optionSuppliers = ref(props.suppliers.map((supplier) => [{ 'title': supplier.name, 'value': supplier.supplier_id }][0]));
const optionProduts = ref(props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.map(product => [{ 'title': product.reference, 'value': product.product_id }][0]));
const showAddButtom = ref(form.references.length < optionProduts.value.length);
const showModal = ref(false);

const submit = () => {
    form.post(route('orders.store'));
    showModal.value = false;
};

const selectedSupplier = () => {
    form.references = [
        {
            'reference': '',
            'quantity': '',
            'batch': '',
            'unity': '',
        }
    ];
    if (form.supplier != null) {
        optionProduts.value = props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.map(product => [{ 'title': product.reference, 'value': product.product_id }][0]);
    }
    showAddButtom.value = form.references.length < optionProduts.value.length;
}

const selectedReference = (reference) => {
    if (form.supplier != null) {
        let product = props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.find(product => product.product_id == reference.reference);
        if (product) {
            reference.unity = props.suppliers.find(supplier => supplier.supplier_id == form.supplier).products.find(product => product.product_id == reference.reference).measurement_unit;
        }
    }
}

const addReference = () => {
    showAddButtom.value = form.references.length < (optionProduts.value.length - 1);
    if (form.references.length < optionProduts.value.length) {
        form.references.push(
            {
                'reference': '',
                'quantity': '',
                'batch': '',
                'unity': '',
            }
        );
    }
}

const removeReference = (index) => {
    form.references.splice(index, 1);
    showAddButtom.value = form.references.length < optionProduts.value.length;
}
</script>

<template>

    <Head title="Nueva orden de compra" />

    <BaseLayout>
        <template #header>
            <h1>Nueva orden de compra</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva orden de compra</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                        <div class="col-md-6" style="height: 40px;">
                            <SelectSearch v-model="form.supplier" :options="optionSuppliers"
                                :changeFunction="selectedSupplier" labelValue="Proveedor"
                                :messageError="Object.keys(form.errors).length ? form.errors.supplier : null" />
                        </div>
                        <div class="col-md-6" style="height: 40px;">
                            <TextInput type="number" name="supplier_order" id="supplier_order"
                                v-model="form.supplier_order" labelValue="Orden de compra - Proveedor" :required="true"
                                :messageError="Object.keys(form.errors).length ? form.errors.supplier_order : null" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-4 mt-5">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>N° LOTE</th>
                                <th>CANTIDAD</th>
                                <th>UNIDAD DE MEDIDA</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>
                                    <SelectSearch v-model="reference['reference']" :options="optionProduts"
                                        :changeFunction="selectedReference(reference)"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.reference'] : null" />
                                </td>
                                <td>
                                    <TextInput type="text" name="batch[]" id="batch[]" v-model="reference['batch']"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.batch'] : null" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="reference['quantity']" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.quantity'] : null" />
                                </td>
                                <td>
                                    {{ reference.unity }}
                                </td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
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
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Enviar
                            </PrimaryButton>
                        </div>
                    </div>
                    <ModalPrais v-model="showModal" @close="showModal = false">
                        <template #header>
                            Nueva orden de compra
                        </template>
                        <template #body>
                            ¿Seguro quiera registra esta nueva orden de compra?
                        </template>
                        <template #footer>
                            <PrimaryButton @click="submit" class="px-5">
                                Si
                            </PrimaryButton>
                            <PrimaryButton @click="showModal = false" class="px-5">
                                No
                            </PrimaryButton>
                        </template>
                    </ModalPrais>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>