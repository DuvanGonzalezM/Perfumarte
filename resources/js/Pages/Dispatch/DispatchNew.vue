<script setup>
import ModalPrais from '@/Components/ModalPrais.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import { ref } from 'vue';

const props = defineProps({
    inventory: {
        type: Array,
        required: true,
    },
    warehouses: {
        type: Array,
        required: true,
    },
    requests: {
        type: Array,
        required: true,
    }
});
const form = useForm({
    dispatches: [],
});
const showModal = ref(false);
const optionWarehouse = ref(props.warehouses.map(warehouse => ({ 'title': warehouse.location.name, 'value': warehouse.warehouse_id })));
const optionRequests = ref(props.requests.map(request => ({ 'title': (request.user.location.name + ' - ' + moment(request.created_at).format('DD/MM/Y')), 'value': request.request_id })));
const optionInventory = ref(props.inventory.map(inventory => ({ 'title': inventory.product.reference, 'value': inventory.inventory_id })));

const addReference = (dispatch) => {
    dispatch.references.push({
        reference: '',
        dispatched_quantity: '',
    });
};
const removeReference = (dispatch, referenceIndex) => {
    dispatch.references.splice(referenceIndex, 1);
};

const requestSeleted = ref(null);
const locationSeleted = ref(null);

const openModal = () => {
    showModal.value = true;
    requestSeleted.value = null;
    locationSeleted.value = null;
}

const addDispatch = () => {
    let locationRequest = null;
    let warehouse = null;
    let references = [
        {
            reference: '',
            dispatched_quantity: '',
        }
    ];
    showModal.value = false;
    if (requestSeleted.value) {
        locationRequest = props.requests.find(requestI => requestI.request_id == requestSeleted.value);
        warehouse = props.warehouses.find(warehouseI => warehouseI.location_id == locationRequest.user.location_id).warehouse_id;
        if (locationRequest.detail_request.length > 0) {
            references = [];
            locationRequest.detail_request.forEach(detail => {
                references.push({
                    reference: detail.inventory.inventory_id,
                    dispatched_quantity: detail.quantity,
                });
            });
        }
    } else if (locationSeleted.value) {
        warehouse = locationSeleted.value;
    }
    requestSeleted.value = null;
    locationSeleted.value = null;
    form.dispatches.push({
        warehouse: warehouse,
        references: references,
    });
};
const removeDispatch = (index) => {
    form.dispatches.splice(index, 1);
};

const submit = () => {
    form.post(route('dispatch.store'));
};
</script>
<template>

    <Head title="Nuevo Despacho" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nuevo Despacho</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Nuevo Despacho</strong>
            </template>
            <div class="container px-0">
                <form class="table-prais">
                    <div v-for="(dispatch, dispatchIndex) in form.dispatches" :key="dispatchIndex">
                        <div v-if="dispatch.warehouse">
                            <div class="row mb-2">
                                <div class="supplier-info">
                                    <div class="info-card">
                                        <div style="display: flex;">
                                            <h6 style="width: 80%;">{{ props.warehouses.find(warehouse => warehouse.warehouse_id ==
                                                dispatch.warehouse).location.name }}</h6>
                                            <div class="remove-dispatch text-end" style="width: 20%;">
                                                <a href="#" @click="removeDispatch(dispatchIndex)">
                                                    <i class="fa-solid fa-house-circle-xmark"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-if="form.errors.error" style="color: red;">
                                {{ form.errors.error }}
                            </p>
                            <table class="table table-hover text-center dt-body-nowrap size-prais-2">
                                <thead>
                                    <tr>
                                        <th>REFERENCIA / INSUMO</th>
                                        <th>CANTIDAD ENVIADA</th>
                                    </tr>
                                </thead>
                                <tbody id="productsList">
                                    <tr v-for="(reference, referenceIndex) in dispatch.references">
                                        <td>
                                            <SelectSearch :options="optionInventory" v-model="reference.reference"
                                                :messageError="Object.keys(form.errors).length ? form.errors['dispatches.' + dispatchIndex + '.references.' + referenceIndex + '.reference'] : null"
                                                name="reference[]" id="reference[]"
                                                placeholder="Selecciona una referencia" />
                                        </td>
                                        <td>
                                            <TextInput type="number" name="dispatched_quantity[]"
                                                :messageError="Object.keys(form.errors).length ? form.errors['dispatches.' + dispatchIndex + '.references.' + referenceIndex + '.dispatched_quantity'] : null"
                                                id="dispatched_quantity[]" v-model="reference.dispatched_quantity" />
                                        </td>
                                        <div class="removeItem" @click="removeReference(dispatch, referenceIndex)">
                                            <i class="fa-solid fa-trash"></i>
                                        </div>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row text-center justify-content-center my-5">
                                <div class="addItem" @click="addReference(dispatch)">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <div class="row my-5">
                        <div class="col text-center">
                            <PrimaryButton @click="openModal" class="px-5">
                                Agregar sede <i class="fa-solid fa-house-medical"></i>
                            </PrimaryButton>
                        </div>
                        <ModalPrais v-model="showModal" @close="showModal = false">
                            <template #header>
                                Agregar sede
                            </template>
                            <template #body>
                                Seleccione la solicitud o la sede a despachar
                                <SelectSearch :options="optionRequests" class="my-4" :disabled="locationSeleted != null"
                                    v-model="requestSeleted" labelValue="Selecciona una solicitud" />
                                <SelectSearch :options="optionWarehouse" :disabled="requestSeleted != null"
                                    v-model="locationSeleted" labelValue="Selecciona una sede" />
                            </template>
                            <template #footer>
                                <PrimaryButton @click="addDispatch" class="px-5">
                                    Agregar
                                </PrimaryButton>
                            </template>
                        </ModalPrais>
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('dispatch.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Crear Despacho
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
