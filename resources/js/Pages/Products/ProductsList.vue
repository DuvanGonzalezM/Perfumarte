<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { can } from 'laravel-permission-to-vuejs';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';

const props = defineProps({
    getProducts: {
        type: Array,
    },

    supplierProduct: {
        type: Array,
    }
});

const form = useForm({
    product_id: '',
    reference: '',
    measurement_unit: '',
    commercial_reference: '',
    category: '',
    supplier_id: '',
});

const showModal = ref(false);
const showConfirmUnableModal = ref(false);
const productToDisableId = ref(null); 
const disableForm = useForm({}); 

const columnsTable = [
    {
        data: 'reference',
        title: 'REFERENCIA'
    },
    {
        data: 'measurement_unit',
        title: 'UNIDAD DE MEDIDA'
    },
    {
        data: "commercial_reference",
        title: 'REFERENCIA COMERCIAL'
    },
    {
        data: "category",
        title: 'CATEGORIA'
    },
    {
        data: "supplier.name",
        title: 'PROVEEDOR'
    },
    {
        data: 'name',
        title: 'EDITAR',
        render: '#render',
    },
    {
        data: 'name',
        title: 'ELIMINAR',
        render: '#rendertwo',
    },
];

const optionSupplier = ref(props.supplierProduct.map(supplier => ({ 'title': supplier.name, 'value': supplier.supplier_id })));

const openModal = (rowData) => {
    form.product_id = rowData.product_id;
    form.reference = rowData.reference;
    form.measurement_unit = rowData.measurement_unit;
    form.commercial_reference = rowData.commercial_reference;
    form.category = rowData.category;
    form.supplier_id = rowData.supplier?.supplier_id;
    showModal.value = true;
}

const submit = () => {
    form.put(route('products.update', form.product_id));
    showModal.value = false;
}

const confirmUnable = (rowData) => {
    productToDisableId.value = rowData.product_id;
    showConfirmUnableModal.value = true;
}

const disableProduct = () => {
    disableForm.put(route('products.disable', productToDisableId.value)); 
            showConfirmUnableModal.value = false;
            productToDisableId.value = null;
        
};

</script>

<template>

    <Head title="Productos" />

    <BaseLayout :loading="disableForm.processing || form.processing ? true : false" >
        <template #header>
            <h1>Productos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Productos</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('product.create')" class="position-absolute" v-if="can('Crear Productos')">
                    Nuevo Registro
                </PrimaryButton>
                <Table :data="getProducts" :columns="columnsTable">
                    <template #templateRender="items">
                        <a href="#" @click="openModal(items.item.rowData)"> <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </template>

                    <template #templateRendertwo="items">
                        <a href="#" @click="confirmUnable(items.item.rowData)"> <i class="fa-solid fa-trash"></i>
                        </a>
                    </template>
                </Table>
                <ModalPrais v-model="showModal" @close="showModal = false">
                    <template #header>
                        <h3>Editar Producto</h3>
                    </template>
                    <template #body>
                        <div class="row">
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="reference" id="reference" v-model="form.reference"
                                    :focus="form.reference != null ? true : false" labelValue="Referencia"
                                    :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="measurement_unit" id="measurement_unit"
                                    v-model="form.measurement_unit"
                                    :focus="form.measurement_unit != null ? true : false" labelValue="Unidad de medida"
                                    :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="commercial_reference" id="commercial_reference"
                                    v-model="form.commercial_reference"
                                    :focus="form.commercial_reference != null ? true : false"
                                    labelValue="Referencia comercial" :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="category" id="category" v-model="form.category"
                                    :focus="form.category != null ? true : false" labelValue="Categoria"
                                    :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <SelectSearch v-model="form.supplier_id" :options="optionSupplier"
                                    :messageError="Object.keys(form.errors).length ? form.errors.supplier_id : null" />
                            </div>
                        </div>
                        <div class="col-md-12 my-4 d-flex justify-content-center ">
                            <PrimaryButton @click="submit" class="px-5">
                                Guardar
                            </PrimaryButton>
                        </div>
                    </template>
                    <template #footer>
                        <div></div>
                    </template>
                </ModalPrais>

                <ModalPrais v-model="showConfirmUnableModal" @close="showConfirmUnableModal = false">
        <template #header>
            Confirmar Desactivación
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-triangle-exclamation text-warning fa-3x"></i>
                <h3 class="mt-3">¿Estás seguro de desactivar este producto?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="disableProduct()" class="px-5 btn-danger">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showConfirmUnableModal = false" class="px-5 btn-secondary">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

            </div>
        </SectionCard>
    </BaseLayout>
</template>