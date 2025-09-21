<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    inventoryReturn: {
        type: Array,
        required: true,
    },
    warehouseReturn: {
        type: Object,
        required: true,
    },
});

const getError = (damageReturnIndex, refIndex, field) => {
    return form.errors[`damageReturn.${damageReturnIndex}.references.${refIndex}.${field}`];
};

const form = useForm({
    damageReturn: [
        {
            warehouse_id: props.warehouseReturn.warehouse_id,
            references: [
                { reference: '', damage_quantity: '', observations: '' }
            ]
        }
    ],
});

const isFormValid = computed(() => {
    return form.damageReturn.every(damageReturn => {
        return damageReturn.references.every(ref => {
            return ref.reference && ref.damage_quantity && ref.observations
        })
    })
});

const showErrorsModal = ref(false);
const optionInventory = ref(props.inventoryReturn.map(inventory => ({ 'title': inventory.product.reference, 'value': inventory.inventory_id })));

const addReference = (dispatch) => {
    dispatch.references.push({
        reference: '',
        damage_quantity: '',
        observations: '',
    });
};
const removeReference = (damageReturn, referenceIndex) => {
    damageReturn.references.splice(referenceIndex, 1);
};

const showConfirmModal = ref(false);
const confirmCreate = () => {
    form.post(route('damageReturn.store'), {
        onSuccess: () => {
            showConfirmModal.value = false;
            
        },
        onError: () => {
            showErrorsModal.value = true;
            showConfirmModal.value = false;

        }
    });
}





</script>
<template>

    <Head title="Nueva Devolucion" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nueva Devolucion</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Nueva Devolucion</strong>
            </template>
            <div class="container px-0">
                <form class="table-prais">
                    <div v-for="(damageReturn, damageReturnIndex) in form.damageReturn" :key="damageReturnIndex">
                        <div>
                            <table class="table table-hover text-center dt-body-nowrap size-prais-2">
                                <thead>
                                    <tr>
                                        <th>Referencia / Insumo</th>
                                        <th>Cantidad </th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ref, refIndex) in damageReturn.references" :key="refIndex">
                                        <td>
                                            <SelectSearch v-model="ref.reference" :options="optionInventory"
                                                placeholder="Selecciona referencia" />
                                        </td>

                                        <td>
                                            <input type="number" v-model="ref.damage_quantity" class="form-control w-20"
                                                :class="{ 'is-invalid': getError(damageReturnIndex, refIndex, 'damage_quantity') }" />

                                            <div v-if="getError(damageReturnIndex, refIndex, 'damage_quantity')">
                                            </div>
                                        </td>

                                        <td>
                                            <input type="text" v-model="ref.observations" class="border p-1 w-full" />
                                        </td>
                                        <div class="removeItem" @click="removeReference(damageReturn, refIndex)">
                                            <i class="fa-solid fa-trash"></i>
                                        </div>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row text-center justify-content-center my-5">
                                <div class="addItem" @click="addReference(damageReturn)">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('damageReturn.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="showConfirmModal = true" class="px-5" :disabled="!isFormValid || form.processing">
                                Registar devolucion
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar Registro
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de registrar esta devolucion?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="confirmCreate" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>

        <ModalPrais v-model="showErrorsModal" @close="showErrorsModal = false">
         <template #header>
            ERROR!
         </template>
         <template #body>
            <div class="text-center">
                <h4>La cantidad devuelta supera la cantidad en inventario</h4>
            </div>
         </template>
        </ModalPrais>
    </BaseLayout>
</template>
