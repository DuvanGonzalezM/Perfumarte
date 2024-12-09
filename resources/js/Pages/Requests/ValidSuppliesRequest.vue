<template>

    <Head title="Validar Solicitudes de Insumos" />

    <BaseLayout>
        <template #header>
            <h1>Validar Solicitudes de Insumos</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Lista de Solicitudes</strong>
            </template>

            <Table :columns="columnsTable" :data="suppliesRequest" class="table-prais" />
        </SectionCard>

        <!-- Modal de Aprobación -->
        <ModalPrais v-model="showApproveModal" @close="showApproveModal = false">
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
        <ModalPrais v-model="showRejectModal" @close="showRejectModal = false">
            <template #header>
                Rechazar Solicitud
            </template>

            <template #body>
                <form @submit.prevent="submitReject">
                    <div class="form-group">
                        <InputLabel for="reason" value="Motivo del Rechazo" />
                        <TextInput id="reason" type="textarea" v-model="rejectForm.reason" class="mt-1 block w-full"
                            required />
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
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import moment from 'moment';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    suppliesRequest: {
        type: Array,
        required: true
    }
});

// Estado para modales
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const selectedRequestId = ref(null);

// Formulario para rechazo
const rejectForm = useForm({
    reason: ''
});

// Métodos para manejar las acciones
const handleEdit = (requestId) => {
    router.get(route('requests.edit', requestId));
};

const handleApprove = (requestId) => {
    selectedRequestId.value = requestId;
    showApproveModal.value = true;
};

const handleReject = (requestId) => {
    selectedRequestId.value = requestId;
    showRejectModal.value = true;
};

const handleView = (requestId) => {
    router.push(route('requests.detail', requestId));
};

const submitApprove = () => {
    router.post(route('requests.validate', selectedRequestId.value), {}, {
        onSuccess: () => {
            showApproveModal.value = false;
            selectedRequestId.value = null;
        }
    });
};

const submitReject = () => {
    rejectForm.post(route('requests.reject', selectedRequestId.value), {
        onSuccess: () => {
            showRejectModal.value = false;
            selectedRequestId.value = null;
            rejectForm.reset();
        }
    });
};

// Configuración de la tabla
const columnsTable = [
    {
        data: 'request_id',
        title: 'CODIGO SOLICITUD'
    },
    {
        data: 'user.username',
        title: 'SOLICITADO POR'
    },
    {
        data: 'user.location.name',
        title: 'SEDE'
    },
    {
        data: "created_at",
        title: 'FECHA DE SOLICITUD',
        render: (data) => moment(data).format('DD/MM/Y')
    },
    {
        data: "status",
        title: 'ESTADO',
        render: (data) => {
            switch (data) {
                case 'pending_approval':
                    return '<span class="text-warning">Pendiente de Aprobación</span>';
                case 'approved':
                    return '<span class="text-success">Aprobada</span>';
                case 'rejected':
                    return '<span class="text-danger">Rechazada</span>';
                default:
                    return data;
            }
        }
    },
    {
        data: 'request_id',
        title: 'DETALLE',
        render: (data) => {
            return '<a href="' + route("requests.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        }
    }
    ];

// Manejador de eventos para los botones de acción
const handleTableClick = (event) => {
    const button = event.target.closest('.action-btn');
    if (!button) return;

    const action = button.dataset.action;
    const id = button.dataset.id;

    console.log('Action:', action); // Para debug
    console.log('ID:', id); // Para debug

    if (action === 'view') {
        handleView(id);
    }
};

// Agregar el listener cuando el componente se monte
onMounted(() => {
    // Asegurarnos de que la tabla esté renderizada
    setTimeout(() => {
        const table = document.querySelector('.table-prais');
        if (table) {
            table.addEventListener('click', handleTableClick);
        }
    }, 100);
});

// Limpiar el listener cuando el componente se desmonte
onUnmounted(() => {
    const table = document.querySelector('.table-prais');
    if (table) {
        table.removeEventListener('click', handleTableClick);
    }
});
</script>

<style scoped>
.modal {
    background-color: rgba(0, 0, 0, 0.5);
}
</style>