<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    inventoryConsumable: {
        type: Array,
        required: true
    },
    warehouseConsumable: {
        type: Object,
        required: true
    },
    userId: {
        type: Number,
        required: true
    }
});

console.log(props.inventoryConsumable);

const getError = (consumableIndex, refIndex, field) => {
    return form.errors[`consumable.${consumableIndex}.references.${refIndex}.${field}`];
};

const form = useForm({
    consumable: [
        {
            warehouse_id: '',
            references: [
                { reference: '', consumable_quantity: '' }
            ]
        }
    ],
});

const isFormValid = computed(() => {
    return form.consumable.every(consumable => {
        return consumable.references.every(ref => {
            return ref.reference && ref.consumable_quantity
        })
    })
});

const showErrorsModal = ref(false);
const optionInventory = ref(
    Array.isArray(props.inventoryConsumable)
        ? props.inventoryConsumable.map(inventory => ({
            title: inventory.product?.reference ?? 'Sin referencia',
            value: inventory.inventory_id
        }))
        : []
);

const addReference = (dispatch) => {
    dispatch.references.push({
        reference: '',
        consumable_quantity: '',
    });
};
const removeReference = (consumable, referenceIndex) => {
    consumable.references.splice(referenceIndex, 1);
};

const showConfirmModal = ref(false);

const confirmCreate = () => {
    form.consumable.forEach((c) => {
        c.warehouse_id = props.warehouseConsumable.warehouse_id;
    });

    form.post(route('consumable.store'), {
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

    <Head title="Nuevo Registro Consumible" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Nuevo Registro Consumible</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Nuevo Registro Consumible</strong>
            </template>
            <div class="container px-0">
                <form class="table-prais">
                    <div v-for="(consumable, consumableIndex) in form.consumable" :key="consumableIndex">
                        <div>
                            <table class="table table-hover text-center">
                                <thead>
                                    <tr>
                                        <th>Referencia</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ref, refIndex) in consumable.references" :key="refIndex">
                                        <!-- Selección de referencia -->
                                        <td>
                                            <SelectSearch v-model="ref.reference" :options="optionInventory"
                                                placeholder="Selecciona referencia" />
                                        </td>

                                        <!-- Cantidad -->
                                        <td>
                                            <input type="number" v-model="ref.consumable_quantity"
                                                class="form-control w-20"
                                                :class="{ 'is-invalid': getError(consumableIndex, refIndex, 'consumable_quantity') }" />
                                        </td>

                                        <!-- Botón eliminar -->
                                        <td>
                                            <div class="removeItem" @click="removeReference(consumable, refIndex)">
                                                <i class="fa-solid fa-trash"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row text-center justify-content-center my-5">
                                <div class="addItem" @click="addReference(consumable)">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('consumable.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="showConfirmModal = true" class="px-5"
                                :disabled="!isFormValid || form.processing">
                                Registar Consumibles
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
                    <h4>¿Estás seguro de crear este registro?</h4>
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
                    <h4>La cantidad registrada supera la cantidad disponible en inventario</h4>
                </div>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>
