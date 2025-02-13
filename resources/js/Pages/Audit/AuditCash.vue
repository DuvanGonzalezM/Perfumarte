<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import inputcontainer from '@/Components/TextInput.vue';
import { defineProps } from '@vue/runtime-core';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    cashSales: {
        type: Array,
        default: () => []
    },
    digitalSales: {
        type: Array,
        default: () => []
    },
    location_id: {
        type: Number,
        required: true
    },
    location_name: {
        type: String,
        required: true
    },

});

const totalCashSales = computed(() =>
    props.cashSales.reduce((total, sale) => total + sale.total, 0)
);

const totalDigitalSales = computed(() =>
    props.digitalSales.reduce((total, sale) => total + sale.total, 0)
);

const form = useForm({
    location_id: props.location_id,
    total_cash_sales: totalCashSales.value,
    total_digital_sales: totalDigitalSales.value,
    observations: '',
    confirmationCash: false,
    confirmationDigital: false
});

const submitAudit = () => {
    form.post("#", {
        onSuccess: () => {
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};
</script>

<template>
    <Head title="Auditoría Caja" />
    <BaseLayout>
        <template #header>
            <h1>Auditoría Caja</h1>
        </template>
        <SectionCard :subtitle="moment().format('DD/MM/Y')" :subextra=" props.location_name">
            <template #headerSection>
                <strong>Detalles de la Auditoría</strong>
            </template>
            <form @submit.prevent="submitAudit">
                <div>
                    <div class="col mb-1">
                        <div class="col-8 p-1 cardboxprais cardpurcheorder position-relative">
                            <h6>Dinero Reportado </h6>
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap align-middle">
                        <thead>
                            <tr>
                                <th>DINERO</th>
                                <th>CANTIDAD</th>
                                <th>CONFIRMACION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Efectivo</td>
                                <td>{{ totalCashSales.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                    }}</td>
                                <td>    <input type="checkbox" v-model="form.confirmationCash"> </td>
                            </tr>
                            <tr>
                                <td>Digital</td>
                                <td>{{ totalDigitalSales.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 })
                                    }}</td>
                                <td>    <input type="checkbox" v-model="form.confirmationDigital"> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <label for="observations">Observaciones:</label>
                    <inputcontainer v-model="form.observations" placeholder="Escriba sus observaciones aquí...">
                    </inputcontainer>
                </div>
                <div class="row my-5 text-center">
                    <div class="col">
                        <primary-button :href="route('audits')">Volver</primary-button>
                    </div>
                    <div class="col">
                        <PrimaryButton @click="submitAudit" :disabled="form.processing">Confirmar Auditoría
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </SectionCard>
    </BaseLayout>
</template>