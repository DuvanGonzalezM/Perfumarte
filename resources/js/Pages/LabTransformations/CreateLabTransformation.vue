<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import SliderPrais from '@/Components/SliderPrais.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import moment from 'moment';
import axios from 'axios';

const props = defineProps({
    newProduct: {
        type: Array,
        required: true,
    },
    requests: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    'reference': null,
    'escencia': '',
    'dipropileno': '',
    'disolvente': '',
    'request': '',
    'status': 'En proceso',
});

const optionProduts = ref(props.newProduct.map(inventory => [{ 'title': inventory.product.reference, 'value': inventory.product_id }][0]));
const optionRequests = ref(props.requests.map(request => ({ 'title': (request.user.location.name + ' - ' + moment(request.created_at).format('DD/MM/Y')), 'value': request.request_id })));
const references = ref(null);
const reference = ref(null);
const swiper = ref(null);
const requestSeleted = ref(null);
const quantityTransform = ref(0);
const disableButton = ref(false);

const getRequest = () => {
    if (requestSeleted) {
        let request = props.requests.find(requestI => requestI.request_id == requestSeleted.value);
        references.value = request.detail_request;
        reference.value = references.value[0];
        form.reference = reference.value.inventory.product_id;
        form.request = request.request_id;
        quantityTransform.value = reference.value.quantity;
    }
}

const changeSlide = () => {
    reference.value = references.value[swiper.value.activeIndex];
    form.reference = reference.value.inventory.product_id;
    form.escencia = '';
    form.dipropileno = '';
    form.disolvente = '';
    quantityTransform.value = reference.value.quantity;
}
const submit = async () => {
    try {
        disableButton.value = true;
        if (references.value.length == 1) {
            form.status = 'Finalizada';
        }
        await axios.post(route('store.LabTransformation', form))
            .then(function (response) {
                if(references.value.length > 1){
                    references.value.splice(swiper.value.activeIndex, 1);
                    reference.value = references.value[0];
                    form.reference = reference.value.inventory.product_id;
                    form.escencia = '';
                    form.dipropileno = '';
                    form.disolvente = '';
                }
            });
    } catch (error) {
        disableButton.value = false;
        console.log(error);
    }
    form.post(route('store.LabTransformation'));
}
</script>

<template>

    <Head title="Nueva transformacion laboratorio" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Nueva transformacion laboratorio</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva transformacion laboratorio</strong>
            </template>
            <div class="container px-0">
                <SelectSearch :options="optionRequests" class="my-4" :changeFunction="getRequest"
                    v-model="requestSeleted" labelValue="Selecciona una solicitud" />
                <SliderPrais :items="references.length" v-if="references" v-model="swiper"
                    :changeFunction="changeSlide">
                    <template v-for="(reference, index) in references" v-slot:[index]>
                        <div class="p-3 mx-5 my-3 cardboxprais cardpurcheorder">
                            <form @submit.prevent="submit" class="table-prais">
                                <table
                                    class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle mb-5">
                                    <thead>
                                        <tr>
                                            <th colspan="2"> TRANSFORMAR: {{ quantityTransform }} ml</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsList">
                                        <tr>
                                            <td>REFERENCIA: </td>
                                            <td>
                                                <SelectSearch v-model="form.reference" :options="optionProduts"
                                                    name="reference[]" id="reference[]" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CANTIDAD ESCENCIA (ml): </td>
                                            <td>
                                                <TextInput type="number" v-model="form.escencia" name="escencia[]"
                                                    id="escencia[]" :required="true" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CANTIDAD DIPROPILENO (ml): </td>
                                            <td>
                                                <TextInput type="number" v-model="form.dipropileno" name="dipropileno[]"
                                                    id="dipropileno[]" :required="true" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CANTIDAD DISOLVENTE (ml): </td>
                                            <td>
                                                <TextInput type="number" v-model="form.disolvente" name="disolvente[]"
                                                    id="disolvente[]" :required="true" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CANTIDAD TOTAL: </td>
                                            <td
                                                :style="quantityTransform == (form.escencia + form.dipropileno + form.disolvente) ? '' : 'color: red;'">
                                                {{ form.escencia + form.dipropileno + form.disolvente || '0' }} ml
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </template>
                </SliderPrais>
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('LabTransformation.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton @click="submit" class="px-5"
                            :disabled="!(quantityTransform == (form.escencia + form.dipropileno + form.disolvente))"
                            :class="!(quantityTransform == (form.escencia + form.dipropileno + form.disolvente)) || disableButton ? 'disabled' : ''">
                            Registrar
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>