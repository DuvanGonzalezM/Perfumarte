<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    inventory: {
        type: Array,
        required: true,
    },
    warehouses: {
        type: Array,
        required: true,
    }
});
const form = useForm({
    dispatches: [
        {
            warehouse: null,
            references: [
                {
                    reference: '',
                    dispatched_quantity: '',
                    observations: '',
                }
            ],
        }
    ],
});
const optionWarehouse = ref(props.warehouses.map(warehouse => ({ 'title': warehouse.name, 'value': warehouse.warehouse_id })));
const optionInventory = ref(props.inventory.map(inventory => ({ 'title': inventory.product.reference, 'value': inventory.inventory_id })))
const addReference = (dispatchIndex) => {
    form.dispatches[dispatchIndex].references.push({
        reference: '',
        dispatched_quantity: '',
        observations: '',
    });
};
const removeReference = (dispatchIndex, referenceIndex) => {
    form.dispatches[dispatchIndex].references.splice(referenceIndex, 1);
};
const addDispatch = () => {
    form.dispatches.push({
        warehouse: null,
        references: [
            {
                reference: '',
                dispatched_quantity: '',
                observations: '',
            }
        ],
    });
};
const removeDispatch = (index) => {
    form.dispatches.splice(index, 1);
};
const submit = () => {
    form.post(route('dispatch.store'), {
        onSuccess: () => {
        },
        onError: (errors) => {

        }
    });
};
</script>
<template>

    <Head title="Nuevo Despacho" />
    <BaseLayout>
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
                        <div class="row mb-4">
                            <div class="col-md-12 py-3 align-middle">
                                <SelectSearch :options="optionWarehouse"
                                    v-model="form.dispatches[dispatchIndex].warehouse" name="warehouse[]"
                                    id="warehouse[]" labelValue="Selecciona una sede" />
                            </div>
                        </div>
                        <div v-if="form.dispatches[dispatchIndex].warehouse">
                            <table class="table table-hover text-center dt-body-nowrap size-prais-3 align-middle">
                                <thead>
                                    <tr>
                                        <th>REFERENCIA / INSUMO</th>
                                        <th>CANTIDAD ENVIADA</th>
                                        <th>OBSERVACIONES</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="productsList">
                                    <tr v-for="(reference, referenceIndex) in form.dispatches[dispatchIndex].references"
                                        :key="referenceIndex">
                                        <td>
                                            <SelectSearch :options="optionInventory"
                                                v-model="form.dispatches[dispatchIndex].references[referenceIndex].reference"
                                                name="reference[]" id="reference[]"
                                                placeholder="Selecciona una referencia" />
                                        </td>
                                        <td>
                                            <TextInput type="number" name="dispatched_quantity[]"
                                                id="dispatched_quantity[]"
                                                v-model="form.dispatches[dispatchIndex].references[referenceIndex].dispatched_quantity" />
                                        </td>
                                        <td>
                                            <TextInput type="text" name="observations[]" id="observations[]"
                                                v-model="form.dispatches[dispatchIndex].references[referenceIndex].observations" />
                                        </td>
                                        <td>
                                            <div class="removeItem"
                                                @click="removeReference(dispatchIndex, referenceIndex)">
                                                <i class="fa-solid fa-trash"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row text-center justify-content-center my-5">
                                <div class="addItem" @click="addReference(dispatchIndex)">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col text-end">
                                <button type="button" class="btn btn-danger" @click="removeDispatch(dispatchIndex)">
                                    Eliminar despacho de esta sede
                                </button>
                            </div>
                        </div>

                        <hr />
                    </div>
                    <div class="row my-5">
                        <div class="col text-center">
                            <PrimaryButton @click="addDispatch" class="px-5">
                                Agregar Sede
                            </PrimaryButton>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('dispatch.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5">
                                Crear Despacho
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>
