<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    transformation: {
        type: Object,
        required: true,
    },
    inventory: {
        type: Array,
        required: true,
    },
});


const form = useForm({
    transformation_id: props.transformation.transformation_id,
    inventory_id: props.transformation.inventory_id,
    escence: props.transformation.escence,
    dipropylene: props.transformation.dipropylene,
    solvent: props.transformation.solvent
});


const optionProducts = props.inventory.map(inventory => ({
    title: inventory.product.reference,
    value: inventory.inventory_id}));

const totalQuantity = computed(() => {
    return (parseFloat(form.escence) || 0) +
        (parseFloat(form.dipropylene) || 0) +
        (parseFloat(form.solvent) || 0);
});

const submit = () => {
    form.put(route('LabTransformation.update', form.transformation_id));
};

</script>

<template>

    <Head :title="transformation.transformation_id ? 'Editar transformación' : 'Nueva transformación'" />

    <BaseLayout :loading="form.processing">
        <template #header>
            <h1>{{ transformation.transformation_id ? 'Editar' : 'Nueva' }} transformación laboratorio</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ transformation.transformation_id ? 'Editar' : 'Nueva' }} transformación laboratorio</strong>
            </template>

            <div class="container px-0">
                <div class="p-3 mx-5 my-3 cardboxprais cardpurcheorder">
                    <form @submit.prevent="submit" class="table-prais">
                        <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle mb-5">
                            <tbody id="productsList">
                                <tr>
                                    <td>REFERENCIA:</td>
                                    <td>
                                        <SelectSearch v-model="form.inventory_id" :options="optionProducts"
                                            :messageError="form.errors.inventory_id" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>CANTIDAD ESCENCIA (ml):</td>
                                    <td>
                                        <TextInput type="number" v-model="form.escence" :required="true"
                                            :messageError="form.errors.escence" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>CANTIDAD DIPROPILENO (ml):</td>
                                    <td>
                                        <TextInput type="number" v-model="form.dipropylene" :required="true"
                                            :messageError="form.errors.dipropylene" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>CANTIDAD DISOLVENTE (ml):</td>
                                    <td>
                                        <TextInput type="number" v-model="form.solvent" :required="true"
                                            :messageError="form.errors.solvent" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>CANTIDAD TOTAL:</td>
                                    <td>
                                        {{ totalQuantity }} ml
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('LabTransformation.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton type="submit" class="px-5" @click="submit" :disabled="form.processing">
                            {{ transformation.transformation_id ? 'Actualizar' : 'Registrar' }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>