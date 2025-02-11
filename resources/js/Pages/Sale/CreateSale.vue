<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import TextInput from '@/Components/TextInput.vue';
import { reference } from '@popperjs/core';
import axios from 'axios';

const props = defineProps({
    assessors: {
        type: Array,
    },
    inventory: {
        type: Array,
    }
});

const form = useForm({
    assessor: '',
    total: 0,
    references: [
        {
            'reference': '',
            'quantity': '',
        }
    ],
    count_50_bill: null,
    count_20_bill: null,
    count_10_bill: null,
    count_5_bill: null,
    count_2_bill: null,
    count_1_bill: null,
    total_coins: null,
});
const optionAssesors = ref(props.assessors.map((assessor) => [{ 'title': assessor.name, 'value': assessor.user_id }][0]));
const optionProducts = ref(props.inventory.map((reference) => [{ 'title': reference.product.reference, 'value': reference.inventory_id }][0]));
const showModal = ref(false);
const devolver = ref(0);
const openModal = () => {
    showModal.value = true;
    form.total = form.references.reduce((acc, reference) => acc + (reference.quantity == 30 ? 17000 : ( reference.quantity == 50 ? 25000 : (reference.quantity == 100 ? 38000 : 0))), 0);
}

const submit = () => {
    form.post(route('sales.store'), form);
    showModal.value = false;
};

// const validateChange = async() => {
//     try {
//         await axios.get(route('sales.validate', { precio: total.value, pago: ((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_1_bill * 1000) + (form.total_coins * 1)) }))
//             .then(function (response) {
//                 console.log(response);
//             });
//     } catch (error) {  
//         console.log(error);
//     }
// }

const validateChange = () => {
    try {
        devolver.value = ((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_1_bill * 1000) + (form.total_coins * 1)) - form.total;
    } catch (error) {
        devolver.value = 0;
    }
}

const addReference = () => {
    form.references.push(
        {
            'reference': '',
            'quantity': '',
        }
    );
}

const removeReference = (index) => {
    form.references.splice(index, 1);
}
</script>

<template>

    <Head title="Nueva Venta" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <!-- <Alert /> -->
            <h1>Nueva Venta</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva Venta</strong>
            </template>
            <div class="container">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                        <div class="col-md-12" style="height: 40px;">
                            <SelectSearch v-model="form.assessor" :options="optionAssesors" labelValue="Asesores"
                                :messageError="Object.keys(form.errors).length ? form.errors.supplier : null" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 mt-5">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>
                                    <SelectSearch v-model="reference['reference']" :options="optionProducts"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.reference'] : null" />
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input d-none" type="radio" v-model="reference['quantity']" :name="'quantity' + index " :id="'quantity' + index " value="30">
                                        <label class="form-check-label" :for="'quantity' + index ">
                                            <i class="fa-solid fa-flask"></i> 30 ml
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input d-none" type="radio"  v-model="reference['quantity']" :name="'quantity1' + index " :id="'quantity1' + index " value="50">
                                        <label class="form-check-label" :for="'quantity1' + index ">
                                            <i class="fa-solid fa-flask"></i> 50 ml
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input d-none" type="radio"  v-model="reference['quantity']" :name="'quantity2' + index " :id="'quantity2' + index " value="100">
                                        <label class="form-check-label" :for="'quantity2' + index">
                                            <i class="fa-solid fa-flask"></i> 100 ml
                                        </label>
                                    </div>
                                </td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addReference">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('sales.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="openModal" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Registrar Venta
                            </PrimaryButton>
                        </div>
                    </div>
                    <ModalPrais v-model="showModal" @close="showModal = false">
                        <template #header>
                            Confirmar Venta
                        </template>
                        <template #body>
                            Este es valor a pagar: ${{ form.total}}
                            <div class="row">
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_50_bill" id="count_50_bill" v-model="form.count_50_bill"
                                        :focus="form.count_50_bill != null ? true : false" labelValue="Cantidad de billetes de 50 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_20_bill" id="count_20_bill" v-model="form.count_20_bill"
                                        :focus="form.count_20_bill != null ? true : false" labelValue="Cantidad de billetes de 20 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_10_bill" id="count_10_bill" v-model="form.count_10_bill"
                                        :focus="form.count_10_bill != null ? true : false" labelValue="Cantidad de billetes de 10 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_5_bill" id="count_5_bill" v-model="form.count_5_bill"
                                        :focus="form.count_5_bill != null ? true : false" labelValue="Cantidad de billetes de 5 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_2_bill" id="count_2_bill" v-model="form.count_2_bill"
                                        :focus="form.count_2_bill != null ? true : false" labelValue="Cantidad de billetes de 2 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-6 mt-4">
                                    <TextInput type="number" name="count_1_bill" id="count_1_bill" v-model="form.count_1_bill"
                                        :focus="form.count_1_bill != null ? true : false" labelValue="Cantidad de billetes de 1 mil"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                                <div class="col-md-12 mt-4">
                                    <TextInput type="number" name="total_coins" id="total_coins" v-model="form.total_coins"
                                        :focus="form.total_coins != null ? true : false" labelValue="Cantidad de monedas"
                                        :minimo="0"
                                        :required="true" />
                                </div>
                            </div>
                            <PrimaryButton @click="validateChange" class="px-5 my-4">
                                Validar Cambio
                            </PrimaryButton>
                            <br>
                            Cantidad a devolver: ${{ devolver }}
                        </template>
                        <template #footer>
                            <PrimaryButton @click="submit" class="px-5">
                                Si
                            </PrimaryButton>
                            <PrimaryButton @click="showModal = false" class="px-5">
                                No
                            </PrimaryButton>
                        </template>
                    </ModalPrais>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>