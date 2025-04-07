<script setup>
import { Head } from '@inertiajs/vue3';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import { ref } from 'vue';
import ModalPrais from '@/Components/ModalPrais.vue';


const props = defineProps({
    location: {
        type: Object,
        required: true
    },
    cashRegister: {
        type: Object,
        required: true
    },
    sales: {
        type: Array,
        required: true
    }
});

const columnsTable = [
    {
        data: 'id',
        title: 'ID'
    },
    {
        data: 'created_at',
        title: 'Fecha',
        render: function (data) {
            return new Date(data).toLocaleString('es-CO', {
                dateStyle: 'medium',
                timeStyle: 'short'
            });
        }
    },
    {
        data: 'total',
        title: 'Total',
        render: function (data) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP'
            }).format(data);
        }
    },
    {
        data: 'user',
        title: 'Usuario'
    },
    {
        data: 'details',
        title: 'Productos',
        render: function (data, type, row) {
            return row.details.length + ' productos';
        }
    },
    {
        data: 'details',
        title: 'Ver detalles',
        render: function (data, type, row) {
            return '<i class="fa-solid fa-eye" onclick="window.showDetails(' + row.id + ')"></i>';
        }
    }
];
const selectedSale = ref(null);
const showDetailsModal = ref(false);

window.showDetails = (saleId) => {
    selectedSale.value = props.sales.find(sale => sale.id === saleId);
    showDetailsModal.value = true;
};
</script>

<template>
    <Head title="Detalle de Ventas de Sede" />
    <BaseLayout>
        <template #header>
            <h1>Detalle de Ventas de Sede</h1>
        </template>
        
        <SectionCard>
            <template #headerSection>
                <strong>{{ location.name }}</strong>
            </template>

            <div class="container">
                <div class="row my-5">
                    <Table :columns="columnsTable" :data="sales" />
                </div>
            </div>

            <div class="container">
                
                    <PrimaryButton :href="route('locations.sales', location.location_id)" class="px-5">
                        VOLVER
                    </PrimaryButton>
                </div>
            
        </SectionCard>
        
        <ModalPrais v-model="showDetailsModal" @close="showDetailsModal = false">
            <template #header>
                Detalles de Venta
            </template>
            <template #body>
                <div class="modal-body">
                    <div v-if="selectedSale">
                        <h6>Información de la Venta</h6>
                            <p><strong>Fecha:</strong> {{ new Date(selectedSale.created_at).toLocaleString('es-CO') }}</p>
                            <p><strong>Usuario:</strong> {{ selectedSale.user }}</p>
                            <p><strong>Total:</strong> {{ new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(selectedSale.total) }}</p>
                            <p><strong>Método de pago:</strong> {{ selectedSale.payment_method }}</p>
                            <p><strong>Código de transacción:</strong> {{ selectedSale.transaction_code }}</p>

                            <h6 class="mt-4">Productos</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Referencia</th>
                                            <th>Valor Unitario</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="detail in selectedSale.details" :key="detail.reference">
                                            <td>{{ detail.reference }}</td>
                                            <td>{{ new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(detail.price / detail.quantity) }}</td>
                                            <td>{{ detail.quantity }}</td>
                                            <td>{{ new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(detail.price) }}</td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                
            
        </template>
        </ModalPrais>
    </BaseLayout>
</template>

<style scoped>
.modal-backdrop.show {
    display: block;
}
</style>