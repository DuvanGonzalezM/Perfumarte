<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    getProduct: {
        type: Array,
    },
});

const form = useForm({
    reference: '',
    quantity: '',
});

const optionProduts = ref(props.getProduct.map(inventory => [{ 'title': inventory.product.reference, 'value': inventory.product_id }][0]));

const submit = () => {
    form.post(route('store.repackage'));
}

</script>

<template>

    <Head title="Nuevo reenvase" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Nuevo reenvase</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nuevo reenvase</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD (ml)</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr>
                                <td>
                                    <SelectSearch v-model="form.reference" :options="optionProduts"
                                    :messageError="Object.keys(form.errors).length ? form.errors.reference : null" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]" v-model="form.quantity"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.quantity : null" />
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
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Registrar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>