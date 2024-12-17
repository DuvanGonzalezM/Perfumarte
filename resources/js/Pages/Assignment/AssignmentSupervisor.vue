<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    locations: {
        type: Array,
    },

    supervisors: {
        type: Array,
    },

}); 


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
        render: function(data) {
            return data && data.length > 0 ? data[0].name : 'Sin supervisor asignado';
        }
    },
];

const submit = () => {
    form.post(route('assignment.store'));
}

</script>

<template>

    <Head title="Supervisores asignados" />

    <BaseLayout>
        <template #header>
            <!-- <Alert /> -->
            <h1>Supervisores asignados</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Supervisores asignados</strong>
            </template>
            <div class="container">
                <Table class="size-prais-5" :data="locations" :columns="columnsTable" />
            </div>
            <div class="col-6 text-end">
                <!--<PrimaryButton @click="submit" class="px-5" :class="form.processing ? 'disabled' : ''">
                    Registrar
                </PrimaryButton> -->
            </div>
        </SectionCard>
    </BaseLayout>
</template>