<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    repackage: {
        type: Object,
        required: true,
    },
    inventory: {
        type: Array,
        required: true,
    },
});


const form = useForm({
    change_warehouse_id: props.repackage.change_warehouse_id,
    inventory_id: props.repackage.inventory_id,
    quantity: props.repackage.quantity
    
});

const optionProducts = props.inventory.map(inventory => ({
    title: inventory.product.reference,
    value: inventory.inventory_id}));

const submit = () => {
    form.put(route('update.repackage', form.change_warehouse_id));
  
};

</script>

<template>

    <Head :title="repackage.change_warehouse_id ? 'Editar reenvase' : 'Nueva reenvase'" />

    <BaseLayout :loading="form.processing">
        <template #header>
            <h1>{{ repackage.change_warehouse_id ? 'Editar' : 'Nueva' }} reenvase</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ repackage.change_warehouse_id ? 'Editar' : 'Nueva' }} reenvase</strong>
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
                                        <TextInput type="number" v-model="form.quantity" :required="true"
                                            :messageError="form.errors.quantity" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('repackage.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton type="submit" class="px-5" @click="submit" :disabled="form.processing">
                            {{ repackage.change_warehouse_id ? 'Actualizar' : 'Registrar' }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>