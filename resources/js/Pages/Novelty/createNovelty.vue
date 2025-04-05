<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    newNovelty: {
        type: Array,
    },
});

const form = useForm({
    type_novelty: '',
    description_novelty: '',
    warehouse_id: '',
});

const optionWarehouse = ref(props.newNovelty.map(warehouse => [{ 'title': warehouse.name, 'value': warehouse.warehouse_id }][0]));

const submit = () => {
    form.post(route('novelty.store'));
}

const listNovelties = ref([
    { name: 'Ajuste de Inventario Bodegas' },
    { name: 'Ajuste de Despachos' },
    { name: 'Ajuste de Ventas' },
    { name: 'Ajuste Ordenes de Compra' },
    { name: 'Ajuste de Transformaciones' },
]);

const optionNovelties = ref(listNovelties.value.map(novelty => ({ 'title': novelty.name, 'value': novelty.name })));


</script>

<template>

    <Head title="Nueva Novedad" />

    <BaseLayout :loading="form.processing">
        <template #header>
            <!-- <Alert /> -->
            <h1>Nueva Novedad</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Registro de Novedad</strong>
            </template>
            <div class="container px-0">
                <form @submit.prevent="submit" class="table-prais">
                    <div class="row">
                    </div>
                    <table class="table table-hover text-center dt-body-nowrap size-prais-2 align-middle">
                        <tbody id="productsList">
                            <tr>
                                <td>TIPO DE NOVEDAD</td>
                                <td>
                                    <SelectSearch v-model="form.type_novelty" :options="optionNovelties"
                                    :messageError="Object.keys(form.errors).length ? form.errors.type_novelty : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>DESCRIPCION</td>
                                <td>
                                    <TextInput type="text" name="description_novelty[]" id="description_novelty[]" v-model="form.description_novelty"
                                        :required="true"
                                        :messageError="Object.keys(form.errors).length ? form.errors.description_novelty : null" />
                                </td>
                            </tr>

                            <tr>
                                <td>SEDE/BODEGA</td>
                                <td>
                                    <SelectSearch v-model="form.warehouse_id" :options="optionWarehouse"
                                    :messageError="Object.keys(form.errors).length ? form.errors.warehouse_id : null" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row text-center justify-content-center my-5">
                    </div>
                    <div class="row my-5 text-center">
                        <div class="col">
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