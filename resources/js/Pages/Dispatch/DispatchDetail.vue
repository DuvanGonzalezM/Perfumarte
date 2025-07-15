<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import moment from 'moment';
import { useForm } from '@inertiajs/vue3';
import ModalPrais from '@/Components/ModalPrais.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';



const props = defineProps({
    dispatch: {
        type: Object,
        required: true,
    },
});


const groupedDispatches = computed(() => {
    const dispatches = {};
    props.dispatch.dispatchdetail.forEach(item => {
        const warehouseName = item.warehouse.location.name || 'No disponible';
        if (!dispatches[warehouseName]) {
            dispatches[warehouseName] = [];
        }
        dispatches[warehouseName].push(item);
    });
    return dispatches;
});
const showModalSuccess = ref(false);
const showModalError = ref(false);
const form = useForm({
  dispatch_id: props.dispatch.dispatch_id,
  status: props.dispatch.status,
  details: props.dispatch.dispatchdetail.map(detail => ({
    id: detail.id,
    returned_quantity: detail.returned_quantity ?? 0,
    dispatched_quantity: detail.dispatched_quantity,
    observations: detail.observations,
    received: detail.received,
    inventory: detail.inventory, 
  }))
});

const approved = () => {
    form.put(route('dispatch.approved', props.dispatch.dispatch_id), {
        status: 'En ruta',
        onSuccess() {
            showModalSuccess.value = true;
        },
        onError() {
            showModalError.value = true;
        }
    });
};

const submitReturnedQuantities = () => {
  
  form.details = [];

  Object.values(groupedDispatches.value).forEach(items => {
  if (Array.isArray(items)) {
    items.forEach(item => {
        form.details.push({
        id: item.dispatchs_detail_id,
        returned_quantity: item.returned_quantity
      });
    });
  } else {
    console.warn('Valor inesperado en groupedDispatches:', items);
  }
});

  form.dispatch_id = props.dispatch.dispatch_id; 

  form.put(route('dispatchReturn.store', form.dispatch_id), {
    onSuccess: () => {
      showModalSuccess.value = true;
    },
    onError: () => {
      showModalError.value = true;
    }
  });
};




</script>

<template>

    <Head :title="'Detalle del despacho ' + dispatch.dispatch_id" />

    <BaseLayout :loading="form.processing">
        <template #header>
            <h1>Detalle del despacho</h1>
        </template>

        <SectionCard :idSection="dispatch.dispatch_id" :subtitle="dispatch.status"
            :subextra="moment(dispatch.created_at).format('DD/MM/Y')">
            <template #headerSection>
                <strong>Detalle del despacho</strong>
            </template>

            <div class="container">
                <div v-for="(items, warehouseName) in groupedDispatches" :key="warehouseName" class="my-4">
                    <div class="row">
                        <div class="col-12 supplier-info">
                            <div class="info-card">
                                <h6>Sede: {{ warehouseName }}</h6>
                            </div>
                        </div>
                        <table class="table table-hover text-center dt-body-nowrap align-middle">
                            <thead>
                                <tr>
                                    <th>Referencia</th>
                                    <th>Cantidad Despachada</th>
                                    <th>Recibido</th>
                                    <th>Cantidad Devuelta</th>
                                    <th>Observaciones</th>
                             
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in items" :key="index">
                                    <td>{{ item.inventory.product.reference }}</td>
                                    <td>{{ item.dispatched_quantity }} {{
                                        item.inventory.product.measurement_unit.replace('KG',
                                            'ml') }}</td>
                                    <th><i v-if="item.received === 1" class="fa-solid fa-circle-check"></i>
                                        <i v-else class="fa-regular fa-circle"></i>
                                    </th>
                                    <td>
                                    <template v-if="can('Aprobar Despachos')">
                                    <TextInput :id="`returned_quantity_${index}`" type="number" name="returned_quantity[]"v-model="item.returned_quantity":placeholder="item.inventory.product.measurement_unit.replace('KG', 'ml')" />
                                    </template>
                                    <template v-else>
                                    <span>{{ item.returned_quantity }}</span>
                                    </template>
                                    </td>
                                    <th>{{ item.observations }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row my-5">
                    <div class="col-12">
                      <div
                        v-if="can('Aprobar Despachos')"
                        class="d-flex justify-content-between"
                      >
                        <PrimaryButton :href="route('dispatch.list')" class="px-5">
                          Volver
                        </PrimaryButton>

                        <PrimaryButton
                          v-if="dispatch.status === 'En aprobacion'"
                          @click="approved"
                          class="px-5"
                          :disabled="form.processing"
                        >
                          Aprobar
                        </PrimaryButton>

                        <PrimaryButton
                          v-else-if="dispatch.status === 'Recibido'"
                          @click="submitReturnedQuantities"
                          :disabled="form.processing"
                        >
                          Confirmar Devolución
                        </PrimaryButton>
                      </div>

                      <div v-else class="d-flex justify-content-center w-100">
                    <div class="text-center">
                      <PrimaryButton :href="route('dispatch.list')" class="px-5">
                        Volver
                       </PrimaryButton>
                    </div>
                  </div>
                </div>
            </div>
            </div>
        </SectionCard> 
    </BaseLayout>
    <ModalPrais v-model="showModalSuccess" @close="showModalSuccess = false">
        <template #header>
            Despacho Aprobado
        </template>
        <template #body>
            El despacho ha sido aprobado correctamente.
        </template>
    </ModalPrais>
    <ModalPrais v-model="showModalError" @close="showModalError = false">
        <template #header>
            Error al aprobar el despacho
        </template>
        <template #body>
            Ha ocurrido un error al aprobar el despacho. Por favor verifique cantidades y existencias e intente nuevamente.
        </template>
    </ModalPrais>
</template>
