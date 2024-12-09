<template>
    <Head title="Detalle de Solicitud de Insumos" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de Solicitud de Insumos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Información de la Solicitud</strong>
            </template>

            <form @submit.prevent="submit" class="space-y-6">
                <div v-for="(reference, index) in form.references" :key="index" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel :for="'reference_' + index" value="Referencia" class="required" />
                            <SelectSearch
                                :id="'reference_' + index"
                                v-model="reference.reference"
                                :options="inventories"
                                option-label="name"
                                option-value="id"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors['references.' + index + '.reference']" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel :for="'quantity_' + index" value="Cantidad" class="required" />
                            <TextInput
                                :id="'quantity_' + index"
                                type="number"
                                v-model="reference.quantity"
                                class="mt-1 block w-full"
                                min="1"
                                required
                            />
                            <InputError :message="form.errors['references.' + index + '.quantity']" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <DangerButton
                            type="button"
                            @click="removeReference(index)"
                            :disabled="form.references.length === 1"
                        >
                            Eliminar
                        </DangerButton>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <SecondaryButton type="button" @click="addReference">
                        Agregar Referencia
                    </SecondaryButton>
                </div>
            </form>
        </SectionCard>

        <div class="mt-6 flex justify-end space-x-4">
            <PrimaryButton 
                @click="handleApprove"
                :disabled="form.processing"
            >
                Aprobar
            </PrimaryButton>
            <DangerButton 
                @click="handleReject"
                :disabled="form.processing"
            >
                Rechazar
            </DangerButton>
        </div>

        <!-- Modal de Aprobación -->
        <ModalPrais
            v-model="showApproveModal"
            @close="showApproveModal = false"
        >
            <template #header>
                Confirmar Aprobación
            </template>
            
            <template #body>
                <p>¿Está seguro que desea aprobar esta solicitud?</p>
            </template>

            <template #footer>
                <SecondaryButton @click="showApproveModal = false">
                    Cancelar
                </SecondaryButton>
                <PrimaryButton @click="submitApprove" class="ms-2">
                    Aprobar
                </PrimaryButton>
            </template>
        </ModalPrais>

        <!-- Modal de Rechazo -->
        <ModalPrais
            v-model="showRejectModal"
            @close="showRejectModal = false"
        >
            <template #header>
                Rechazar Solicitud
            </template>
            
            <template #body>
                <form @submit.prevent="submitReject">
                    <div class="form-group">
                        <InputLabel for="reason" value="Motivo del Rechazo" />
                        <TextInput
                            id="reason"
                            type="textarea"
                            v-model="rejectForm.reason"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="rejectForm.errors.reason" class="mt-2" />
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="showRejectModal = false">
                    Cancelar
                </SecondaryButton>
                <DangerButton @click="submitReject" class="ms-2">
                    Rechazar
                </DangerButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>

<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    requestPrais: {
        type: Object,
        required: true
    },
    inventories: {
        type: Array,
        required: true
    }
});

// Estado para modales
const showApproveModal = ref(false);
const showRejectModal = ref(false);

// Formulario para rechazo
const rejectForm = useForm({
    reason: ''
});

// Inicializar el formulario con los datos existentes
const form = useForm({
    references: Array.isArray(props.requestPrais?.detailRequest) 
        ? props.requestPrais.detailRequest.map(detail => ({
            reference: detail.inventory_id,
            quantity: detail.quantity
        }))
        : [{ reference: '', quantity: 1 }]
});

const addReference = () => {
    form.references.push({
        reference: '',
        quantity: 1
    });
};

const removeReference = (index) => {
    if (form.references.length > 1) {
        form.references.splice(index, 1);
    }
};

const handleApprove = () => {
    showApproveModal.value = true;
};

const handleReject = () => {
    showRejectModal.value = true;
};

const submitApprove = () => {
    form.post(route('requests.validate', props.requestPrais.request_id), {
        onSuccess: () => {
            showApproveModal.value = false;
            window.location.href = route('suppliesrequest.validation');
        }
    });
};

const submitReject = () => {
    rejectForm.post(route('requests.reject', props.requestPrais.request_id), {
        onSuccess: () => {
            showRejectModal.value = false;
            window.location.href = route('suppliesrequest.validation');
        }
    });
};

const submit = () => {
    form.put(route('requests.update', props.requestPrais.request_id));
};
</script>

<style scoped>
.required:after {
    content: " *";
    color: red;
}
</style>
