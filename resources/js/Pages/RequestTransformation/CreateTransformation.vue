<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const props = defineProps({
    inventories: {
        type: Array,
    },
});

const form = useForm({
    references: [
        {
            'reference': '',
            'quantity': '',
        }
    ],
});

const optionProduts = ref(props.inventories.map(inventory => [{ 'title': inventory.product.reference, 'value': inventory.inventory_id }][0]));
const showAddButtom = ref(form.references.length < optionProduts.value.length);

const addRow = () => {
    showAddButtom.value = form.references.length < (optionProduts.value.length - 1);
    if (form.references.length < optionProduts.value.length) {
        form.references.push({
            'reference': '',
            'quantity': '',
        });
    }
}

const removeReference = (index) => {
    form.references.splice(index, 1);
    showAddButtom.value = form.references.length < optionProduts.value.length;
}
const submit = () => {
    form.post(route('transformation.store'));
}

</script>

<template>

    <Head title="Nueva Transformacion" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Nueva Transformacion</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Nueva Transformacion</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2">
                        <thead>
                            <tr>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD (ml)</th>
                            </tr>
                        </thead>
                        <tbody id="productsList">
                            <tr v-for="(reference, index) in form.references">
                                <td>
                                    <SelectSearch v-model="reference['reference']" :options="optionProduts"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.reference'] : null" />
                                </td>
                                <td>
                                    <TextInput type="number" name="quantity[]" id="quantity[]"
                                        v-model="reference['quantity']" :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors['references.' + index + '.quantity'] : null" />
                                </td>
                                <div class="removeItem" @click="removeReference(index)">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                        <div class="addItem" @click="addRow" v-if="showAddButtom">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>
                    <div class="row my-5">
                        <div class="col-6">
                            <PrimaryButton :href="route('transformationRequest.list')" class="px-5">
                                Volver
                            </PrimaryButton>
                        </div>
                        <div class="col-6 text-end">
                            <PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                                Enviar
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </SectionCard>
    </BaseLayout>
</template>