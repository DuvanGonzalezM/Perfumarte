<script setup>
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    inventories: {
        type: Array,
    },
});



const columnsTable = [
    {
        data: 'commercial_reference',
        title: 'REFERENCIA'
    },
    {
        data: 'status',
        title: 'CANTIDAD'
    },
];

const submit = () => {
    form.post(route('orders.store'));
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
            <Table :data="inventories" :columns="columnsTable" />

            <div class="col-6">
                <PrimaryButton :href="route('transformationRequest.list')" class="px-5">
                    Volver
                </PrimaryButton>
            </div>
            <div class="col-6 text-end">
                <PrimaryButton @click="submit" class="px-5">
                    Enviar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>