<script setup>
import { ref } from 'vue';
import moment from 'moment';
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import inputcontainer from '@/Components/TextInput.vue';



const submitAudit = () => {

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
                            <h6>Dinero Reportado </h6>
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap align-middle">
                        <thead>
                            <tr>
                                <th>Dinero</th>
                                <th>Cantidad</th>
                                <th>Confirmado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'Efectivo' }}</td>
                                <td>{{ '' }}</td>
                                <th> <input type="checkbox" v-model="confirmationCash"> </th>
                            </tr>
                            <tr>
                                <td>{{ 'Digital' }}</td>
                                <td>{{ '' }}</td>
                                <th> <input type="checkbox" v-model="confirmationDigital"> </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <label for="observations">Observaciones:</label>
                    <inputcontainer v-model="observations" placeholder="Escriba sus observaciones aquí...">
                    </inputcontainer>
                </div>
                <div class="row my-5 text-center">
                    <div class="col">
                        <primary-button :href="route('audits')">Volver</primary-button>
                    </div>
                    <div class="col">
                        <PrimaryButton type="submit">Confirmar Auditoría</PrimaryButton>
                    </div>
                </div>
            </form>
        </SectionCard>
    </BaseLayout>
</template>