<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    newProduct: {
        type: Array,
    },
});

const form = useForm({

    reference: '',
    escencia: '',
    dipropileno: '',
    disolvente: '',
    cantidad total:'',

});

const optionProduts = ref(props.newProduct.map(inventory => [{ 'title': inventory.product.reference, 'value': inventory.product_id }][0]));

const submit = () => {
    form.post(route('store.repackage'));
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
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <tbody id="productsList">
                            <tr>
                                <td>REFERENCIA: </td>
                                <td>
                                    <SelectSearch v-model="form.reference" :options="optionProduts" />
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD ESCENCIA (ml): </td>
                                <td>
                                    <TextInput type="string" v-model="form.escencia" :required="true" />
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DIPROPILENO (ml): </td>
                                <td>
                                    <TextInput type="number" v-model="form.dipropileno" :required="true" />
                                </td>
                            </tr>
                            <tr>
                                <td>CANTIDAD DISOLVENTE (ml): </td>
                                <td>
                                    <TextInput type="number" v-model="form.disolvente" :required="true" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('repackage.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5">
                                Registrar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>