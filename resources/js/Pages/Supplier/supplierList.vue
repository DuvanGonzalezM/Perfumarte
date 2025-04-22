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


const props = defineProps({
    getSuppliers: {
        type: Array,
    },

});

const form = useForm({
    supplier_id: '',
    nit: '',
    name: '',
    country: '',
    address: '',
    phone: '',
    email: '',
});

const showModal = ref(false);
const showConfirmUnableModal = ref(false);
const supplierToDisableId = ref(null);
const disableForm = useForm({});
const confirmUpdate = ref(false);  

const columnsTable = [
    {
        data: 'nit',
        title: 'NIT'
    },
    {
        data: 'name',
        title: 'NOMBRE'
    },
    {
        data: "country",
        title: 'PAIS'
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

const openModal = (rowData) => {
    form.supplier_id = rowData.supplier_id;
    form.nit = rowData.nit;
    form.name = rowData.name;
    form.country = rowData.country;
    form.address = rowData.address;
    form.phone = rowData.phone;
    form.email = rowData.email;
    showModal.value = true;
}

const submit = () => {
    confirmUpdate.value = true;
}

const confirmUpdateAction = () => {
    form.put(route('supplier.update', form.supplier_id), {
        onFinish: () => {
            confirmUpdate.value = false;
            showModal.value = false;
        }
    });
}

const confirmUnable = (rowData) => {
    supplierToDisableId.value = rowData.supplier_id;
    showConfirmUnableModal.value = true;
}

const disableSupplier = () => {
    disableForm.put(route('supplier.disable', supplierToDisableId.value));
    showConfirmUnableModal.value = false;
    supplierToDisableId.value = null;
};

</script>

<template>

    <Head title="Proveedores" />

    <BaseLayout :loading="form.processing || disableForm.processing  ? true : false">
        <template #header>
            <h1>Proveedores</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Proveedores</strong>
            </template>
            <div class="container">
                <PrimaryButton :href="route('supplier.create')" class="position-absolute" v-if="can('Crear Proveedores')">
                    Nuevo Registro
                </PrimaryButton>

                <Table :data="getSuppliers" :columns="columnsTable">
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
                        <h3>Editar Proveedor</h3>
                    </template>
                    <template #body>
                        <div class="row">
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="nit" id="nit" v-model="form.nit"
                                    :focus="form.nit != null ? true : false" labelValue="NIT" :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="name" id="name" v-model="form.name"
                                    :focus="form.name != null ? true : false" labelValue="Nombre" :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="country" id="country" v-model="form.country"
                                    :focus="form.country != null ? true : false" labelValue="País" :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="address" id="address" v-model="form.address"
                                    :focus="form.address != null ? true : false" labelValue="Dirección"
                                    :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="phone" id="phone" v-model="form.phone"
                                    :focus="form.phone != null ? true : false" labelValue="Telefono" :required="true" />
                            </div>
                            <div class="col-md-12 my-3">
                                <TextInput type="string" name="email" id="email" v-model="form.email"
                                    :focus="form.email != null ? true : false" labelValue="Correo" :required="true" />
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
                        Confirmar Eliminación
                    </template>
                    <template #body>
                        <div class="text-center">
                            <h3 class="mt-3">¿Estás seguro de eliminar este proveedor?</h3>
                        </div>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="disableSupplier()" class="px-5 btn-danger">
                            Confirmar
                        </PrimaryButton>
                    </template>
                </ModalPrais>

            </div>
        </SectionCard>

        <ModalPrais v-model="confirmUpdate" @close="confirmUpdate = false">
            <template #header>
                Confirmar Actualización
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de actualizar este proveedor?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="confirmUpdateAction" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>

    </BaseLayout>
</template>