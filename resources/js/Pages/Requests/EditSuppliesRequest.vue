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

const submit = () => {
    form.put(route('requests.update', props.requestPrais.request_id), {
        onSuccess: () => {
            window.location.href = route('suppliesrequest.validation');
        }
    });
};
</script>

<template>
    <Head title="Editar Solicitud de Insumos" />

    <BaseLayout>
        <template #header>
            <h1>Editar Solicitud de Insumos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Editar Solicitud #{{ requestPrais.request_id }}</strong>
            </template>

            <form @submit.prevent="submit" class="mt-4">
                <div class="container">
                    <!-- Información de la solicitud -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Solicitante:</strong> {{ requestPrais.user.username }}
                        </div>
                        <div class="col-md-4">
                            <strong>Sede:</strong> {{ requestPrais.user.location.name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Fecha:</strong> {{ new Date(requestPrais.created_at).toLocaleDateString() }}
                        </div>
                    </div>

                    <!-- Lista de productos -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>Productos Solicitados</h5>
                        </div>
                    </div>

                    <div v-for="(reference, index) in form.references" :key="index" class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <InputLabel :for="'product_' + index" value="Producto" required />
                                <SelectSearch
                                    :id="'product_' + index"
                                    v-model="reference.reference"
                                    :options="inventories.map(inv => ({
                                        value: inv.id,
                                        label: inv.product.name
                                    }))"
                                    placeholder="Seleccione un producto"
                                    required
                                />
                                <InputError :message="form.errors['references.' + index + '.reference']" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <InputLabel :for="'quantity_' + index" value="Cantidad" required />
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
                        <div class="col-md-2 d-flex align-items-end">
                            <DangerButton
                                type="button"
                                @click="removeReference(index)"
                                :disabled="form.references.length === 1"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </DangerButton>
                        </div>
                    </div>

                    <!-- Botón para agregar más productos -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <SecondaryButton
                                type="button"
                                @click="addReference"
                            >
                                <i class="fa-solid fa-plus"></i> Agregar Producto
                            </SecondaryButton>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <PrimaryButton
                                type="submit"
                                :disabled="form.processing"
                            >
                                Guardar Cambios
                            </PrimaryButton>
                            <SecondaryButton
                                :href="route('suppliesrequest.validation')"
                                class="ms-2"
                            >
                                Cancelar
                            </SecondaryButton>
                        </div>
                    </div>
                </div>
            </form>
        </SectionCard>
    </BaseLayout>
</template>

<style scoped>
.required:after {
    content: " *";
    color: red;
}
.form-group {
    margin-bottom: 1rem;
}
</style>
