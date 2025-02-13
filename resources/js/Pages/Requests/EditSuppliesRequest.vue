<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import moment from 'moment';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    requestPrais: Object,
    inventory: Array,
});

const form = useForm({
    references: props.requestPrais.detail_request.map(detail => ({
        reference: detail.inventory.inventory_id,
        quantity: detail.quantity
    }))
});

const showApproveModal = ref(false);
const optionInventory = ref(props.inventory.map(inventory => ({ 'title': inventory.product.commercial_reference, 'value': inventory.inventory_id })));
const addReference = () => {
    form.references.push({
        reference: '',
        quantity: '',
    });
};

const removeReference = (index) => {
    form.references.splice(index, 1);
};

const submit = () => {
    form.put(route('suppliesrequest.update', props.requestPrais.request_id));
};
</script>
<template>

    <Head title="Detalle de Solicitud de Insumos" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Detalle de Solicitud de Insumos</h1>
        </template>
        <SectionCard :idSection="requestPrais.request_id"
            :subtitle="requestPrais.status + (requestPrais.status.trim().toLowerCase() === 'pendiente' ? ' por despachar' : '')"
            :subextra="moment(requestPrais.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Información de la Solicitud</strong>
            </template>
            <form @submit.prevent="submit" class="table-prais">
                <table class="table table-hover text-center dt-body-nowrap size-prais-2">
                    <thead>
                        <tr>
                            <th>Referencia</th>
                            <th>Cantidad</th>
                            <th v-if="requestPrais.status.trim().toLowerCase() !== 'pendiente' && can('Editar Solicitudes Insumos')"></th>
                        </tr>
                    </thead>
                    <tbody id="productsList">
                        <tr v-for="(reference, referenceIndex) in form.references" :key="reference.reference"
                            v-if="requestPrais.status.trim().toLowerCase() !== 'pendiente' && can('Editar Solicitudes Insumos')">
                            <td>
                                <SelectSearch :options="optionInventory" v-model="reference.reference"
                                    :getOptionLabel="option => option.label"
                                    :messageError="Object.keys(form.errors).length ? form.errors['references.' + referenceIndex + '.reference'] : null"
                                    name="reference[]" id="reference[]" placeholder="Selecciona una referencia" />

                            </td>
                            <td>
                                <TextInput type="number" name="quantity[]"
                                    :messageError="Object.keys(form.errors).length ? form.errors['references.' + referenceIndex + '.quantity'] : null"
                                    id="quantity[]" v-model="reference.quantity" />
                            </td>
                            <td>
                                <div @click=" removeReference(referenceIndex)" class="removeItem">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="(detail) in props.requestPrais.detail_request" :key="detail.reference"
                            v-if="requestPrais.status.trim().toLowerCase() === 'pendiente'">
                            <td>
                                {{ detail.inventory.product.reference }}
                            </td>
                            <td>
                                {{ detail.quantity }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="row text-center justify-content-center my-5">
                    <div v-if="requestPrais.status.trim().toLowerCase() !== 'pendiente' && can('Editar Solicitudes Insumos')" @click="addReference"
                        class="addItem">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-12">
                        <ModalPrais v-model="showApproveModal" @close="showApproveModal = false">
                            <template #header>
                                Confirmar Aprobación
                            </template>
                            <template #body>
                                ¿Está seguro que desea aprobar esta solicitud?
                            </template>
                            <template #footer>
                                <PrimaryButton @click="submit">
                                    Confirmar
                                </PrimaryButton>
                            </template>
                        </ModalPrais>
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-12 d-flex justify-content-between">
                        <PrimaryButton :href="route('suppliesrequest.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                        <PrimaryButton v-if="requestPrais.status.trim().toLowerCase() !== 'pendiente' && can('Editar Solicitudes Insumos')"
                            @click="showApproveModal = true" class="px-5">
                            Aprobar
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </SectionCard>
    </BaseLayout>
</template>
