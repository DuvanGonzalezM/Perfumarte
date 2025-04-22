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
const confirmUpdate = ref(false);
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

const optionCategory = ref(props.listCategory.map(category => ({ 'title': category.name, 'value': category.name })));
const optionMeasurement = ref(props.listMeasurement.map(measurement => ({ 'title': measurement.name, 'value': measurement.name }))); 
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
    confirmUpdate.value = true;
}

const confirmUpdateAction = () => {
    form.put(route('products.update', form.product_id), {
        onSuccess: () => {
            confirmUpdate.value = false;
            showModal.value = false;
        },
        onError: () => {
            confirmUpdate.value = false;
        }
    });
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

    <BaseLayout :loading="disableForm.processing || form.processing ? true : false">
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
                        <a href="#" @click="openModal(items.item.rowData)"> 
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </template>

                    <template #templateRendertwo="items">
                        <a href="#" @click="confirmUnable(items.item.rowData)"> 
                            <i class="fa-solid fa-trash"></i>
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
                                    :required="true" :error="form.errors.reference"/>
                            </div>
                            <div class="col-md-12 my-3">
                                <SelectSearch v-model="form.measurement_unit" :options="optionMeasurement" :error="form.errors.measurement_unit" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="commercial_reference" id="commercial_reference"
                                    v-model="form.commercial_reference"
                                    :focus="form.commercial_reference != null ? true : false"
                                    labelValue="Referencia comercial" :required="true" :error="form.errors.commercial_reference" />
                            </div>
                            <div class="col-md-12 my-3">
                                <SelectSearch v-model="form.category" :options="optionCategory" :error="form.errors.category" />
                            </div>
                            <div class="col-md-12 my-3">
                                <SelectSearch v-model="form.supplier_id" :options="optionSupplier"
                                    :messageError="Object.keys(form.errors).length ? form.errors.supplier_id : null" :error="form.errors.supplier_id" />
                            </div>
                        </div>
                        <div class="col-md-12 my-4 d-flex justify-content-center">
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
                        Confirmar Eliminación
                    </template>
                    <template #body>
                        <div class="text-center">
                            <h3 class="mt-3">¿Estás seguro de eliminar este producto?</h3>
                        </div>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="disableProduct()" class="px-5 btn-danger">
                            Confirmar
                        </PrimaryButton>
                    </template>
                </ModalPrais>

                <!-- Modal de Confirmación de Actualización -->
                <ModalPrais v-model="confirmUpdate" @close="confirmUpdate = false">
                    <template #header>
                        Confirmar Edicion
                    </template>
                    <template #body>
                        <div class="text-center">
                            <h4>¿Estás seguro de editar este producto?</h4>
                        </div>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="confirmUpdateAction" class="px-5" :disabled="form.processing">
                            <span v-if="form.processing">Procesando...</span>
                            <span v-else>Confirmar</span>
                        </PrimaryButton>
                    </template>
                </ModalPrais>
            </div>
        </SectionCard>
    </BaseLayout>
</template>