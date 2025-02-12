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
import CountControl from '@/Components/CountControl.vue';

const props = defineProps({
    assessors: {
        type: Array,
    },
    inventory: {
        type: Array,
    },
    warehouse: {
        type: Object,
    }
});

const form = useForm({
    assessor: '',
    total: 0,
    pay_method: '',
    transaction_code: null,
    references: [],
    count_100_bill: 0,
    count_50_bill: 0,
    count_20_bill: 0,
    count_10_bill: 0,
    count_5_bill: 0,
    count_2_bill: 0,
    total_coins: 0,
});
const optionAssesors = ref(props.assessors.map((assessor) => [{ 'title': assessor.name, 'value': assessor.user_id }][0]));
const optionProducts = ref(props.inventory.map((reference) => [{ 'title': reference.product.commercial_reference, 'value': reference.inventory_id }][0]));
const optionPayMethod = ref([{ 'title': 'Efectivo', 'value': 'Efectivo' }, { 'title': 'Transferencia', 'value': 'Transferencia' }]);
const showModal = ref(false);
const showModalReference = ref(false);
const showModalChange = ref(false);
const referenceNew = ref({ 'reference': '', 'quantity': '', 'units': 1, 'perdurable': [] });
const devolver = ref(0);
const questionPerdurable = ref(false);
const openModal = () => {
    showModal.value = true;
    form.total = form.references.reduce((acc, reference) => acc + (reference.units * priceReference(reference.quantity) + reference.perdurable.reduce((a, b) => a + Number(b), 0) * props.warehouse.price_drops), 0);
    form.pay_method = '';
    form.transaction_code = null;
}
const openModalChange = () => {
    showModalChange.value = true;
    showModal.value = false;
    try {
        devolver.value = ((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_100_bill * 100000) + (form.total_coins * 1)) - form.total;
    } catch (error) {
        devolver.value = 0;
    }
}

const submit = () => {
    form.post(route('sales.store'), form);
    showModal.value = false;
};

const validateChange = async() => {
    try {
        await axios.get(route('sales.validate', { precio: form.total, pago: ((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_1_bill * 1000) + (form.total_coins * 1)) }))
            .then(function (response) {
                console.log(response);
            });
    } catch (error) {  
        console.log(error);
    }
}

// const validateChange = () => {
//     try {
//         devolver.value = ((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_100_bill * 100000) + (form.total_coins * 1)) - form.total;
//     } catch (error) {
//         devolver.value = 0;
//     }
// }
const changeReference = () => {
    referenceNew.value.quantity = '';
    referenceNew.value.units = 1;
    referenceNew.value.perdurable = [];
    questionPerdurable.value = false;
}

const changePayMethod = () => {
    form.count_100_bill = 0;
    form.count_50_bill = 0;
    form.count_20_bill = 0;
    form.count_10_bill = 0;
    form.count_5_bill = 0;
    form.count_2_bill = 0;
    form.total_coins = 0;
    form.transaction_code = null;
}

const changePerdurable = () => {
    referenceNew.value.perdurable = [];
}

const addReferenceModal = () => {
    showModalReference.value = true;
    referenceNew.value = { 'reference': '', 'quantity': '', 'units': 1, 'perdurable': [] };
}

const priceReference = (value) => {
    if (value == 30) {
        return props.warehouse.price30;
    } else if (value == 50) {
        return props.warehouse.price50;
    } else if (value == 100) {
        return props.warehouse.price100;
    } else {
        return 0;
    }
}
const addReference = () => {
    form.references.push(
        {
            'reference': referenceNew.value.reference,
            'quantity': referenceNew.value.quantity,
            'units': referenceNew.value.units,
            'perdurable': referenceNew.value.perdurable,
        }
    );
    showModalReference.value = false;
    referenceNew.value = { 'reference': '', 'quantity': '', 'units': 1, 'perdurable': [] };
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
                            <SelectSearch v-model="form.assessor" :options="optionAssesors"
                                labelValue="Asesor de la venta"
                                :messageError="Object.keys(form.errors).length ? form.errors.supplier : null" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-5 mt-5">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>PRESENTACION</th>
                                <th>UNIDAD(ES)</th>
                                <th>N° GOTAS</th>
                                <th>PRECIO</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>{{ props.inventory.find(item => item.inventory_id === reference.reference)?.product.commercial_reference }}</td>
                                <td>{{ reference.quantity }}</td>
                                <td>{{ reference.units }}</td>
                                <td>{{ reference.perdurable.reduce((a, b) => a + Number(b), 0) }}</td>
                                <td>$ {{ reference.units * priceReference(reference.quantity) + reference.perdurable.reduce((a, b) => a + Number(b), 0) * props.warehouse.price_drops}}</td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addReferenceModal">
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
                            <PrimaryButton @click="openModal" class="px-5" :class="form.processing || form.references.length == 0 ? 'disabled' : ''">
                                Registrar Venta
                            </PrimaryButton>
                        </div>
                    </div>

                    <ModalPrais v-model="showModalReference" @close="showModalReference = false">
                        <template #header>
                            Agregar Referencia
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-md-12">
                                    <SelectSearch v-model="referenceNew['reference']" labelValue="Referencia"
                                        :options="optionProducts"
                                        :changeFunction="changeReference"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + '.reference'] : null" />
                                </div>
                                <div class="col-md-12 row mt-3 justify-content-center" v-if="referenceNew['reference']">
                                    <label
                                        class="form-check prais-radio row col-md-2 m-4 d-flex justify-content-center form-check-label p-2 pt-3"
                                        :for="'quantity'">
                                        <i class="fa-solid fa-flask d-flex justify-content-center"></i>30 ml
                                        <input class="form-check-input d-none" type="radio"
                                            v-model="referenceNew['quantity']" :name="'quantity'" :id="'quantity'"
                                            value="30">
                                    </label>
                                    <label
                                        class="form-check prais-radio row col-md-2 m-4 d-flex justify-content-center form-check-label p-1 pt-3"
                                        :for="'quantity1'">
                                        <i class="fa-solid fa-flask d-flex justify-content-center fs-4"></i>50 ml
                                        <input class="form-check-input d-none" type="radio"
                                            v-model="referenceNew['quantity']" :name="'quantity1'" :id="'quantity1'"
                                            value="50">
                                    </label>
                                    <label
                                        class="form-check prais-radio row col-md-2 m-4 d-flex justify-content-center form-check-label p-1 pt-3"
                                        :for="'quantity2'">
                                        <i class="fa-solid fa-flask d-flex justify-content-center fs-2"></i>100 ml
                                        <input class="form-check-input d-none" type="radio"
                                            v-model="referenceNew['quantity']" :name="'quantity2'" :id="'quantity2'"
                                            value="100">
                                    </label>
                                    <div class="col-md-12 mt-2 row">
                                        <div class="col-md-6">
                                            <CountControl v-model="referenceNew['units']" :min="1" title="Unidad(es)" />
                                        </div>
                                        <div class="col-md-6 row form-check prais-switch form-switch d-flex justify-content-center">
                                            <label class="form-check-label ms-2" for="questionPerdurable">¿Agregar gotas de perduración?</label>
                                            <input class="form-check-input fs-4" type="checkbox" id="questionPerdurable"
                                                v-model="questionPerdurable" @change="changePerdurable">
                                        </div>
                                        <div class="row d-flex justify-content-center mt-3" v-if="questionPerdurable" v-for="(unidad, index) in Array.from({length: referenceNew['units']}, (v, k) => k + 1)">
                                            Unidad {{ unidad }}
                                            <label
                                                class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index+'perdurable'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>5
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]" :name="index+'perdurable'"
                                                    :id="index+'perdurable'" value="5">
                                            </label>
                                            <label
                                                class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index+'perdurable1'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>10
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]" :name="index+'perdurable1'"
                                                    :id="index+'perdurable1'" value="10">
                                            </label>
                                            <label
                                                class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index+'perdurable2'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>15
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]" :name="index+'perdurable2'"
                                                    :id="index+'perdurable2'" value="15">
                                            </label>
                                            <label
                                                class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index+'perdurable3'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>20
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]" :name="index+'perdurable3'"
                                                    :id="index+'perdurable3'" value="20">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 d-flex justify-content-center">
                                        <PrimaryButton @click="addReference" :class="referenceNew['quantity'] ? '' : 'disabled'" class="px-5">
                                            Agregar Referencia
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div></div>
                        </template>
                    </ModalPrais>

                    <ModalPrais v-model="showModal" @close="showModal = false">
                        <template #header>
                            Valor a pagar: ${{ form.total }}
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-md-12">
                                    <SelectSearch :changeFunction="changePayMethod" v-model="form.pay_method" labelValue="Metodo de pago" :options="optionPayMethod" />
                                </div>
                                <div class="row" v-if="form.pay_method == 'Efectivo'">
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_100_bill" :min="0" title="N° Billetes 100 mil" />
                                    </div>
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_50_bill" :min="0" title="N° Billetes 50 mil" />
                                    </div>
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_20_bill" :min="0" title="N° Billetes 20 mil" />
                                    </div>
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_10_bill" :min="0" title="N° Billetes 10 mil" />
                                    </div>
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_5_bill" :min="0" title="N° Billetes 5 mil" />
                                    </div>
                                    <div class="col-md-4 my-3">
                                        <CountControl v-model="form.count_2_bill" :min="0" title="N° Billetes 2 mil" />
                                    </div>
                                    <div class="col-md-12 my-3">
                                        <TextInput type="number" name="total_coins" id="total_coins"
                                            v-model="form.total_coins" :focus="form.total_coins != null ? true : false"
                                            labelValue="Cantidad total de monedas" :minimo="0" :required="true" />
                                    </div>
                                    <div class="col-md-12 my-4 d-flex justify-content-center ">
                                        <PrimaryButton @click="openModalChange" class="px-5">
                                            Cobrar
                                        </PrimaryButton>
                                    </div>
                                </div>
                                <div class="row" v-if="form.pay_method == 'Transferencia'">
                                    <div class="col-md-12 my-4 d-flex justify-content-center">
                                        <TextInput type="text" name="transaction_code" id="transaction_code"
                                            v-model="form.transaction_code" :focus="form.transaction_code != null ? true : false"
                                            labelValue="Codigo de transaccion" :minimo="0" :required="true" />
                                    </div>
                                    <div class="col-md-12 my-4 d-flex justify-content-center ">
                                        <PrimaryButton @click="submit" class="px-5">
                                            Pagar
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div></div>
                        </template>
                    </ModalPrais>

                    <ModalPrais v-model="showModalChange" @close="showModalChange = false">
                        <template #body>
                            <h3>Cantidad a devolver: ${{ devolver }}</h3>
                            <div class="col-md-12 my-4 d-flex justify-content-center ">
                                <PrimaryButton @click="submit" class="px-5">
                                    Pagar
                                </PrimaryButton>
                            </div>
                        </template>
                        <template #footer>
                            <div></div>
                        </template>
                    </ModalPrais>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>