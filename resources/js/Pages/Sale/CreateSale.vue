<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, computed } from 'vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import TextInput from '@/Components/TextInput.vue';
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
    container: null,
    transaction_code: null,
    references: [],
    count_100_bill: 0,
    count_50_bill: 0,
    count_20_bill: 0,
    count_10_bill: 0,
    count_5_bill: 0,
    count_2_bill: 0,
    total_coins: null,
    rest_count_100_bill: 0,
    rest_count_50_bill: 0,
    rest_count_20_bill: 0,
    rest_count_10_bill: 0,
    rest_count_5_bill: 0,
    rest_count_2_bill: 0,
    rest_total_coins: null,
});

const containerNames = {
    '30': props.inventory.filter((reference) => (reference.product_id == 385 || reference.product_id == 386) && reference.quantity > 0).map((reference) => [{ 'title': `${reference.product.commercial_reference}`, 'value': reference.product_id }][0]),
    '50': props.inventory.filter((reference) => (reference.product_id == 388 || reference.product_id == 389 || reference.product_id == 390 || reference.product_id == 391) && reference.quantity > 0).map((reference) => [{ 'title': `${reference.product.commercial_reference}`, 'value': reference.product_id }][0]),
    '100': props.inventory.filter((reference) => (reference.product_id == 392 || reference.product_id == 393 || reference.product_id == 394) && reference.quantity > 0).map((reference) => [{ 'title': `${reference.product.commercial_reference}`, 'value': reference.product_id }][0]),
};
const giftBag = 372;
const optionAssesors = ref(props.assessors.map((assessor) => [{ 'title': assessor.name, 'value': assessor.user_id }][0]));
const optionProducts = ref(props.inventory.filter((reference) => (reference.product.category != 'Insumo' && reference.quantity > 0) || reference.product_id == giftBag).map((reference) => [{ 'title': `${reference.product.commercial_reference} - ${reference.product.category}`, 'value': reference.inventory_id }][0]));
const optionPayMethod = ref([{ 'title': 'Efectivo', 'value': 'Efectivo' }, { 'title': 'Transferencia', 'value': 'Transferencia' }]);
const showModalReference = ref(false);
const showModal = ref(false);
const showModalChange = ref(false);

const referenceNew = ref({ 'reference': '', 'quantity': '', 'units': 1, 'perdurable': [] });
const devolver = ref(0);
const questionPerdurable = ref(false);
const openModal = () => {
    showModal.value = true;
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
    form.post(route('sales.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.log('Error en la validación');
        },
    });
};

const validateChange = async () => {
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
    form.total_coins = null;
    form.rest_count_100_bill = 0;
    form.rest_count_50_bill = 0;
    form.rest_count_20_bill = 0;
    form.rest_count_10_bill = 0;
    form.rest_count_5_bill = 0;
    form.rest_count_2_bill = 0;
    form.rest_total_coins = null;
    form.transaction_code = null;
}

const changePerdurable = () => {
    // Inicializar el array de perdurables con 0 para cada unidad
    referenceNew.value.perdurable = Array(referenceNew.value.units).fill(0);
}

const togglePerdurable = (index, value) => {
    // Si ya está seleccionado, lo deseleccionamos
    if (referenceNew.value.perdurable[index] === value) {
        referenceNew.value.perdurable[index] = 0;
    } else {
        referenceNew.value.perdurable[index] = value;
    }
}

const buttonErrorMessage = ref('');

// Validar si hay referencias de 5ml con menos de 12 unidades en total
const hasValidReferences = computed(() => {
    // Sumar todas las unidades de referencias de 5ml
    const totalUnits5ml = form.references
        .filter(ref => ref.quantity === 5)
        .reduce((acc, ref) => acc + ref.units, 0);
    
    if (totalUnits5ml < 12 && totalUnits5ml > 0) {
        buttonErrorMessage.value = 'La cantidad total de 5ml debe ser al menos 12 unidades';
        return false;
    }
    buttonErrorMessage.value = '';
    return true;
});

const addReferenceModal = () => {
    showModalReference.value = true;
    referenceNew.value = { 
        'reference': '', 
        'quantity': '', 
        'units': 1, 
        'container': null,
        'perdurable': Array(1).fill(0) // Inicializar con 0 para permitir deselección
    };
}

const priceReference = (value, productId=null) => {
    const totalUnitsBySize = {
        30: form.references.filter(ref => ref.quantity == 30).reduce((acc, ref) => acc + ref.units, 0),
        50: form.references.filter(ref => ref.quantity == 50).reduce((acc, ref) => acc + ref.units, 0),
        100: form.references.filter(ref => ref.quantity == 100).reduce((acc, ref) => acc + ref.units, 0)
    };
    
    if (value == 30 && totalUnitsBySize[30] >= 12) {
        return props.warehouse.price30 - 1000;
    } else if (value == 30) {
        return props.warehouse.price30;
    } else if (value == 50 && totalUnitsBySize[50] >= 12) {
        return props.warehouse.price50 - 2000;
    } else if (value == 50) {
        return props.warehouse.price50;
    } else if (value == 100 && totalUnitsBySize[100] >= 12) {
        return props.warehouse.price100 - 2000;
    } else if (value == 100) {
        return props.warehouse.price100;
    } else if (value == 5) {
        const totalUnits5ml = form.references.filter(ref => ref.quantity == 5).reduce((acc, ref) => acc + ref.units, 0);
        
        if (totalUnits5ml >= 50) {
            // Si hay 50 o más unidades, calcular descuentos
            const discountedUnits = 50; // Máximo 50 unidades con descuento
            const regularUnits = totalUnits5ml - discountedUnits; // Unidades a precio normal
            
            // Precio total: 105000 para las 50 primeras + precio normal para las demás
            const totalDiscountedPrice = 105000;
            const totalRegularPrice = regularUnits * 2100; // Precio normal para 50+ unidades
            
            return (totalDiscountedPrice + totalRegularPrice) / totalUnits5ml;
        } else if (totalUnits5ml >= 25) {
            // Si hay 25 o más unidades, calcular descuentos
            const discountedUnits = 25; // Máximo 25 unidades con descuento
            const regularUnits = totalUnits5ml - discountedUnits; // Unidades a precio normal
            
            // Precio total: 66000 para las 25 primeras + precio normal para las demás
            const totalDiscountedPrice = 66000;
            const totalRegularPrice = regularUnits * 2700; // Precio normal para 25-49 unidades
            
            return (totalDiscountedPrice + totalRegularPrice) / totalUnits5ml;
        } else if (totalUnits5ml >= 12) {
            // Si hay 12 o más unidades, calcular descuentos
            const discountedUnits = 12; // Máximo 12 unidades con descuento
            const regularUnits = totalUnits5ml - discountedUnits; // Unidades a precio normal
            
            // Precio total: 38000 para las 12 primeras + precio normal para las demás
            const totalDiscountedPrice = 38000;
            const totalRegularPrice = regularUnits * 3200; // Precio normal para 12-24 unidades
            
            return (totalDiscountedPrice + totalRegularPrice) / totalUnits5ml;
        }
        
        // Para menos de 12 unidades, usar precio base
        return props.warehouse.price5;
    } else {
        if (productId == giftBag){
            return 2000;
        }
        return 0;
    }
}

const updateTotal = () => {
    form.total = form.references.reduce((acc, reference) => {
        let product_id = null;
        if(props.inventory.find(item => item.inventory_id === reference.reference)?.product_id == giftBag){
            product_id = giftBag;
        }
        const price = priceReference(reference.quantity, product_id);
        const dropsPrice = reference.perdurable.reduce((a, b) => a + Number(b), 0) * props.warehouse.price_drops;
        return acc + (reference.units * price + dropsPrice);
    }, 0);
}

const addReference = () => {
    // Convertir quantity a número si es string
    let quantity = typeof referenceNew.value.quantity === 'string' ? parseInt(referenceNew.value.quantity) : referenceNew.value.quantity;
    
    form.references.push(
        {
            'reference': referenceNew.value.reference,
            'quantity': quantity,
            'units': referenceNew.value.units,
            'container': referenceNew.value.container,
            'perdurable': referenceNew.value.perdurable,
        }
    );
    
    // Actualizar el total
    updateTotal();
    
    showModalReference.value = false;
    referenceNew.value = { 
        'reference': '', 
        'quantity': '', 
        'units': 1, 
        'container': null,
        'perdurable': Array(1).fill(0)
    };
};

const removeReference = (index) => {
    form.references.splice(index, 1);

    form.total = form.references.reduce((acc, reference) => {
        let product_id = null;
        if(props.inventory.find(item => item.inventory_id === reference.reference)?.product_id == giftBag){
            product_id = giftBag;
        }
        const price = priceReference(reference.quantity, product_id);
        const dropsPrice = reference.perdurable.reduce((a, b) => a + Number(b), 0) * props.warehouse.price_drops;
        return acc + (reference.units * price + dropsPrice);
    }, 0);
};

const changeQuantity = (reference) => {
    reference.container = null;
}
</script>

<template>

    <Head title="Nueva Venta" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <!-- <Alert /> -->
            <h1>Nueva Venta</h1>
        </template>
        <SectionCard :subextra="'Valor total: ' + Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(form.total)">
            <template #headerSection>   
                <strong>Nueva Venta</strong>
            </template>
            <div class="container">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                        <div class="col-md-12" style="height: 40px;">
                            <SelectSearch v-model="form.assessor" :options="optionAssesors"
                                labelValue="Asesor de la venta" :messageError="form.errors.assessor" />
                        </div>
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-5 mt-5">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CATEGORIA</th>
                                <th>PRESENTACION</th>
                                <th>UNIDAD(ES)</th>
                                <th>N° GOTAS</th>
                                <th>PRECIO</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>{{props.inventory.find(item => item.inventory_id ===
                                    reference.reference)?.product.commercial_reference}}</td>
                                <td>{{props.inventory.find(item => item.inventory_id ===
                                    reference.reference)?.product.category}}</td>
                                <td>{{ !reference.quantity ? 'N/A' : reference.quantity + ' ML' }}</td>
                                <td>{{ reference.units }}</td>
                                <td>{{ !reference.perdurable.reduce((a, b) => a + Number(b), 0) ? 'N/A' : reference.perdurable.reduce((a, b) => a + Number(b), 0) }}</td>
                                <td>
                                    {{ Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(reference.units * priceReference(reference.quantity, props.inventory.find(item => item.inventory_id === reference.reference)?.product_id == giftBag ? giftBag : null) +
                                        reference.perdurable.reduce((a,
                                            b) => a + Number(b), 0) * props.warehouse.price_drops) }}
                                </td>
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
                            <PrimaryButton @click="openModal" class="px-5"
                                :class="form.processing || form.references.length == 0 || !form.assessor || !hasValidReferences ? 'disabled' : ''">
                                Registrar Venta
                            </PrimaryButton>
                            <br>
                            <span v-if="!hasValidReferences" class="text-danger">{{ buttonErrorMessage }}</span>
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
                                        :options="optionProducts" :changeFunction="changeReference"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + '.reference'] : null" />
                                </div>
                                <div class="col-md-12" v-if="referenceNew['reference']">
                                    <div class="col-md-12 row mt-3 justify-content-center" v-if="props.inventory.find(item => item.inventory_id === referenceNew['reference'])?.product_id != giftBag">
                                        <label
                                            class="form-check prais-radio row col-md-3 m-4 d-flex justify-content-center form-check-label p-2 pt-3"
                                            :for="'quantity5'">
                                            <i class="fa-solid fa-flask d-flex justify-content-center fs-9"></i>5 ml
                                            <input class="form-check-input d-none" type="radio"
                                                v-model.number="referenceNew['quantity']" :name="'quantity'" :id="'quantity5'" @change="changeQuantity(referenceNew)"
                                                :value="5">
                                        </label>
                                        <label
                                            class="form-check prais-radio row col-md-3 m-4 d-flex justify-content-center form-check-label p-2 pt-3"
                                            :for="'quantity30'">
                                            <i class="fa-solid fa-flask d-flex justify-content-center"></i>30 ml
                                            <input class="form-check-input d-none" type="radio"
                                                v-model.number="referenceNew['quantity']" :name="'quantity'" :id="'quantity30'"
                                                @change="changeQuantity(referenceNew)"
                                                :value="30">
                                        </label>
                                        <label
                                            class="form-check prais-radio row col-md-3 m-4 d-flex justify-content-center form-check-label p-1 pt-3"
                                            :for="'quantity50'">
                                            <i class="fa-solid fa-flask d-flex justify-content-center fs-4"></i>50 ml
                                            <input class="form-check-input d-none" type="radio"
                                                v-model.number="referenceNew['quantity']" :name="'quantity'" :id="'quantity50'"
                                                @change="changeQuantity(referenceNew)"
                                                :value="50">
                                        </label>
                                        <label
                                            class="form-check prais-radio row col-md-3 m-4 d-flex justify-content-center form-check-label p-1 pt-3"
                                            :for="'quantity100'">
                                            <i class="fa-solid fa-flask d-flex justify-content-center fs-2"></i>100 ml
                                            <input class="form-check-input d-none" type="radio"
                                                v-model.number="referenceNew['quantity']" :name="'quantity'" :id="'quantity100'"
                                                @change="changeQuantity(referenceNew)"
                                                :value="100">
                                        </label>
                                        <div class="col-md-8" v-if="containerNames[referenceNew['quantity']]">
                                            <SelectSearch v-model="referenceNew['container']" labelValue="Envase"
                                                :options="containerNames[referenceNew['quantity']]"
                                                :messageError="Object.keys(form.errors).length ? form.errors['references.' + '.container'] : null" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 row justify-content-center">
                                        <div class="col-md-6">
                                            <CountControl v-model="referenceNew['units']" :min="1" title="Unidad(es)" />
                                        </div>
                                        <div class="col-md-6 row form-check prais-switch form-switch d-flex justify-content-center" v-if="props.inventory.find(item => item.inventory_id === referenceNew['reference'])?.product_id != giftBag">
                                            <label class="form-check-label ms-2" for="questionPerdurable">¿Agregar gotas
                                                de perduración?</label>
                                            <input class="form-check-input fs-4" type="checkbox" id="questionPerdurable"
                                                v-model="questionPerdurable" @change="changePerdurable">
                                        </div>
                                        <div class="row d-flex justify-content-center mt-3" v-if="questionPerdurable"
                                            v-for="(unidad, index) in Array.from({ length: referenceNew['units'] }, (v, k) => k + 1)">
                                            Unidad {{ unidad }}
                                            <label class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index + 'perdurable5'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>5
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]"
                                                    :name="index + 'perdurable5'" :id="index + 'perdurable5'" :value="5"
                                                    @click="togglePerdurable(index, 5)">
                                            </label>
                                            <label class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index + 'perdurable10'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>10
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]"
                                                    :name="index + 'perdurable10'" :id="index + 'perdurable10'" :value="10"
                                                    @click="togglePerdurable(index, 10)">
                                            </label>
                                            <label class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index + 'perdurable15'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>15
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]"
                                                    :name="index + 'perdurable15'" :id="index + 'perdurable15'" :value="15"
                                                    @click="togglePerdurable(index, 15)">
                                            </label>
                                            <label class="form-check prais-radio row col-md-1 mx-4 d-flex justify-content-center form-check-label p-1 pt-1"
                                                :for="index + 'perdurable20'">
                                                <i class="fa-solid fa-droplet d-flex justify-content-center"></i>20
                                                <input class="form-check-input d-none" type="radio"
                                                    v-model="referenceNew['perdurable'][index]"
                                                    :name="index + 'perdurable20'" :id="index + 'perdurable20'" :value="20"
                                                    @click="togglePerdurable(index, 20)">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 d-flex justify-content-center">
                                        <PrimaryButton @click="addReference"
                                            :class="referenceNew['quantity'] && (referenceNew['quantity'] != 5 ? referenceNew['container'] != null : true) || props.inventory.find(item => item.inventory_id === referenceNew['reference'])?.product_id == giftBag ? '' : 'disabled'" class="px-5">
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
                            Valor a pagar: {{ Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(Math.round(form.total)) }}
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-md-12">
                                    <SelectSearch :changeFunction="changePayMethod" v-model="form.pay_method"
                                        labelValue="Metodo de pago" :options="optionPayMethod" />
                                </div>
                                <div class="row" v-if="form.pay_method == 'Efectivo'">
    <h4 class="mt-3 d-flex justify-content-center">$ {{ ((form.count_50_bill * 50000) +
        (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill
            * 5000) + (form.count_2_bill * 2000) + (form.count_100_bill * 100000) +
        (form.total_coins * 1)) }}</h4>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_100_bill" :min="0"
            title="N° Billetes 100.000" />
    </div>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_50_bill" :min="0"
            title="N° Billetes 50.000" />
    </div>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_20_bill" :min="0"
            title="N° Billetes 20.000" />
    </div>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_10_bill" :min="0"
            title="N° Billetes 10.000" />
    </div>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_5_bill" :min="0" title="N° Billetes 5.000" />
    </div>
    <div class="col-md-4 my-3">
        <CountControl v-model="form.count_2_bill" :min="0" title="N° Billetes 2.000" />
    </div>
    <div class="col-md-12 my-3">
        <TextInput type="number" name="total_coins" id="total_coins"
            v-model="form.total_coins" :focus="form.total_coins != null ? true : false"
            labelValue="Cantidad total de monedas" :minimo="0" :required="true" />
    </div>

                                    <div class="col-md-12 my-3">
                                        <TextInput type="number" name="total_coins" id="total_coins"
                                            v-model="form.total_coins" :focus="form.total_coins != null ? true : false"
                                            labelValue="Cantidad total de monedas" :minimo="0" :required="true" />
                                    </div>
                                    <div class="col-md-12 my-4 d-flex justify-content-center">
                                        <PrimaryButton @click="openModalChange" class="px-5">
                                            Cobrar
                                        </PrimaryButton>
                                    </div>
                                </div>
                                <div class="row" v-if="form.pay_method == 'Transferencia'">
                                    <div class="col-md-12 my-4 d-flex justify-content-center">
                                        <TextInput type="text" name="transaction_code" id="transaction_code"
                                            v-model="form.transaction_code"
                                            :focus="form.transaction_code != null ? true : false"
                                            labelValue="Codigo de transaccion" :minimo="0" :required="true" />
                                    </div>
                                    <div class="col-md-12 my-4 d-flex justify-content-center">
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
                        <template #header>
                            <h3>Cantidad a devolver: ${{ Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(devolver) }}</h3>
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_100_bill" :min="0" title="Billetes de $100,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_50_bill" :min="0" title="Billetes de $50,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_20_bill" :min="0" title="Billetes de $20,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_10_bill" :min="0" title="Billetes de $10,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_5_bill" :min="0" title="Billetes de $5,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_count_2_bill" :min="0" title="Billetes de $2,000" />
                                </div>
                                <div class="col-6">
                                    <CountControl v-model="form.rest_total_coins" :min="0" title="Monedas de $100" />
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="row">
                                <div class="col-6">
                                    <PrimaryButton @click="openModal">
                                        Volver
                                    </PrimaryButton>
                                </div>
                                <div class="col-6 text-end">
                                    <PrimaryButton @click="submit">
                                        Registrar Venta
                                    </PrimaryButton>
                                </div>
                            </div>
                        </template>
                    </ModalPrais>

                    <ModalPrais v-model="showModalChange" @close="showModalChange = false">
                        <template #header>
                            <h3>Cantidad a devolver: ${{ Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0, minimumFractionDigits: 0 }).format(devolver) }}</h3>
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_100_bill" :min="0"
                                        title="N° Billetes 100 mil" />
                                </div>
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_50_bill" :min="0"
                                        title="N° Billetes 50 mil" />
                                </div>
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_20_bill" :min="0"
                                        title="N° Billetes 20 mil" />
                                </div>
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_10_bill" :min="0"
                                        title="N° Billetes 10 mil" />
                                </div>
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_5_bill" :min="0" title="N° Billetes 5 mil" />
                                </div>
                                <div class="col-md-4 my-3">
                                    <CountControl v-model="form.rest_count_2_bill" :min="0" title="N° Billetes 2 mil" />
                                </div>
                                <div class="col-md-12 my-3">
                                    <TextInput type="number" name="total_coins" id="total_coins"
                                        v-model="form.rest_total_coins"
                                        :focus="form.rest_total_coins != null ? true : false"
                                        labelValue="Cantidad total de monedas" :minimo="0" :required="true" />
                                </div>
                            </div>
                            <div class="col-md-12 my-4 d-flex justify-content-center ">
                                <PrimaryButton
                                    :class="((form.rest_count_50_bill * 50000) + (form.rest_count_20_bill * 20000) + (form.rest_count_10_bill * 10000) + (form.rest_count_5_bill * 5000) + (form.rest_count_2_bill * 2000) + (form.rest_count_100_bill * 100000) + (form.rest_total_coins * 1)) != (devolver) ? 'disabled' : ''"
                                    @click="submit" class="px-5">
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