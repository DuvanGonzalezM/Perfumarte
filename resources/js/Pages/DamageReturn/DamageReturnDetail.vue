<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';
import TextInput from '@/Components/TextInput.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { ref } from 'vue';

const props = defineProps({
    damageReturn: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    details: props.damageReturn.damagereturndetail.map(damageReturnDetail => ({
        damage_return_detail_id: damageReturnDetail.damage_return_detail_id,
        inventory_id: damageReturnDetail.inventory_id,
        warehouse_id: damageReturnDetail.warehouse_id,
        quantity: damageReturnDetail.damage_quantity,
        received: Boolean(damageReturnDetail.received),
        observations: damageReturnDetail.observations,
        discarded: false,
    })),
});

const showErrorsModal = ref(false);
const showConfirmModal = ref(false);
const approveReturn = () => {
    form.post(
        route('damageReturn.approved', props.damageReturn.damage_return_id),
        {
            onSuccess: () => {
                showConfirmModal.value = false;
            },
            onError: () => {
                showErrorsModal.value = true;
                showConfirmModal.value = false;

            }
        }
    );
};

const showApproveModal = ref(false);

const approveReturnFinal = () => {
    form.put(
        route('returnFinal.approved', props.damageReturn.damage_return_id),
        {
            onSuccess: () => {
                showApproveModal.value = false;
            },
            onError: () => {
                showErrorsModal.value = true;
                showApproveModal.value = false;
            }
        }
    );
};

</script>

<template>

    <Head :title="'Detalle de la devolución ' + damageReturn.damage_return_id" />

    <BaseLayout>
        <template #header>
            <h1>Detalle de la devolución</h1>
        </template>

        <SectionCard :idSection="damageReturn.damage_return_id" :subtitle="can('Confirmar Devoluciones')
            ? (damageReturn.damagereturndetail[0]?.warehouse?.location?.name || 'Sede no disponible')
            : damageReturn.status" :subextra="moment(damageReturn.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle de la devolución</strong>
            </template>

            <div class="container">
                <table class="table table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>Referencia</th>
                            <th>Cantidad Devuelta</th>
                            <th>Recibido</th>
                            <th>Observaciones</th>
                            <th v-if="can('Aprobar Devoluciones')">Dar de baja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in form.details" :key="index">
                            <td>{{ damageReturn.damagereturndetail[index].inventory?.product?.reference ?? 'N/A' }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>
                                <input type="checkbox" v-model="item.received"
                                    :disabled="props.damageReturn.status.trim().toLowerCase() !== 'confirmar' || !can('Confirmar Devoluciones')" />
                            </td>
                            <td>
                                <template
                                    v-if="props.damageReturn.status.trim().toLowerCase() === 'confirmar' && can('Confirmar Devoluciones')">
                                    <TextInput :id="`observations_${index}`" type="text" name="observations[]"
                                        v-model="form.details[index].observations" placeholder="Observaciones" />
                                </template>
                                <template v-else>
                                    {{ item.observations }}
                                </template>
                            </td>

                            <td v-if="can('Aprobar Devoluciones')">
                                <input type="checkbox" v-model="item.discarded" />
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex my-5" :class="[
                    (props.damageReturn.status === 'Confirmar' && can('Confirmar Devoluciones')) ||
                        (props.damageReturn.status === 'En aprobacion' && can('Aprobar Devoluciones'))
                        ? 'justify-content-between'
                        : 'justify-content-center'
                ]">
                    <!-- Botón Volver -->
                    <PrimaryButton :href="route('damageReturn.list')" class="px-5">
                        Volver
                    </PrimaryButton>

                    <!-- Botón Confirmar (solo en estado Confirmar) -->
                    <template v-if="props.damageReturn.status === 'Confirmar' && can('Confirmar Devoluciones')">
                        <PrimaryButton class="px-5" @click="showConfirmModal = true">
                            Confirmar Devolución
                        </PrimaryButton>
                    </template>

                    <!-- Botón Aprobar (solo en estado En aprobación) -->
                    <template v-else-if="props.damageReturn.status === 'En aprobacion' && can('Aprobar Devoluciones')">
                        <PrimaryButton class="px-5" @click="showApproveModal = true">
                            Aprobar Devolución
                        </PrimaryButton>
                    </template>
                </div>
            </div>
        </SectionCard>
        <ModalPrais v-model="showConfirmModal" @close="showConfirmModal = false">
            <template #header>
                Confirmar Devolución
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de confirmar esta devolución?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="approveReturn" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Confirmar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>

        <ModalPrais v-model="showApproveModal" @close="showApproveModal = false">
            <template #header>
                Aprobar Devolución
            </template>
            <template #body>
                <div class="text-center">
                    <h4>¿Estás seguro de aprobar y dar de baja los productos marcados?</h4>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="approveReturnFinal" class="px-5" :disabled="form.processing">
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Aprobar</span>
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>