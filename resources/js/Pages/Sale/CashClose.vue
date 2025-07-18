<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalPrais from '@/Components/ModalPrais.vue';

// Estado para los modales
const showConfirmModal = ref(false);
const showSuccessModal = ref(false);
const shouldRedirect = ref(false);

// Método para mostrar el modal de confirmación
const confirmClose = () => {
    showConfirmModal.value = true;
};

// Método para manejar la confirmación del cierre
const handleConfirmClose = () => {
    showConfirmModal.value = false;
    submit();
};

// Método para manejar el cierre exitoso
const handleSuccess = () => {
    showSuccessModal.value = true;
    shouldRedirect.value = true;
};

// Actualizar el método submit para manejar el éxito


const props = defineProps({
    totalCash: {
        type: [Number, String],
        default: 0,
        transform: (value) => Number(value)
    },
    totalDigital: {
        type: [Number, String],
        default: 0,
        transform: (value) => Number(value)
    },
    totalSales: {
        type: [Number, String],
        default: 0,
        transform: (value) => Number(value)
    },
    cashRegisterId: {
        type: [Number, null],
        default: null
    }
});

// Calcular el total de billetes y monedas
const calculatedCash = () => {
    return (
        form.count_100_bill * 100000 +
        form.count_50_bill * 50000 +
        form.count_20_bill * 20000 +
        form.count_10_bill * 10000 +
        form.count_5_bill * 5000 +
        form.count_2_bill * 2000 +
        Number(form.total_coins)
    );
};

// Verificar si los totales coinciden (comparando en miles)


const isValidCashCount = () => {
    const counted = Number(calculatedCash());
    const expected = Number(props.totalCash);
    // Usamos una pequeña tolerancia para evitar problemas con decimales
    return Math.abs(counted - expected) < 1;
};

// Verificar si hay diferencia (en miles)
const cashDifference = () => {
    return Number(calculatedCash()) - Number(props.totalCash);
};

const form = useForm({
    cashRegisterId: props.cashRegisterId,
    count_100_bill: 0,
    count_50_bill: 0,
    count_20_bill: 0,
    count_10_bill: 0,
    count_5_bill: 0,
    count_2_bill: 0,
    total_coins: 0,
    total_digital: Number(props.totalDigital),
    total_cash: Number(props.totalCash),
    observations: ''
});

const calculatedTotal = () => {
    return Number(props.totalCash) + Number(props.totalDigital);
};

const calculateTotal = () => {
    form.total_digital = props.totalDigital;
};

onMounted(() => {
    calculateTotal();
});

const canSubmit = () => {
    return isValidCashCount() && props.cashRegisterId !== null;
};

const submit = () => {
    if (!canSubmit()) {
        return;
    }
    
    const formData = {
        ...form,
        cashRegisterId: props.cashRegisterId,
        count_100_bill: Number(form.count_100_bill),
        count_50_bill: Number(form.count_50_bill),
        count_20_bill: Number(form.count_20_bill),
        count_10_bill: Number(form.count_10_bill),
        count_5_bill: Number(form.count_5_bill),
        count_2_bill: Number(form.count_2_bill),
        total_coins: Number(form.total_coins),
        total_cash: Number(calculatedCash.value),
        total_digital: Number(form.total_digital)
    };
    
    form.post(route('cash.close'), {
        ...formData,
        onSuccess: () => {
            handleSuccess();
        },
        onError: () => {
            // Manejar error si es necesario
        }
    });

    // Verificar y redirigir si es necesario
    watch(showSuccessModal, (newVal) => {
        if (newVal === false && shouldRedirect.value) {
            window.location.href = route('sales.list');
            shouldRedirect.value = false;
        }
    });
};
</script>

<template>

    <Head title="Cierre de Caja" />

    <BaseLayout>
        <SectionCard>
            <template #headerSection>
                <strong>Cierre de Caja</strong>
            </template>

            <form class="mt-4">
                <div class="container">
                    <div class="row g-3">

                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Resumen de Ventas del Día</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <strong>Ventas en Efectivo: ${{ Number(props.totalCash).toLocaleString() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <strong>Ventas Digitales: ${{ Number(props.totalDigital).toLocaleString() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-success">
                                                <strong>Total Ventas: $ {{ Number(calculatedTotal()).toLocaleString() }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <label class="form-label">Billetes de $100.000</label>
                            <input v-model="form.count_100_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Billetes de $50.000</label>
                            <input v-model="form.count_50_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Billetes de $20.000</label>
                            <input v-model="form.count_20_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Billetes de $10.000</label>
                            <input v-model="form.count_10_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Billetes de $5.000</label>
                            <input v-model="form.count_5_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Billetes de $2.000</label>
                            <input v-model="form.count_2_bill" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Total en Monedas</label>
                            <input v-model="form.total_coins" type="number" class="form-control" min="0"
                                @input="calculateTotal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Pagos Digitales</label>
                            <input v-model="form.total_digital" type="number" class="form-control" min="0" readonly>
                        </div>


                        <div class="col-12">
                            <div class="alert" :class="{'alert-success': isValidCashCount, 'alert-danger': !isValidCashCount}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Total en Efectivo Contado: ${{ calculatedCash().toLocaleString() }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total en Efectivo Ventas: ${{ Number(props.totalCash).toLocaleString() }}</strong>
                                    </div>
                                </div>
                                <div v-if="!isValidCashCount" class="mt-2">
                                    <strong>
                                        Diferencia: ${{ Math.abs(cashDifference()).toLocaleString() }}
                                        ({{ cashDifference() > 0 ? 'Sobrante' : 'Faltante' }})
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea v-model="form.observations" class="form-control" rows="3"></textarea>
                        </div>

                        <input v-model="form.cashRegisterId" type="hidden" class="form-control">

                        <div class="row my-5">
                            <div class="col-6">
                                <PrimaryButton type="button" :href="route('sales.list')">
                                    Volver
                                </PrimaryButton>
                            </div>
                            <div class="col-6 text-end">
                                <PrimaryButton 
                                    type="button"
                                    @click="confirmClose"
                                    :disabled="!canSubmit"
                                    :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }"
                                >
                                    {{ isValidCashCount ? 
                                        (props.cashRegisterId ? 'Confirmar Cierre' : 'No hay caja abierta') : 
                                        'Totales no coinciden' }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal de Confirmación -->
            <ModalPrais v-model="showConfirmModal">
                <template #header>
                    Confirmar Cierre
                </template>
                <template #body>
                    ¿Está seguro que desea cerrar la caja?
                </template>
                <template #footer>
                    <PrimaryButton @click="showConfirmModal = false" class="me-2">
                        Cancelar
                    </PrimaryButton>
                    <PrimaryButton @click="handleConfirmClose">
                        Confirmar
                    </PrimaryButton>
                </template>
            </ModalPrais>

            <!-- Modal de Éxito -->
            <ModalPrais v-model="showSuccessModal">
                <template #header>
                    ¡Éxito!
                </template>
                <template #body>
                    <div class="alert alert-success">
                        Cierre de caja realizado exitosamente
                    </div>
                </template>
            </ModalPrais>
        </SectionCard>
    </BaseLayout>
</template>