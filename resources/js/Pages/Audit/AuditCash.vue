<script setup>
import { ref } from 'vue';
import moment from 'moment';
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import inputcontainer from '@/Components/TextInput.vue';

const totalCash = ref(0); // Inicializa con un valor predeterminado
const totalDigital = ref(0); // Inicializa con un valor predeterminado
const confirmationCash = ref(false);
const confirmationDigital = ref(false);
const observations = ref('');

// Computed para total general
//const totalGeneral = computed(() => totalCash.value + totalDigital.value);

// Método para enviar auditoría
const submitAudit = () => {
    // Lógica para enviar auditoría
};
</script>

<template>

    <Head title="Auditoría Caja" />
    <BaseLayout>
        <template #header>
            <h1>Auditoría Caja</h1>
        </template>

        <SectionCard :subtitle="moment().format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalles de la Auditoría</strong>
            </template>


            <form @submit.prevent="submitAudit">
                <div>
                    <div class="col mb-1">
                        <div class="col-8 p-1 cardboxprais cardpurcheorder position-relative">
                            <h6>Productos Vendidos</h6>
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap align-middle">
                        <thead>
                            <tr>
                                <th>Fragancias 100 ML</th>
                                <th>Fragancias 50 ML</th>
                                <th>Fragancias 30 ML</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ }}</td>
                                <td>{{ }}</td>
                                <th>{{ }}</th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col mb-1">
                        <div class="col-8 p-1 cardboxprais cardpurcheorder position-relative">
                            <h6>Dinero Reportado </h6>
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap align-middle">
                        <thead>
                            <tr>
                                <th>Total Efectivo</th>
                                <th>Total Digital</th>
                                <th>Total General</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ totalCash }}</td>
                                <td>{{ totalDigital }}</td>
                                <th>{{ totalGeneral }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <div class="col mb-1">
                        <div class="col-8 p-1 cardboxprais cardpurcheorder position-relative">
                            <h6>Confirmaciones</h6>
                        </div>
                    </div>
                    <label>
                        <input type="checkbox" v-model="confirmationCash" />
                        Confirmar que el dinero en efectivo es correcto
                    </label>
                    <label>
                        <input type="checkbox" v-model="confirmationDigital" />
                        Confirmar que el dinero digital es correcto
                    </label>
                </div>
                <div>
                    <label for="observations">Observaciones:</label>
                    <inputcontainer v-model="observations" placeholder="Escriba sus observaciones aquí...">
                    </inputcontainer>
                </div>
                <PrimaryButton type="submit">Confirmar Auditoría</PrimaryButton>
            </form>
        </SectionCard>
    </BaseLayout>
</template>