<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    newSupplier: {
        type: Array,
    },
});

const form = useForm({
    nit: '',
    name: '',
    country: '',
    address: '',
    phone: '',
    email: '',
});

const showConfirmModal = ref(false);

const submit = () => {
    showConfirmModal.value = true;
}

const confirmCreate = () => {
    form.post(route('supplier.store'), {
        onFinish: () => {
            showConfirmModal.value = false;
        }
    });
}
</script>

<template>
    <Head title="Nuevo Proveedor" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nuevo Proveedor</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Registro del proveedor</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <tbody id="supplierList">
                            <tr>
                                <td>NIT</td>
                                <td>
                                    <TextInput type="text" name="nit" id="nit" v-model="form.nit" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.nit : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>NOMBRE</td>
                                <td>
                                    <TextInput type="text" name="name" id="name" v-model="form.name" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.name : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>PAIS</td>
                                <td>
                                    <TextInput type="text" name="country" id="country" v-model="form.country"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.country : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>DIRECCION</td>
                                <td>
                                    <TextInput type="text" name="address" id="address" v-model="form.address"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.address : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>TELEFONO</td>
                                <td>
                                    <TextInput type="text" name="phone" id="phone" v-model="form.phone" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.phone : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>EMAIL</td>
                                <td>
                                    <TextInput type="text" name="email" id="email" v-model="form.email" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.email : null" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('suppliers.list')" class="px-5">
                                VOLVER
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                REGISTRAR
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>

        <!-- Modal de Confirmación para Creación -->
        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar Creación
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de crear este nuevo proveedor?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="confirmCreate" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
                <PrimaryButton @click="showConfirmModal = false" class="px-5 btn-secondary">
                    Cancelar
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>