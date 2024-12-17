<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import SelectSearch from '@/Components/SelectSearch.vue';



const props = defineProps({
    locations: {
        type: Array,
    },
    supervisors: {
        type: Array,
    },
});

const form = useForm({
    location_id: null,
    user_id: null,
});

const showModal = ref(false);
const openModal = (rowData) => {
    form.location_id = rowData.location_id;
    form.user_id = null;
    showModal.value = true;
    if (rowData.user_location.length > 0) {
        form.user_id = rowData.user_location[0].user_id;
    }

}

const optionSupervisor = ref(props.supervisors.map((supervisor) => ({ 'title': supervisor.name, 'value': supervisor.user_id })));

const columnsTable = [
    {
        data: 'location_id',
        title: 'CODIGO SEDE'
    },
    {
        data: 'name',
        title: 'NOMBRE SEDE'
    },
    {
        data: 'user_location',
        title: 'SUPERVISOR ASIGNADO',
        render: function (data) {
            return data && data.length > 0 ? data[0].name : 'Sin supervisor asignado';
        }
    },
    {
        data: 'location_id',
        title: 'ASIGNAR',
        render: '#render',
    },
];



const submit = () => {
    form.put(route('assignment.update'), form.user_id);
    showModal.value = false;
}


</script>

<template>

    <Head title="Supervisores asignados" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Asignacion de supervisores</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Supervisores asignados</strong>
            </template>
            <div class="container">
                <Table class="size-prais-5" :data="locations" :columns="columnsTable">
                    <template #templateRender="items">
                        <a href="#" @click.prevent="openModal(items.item.rowData)"> <i class="fa-solid fa-user-pen"></i>
                        </a>
                    </template>
                </Table>
                <ModalPrais v-model="showModal" @close="showModal = false">
                    <template #header>
                        Supervisores
                    </template>
                    <template #body>
                        <form @submit.prevent="submit">
                            <SelectSearch class="mt-5" v-model="form.user_id" :options="optionSupervisor"
                                labelValue="Seleccionar" />
                        </form>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="submit" class="px-5">
                            Registrar
                        </PrimaryButton>
                    </template>
                </ModalPrais>
            </div>
        </SectionCard>
    </BaseLayout>
</template>