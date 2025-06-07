<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    audits: {
        type: Array,
        required: true
    },
    location: {
        type: Object,
        required: true
    }
});
const columnsTable = [
{
        data: 'id_audits',
        title: 'CODIGO AUDITORIA'
    },
    {
        data: "created_at",
        title: 'FECHA AUDITORIA',
        render: function (data) {
            return moment(data).format('DD/MM/YYYY HH:mm');
        },
    },
    {
        data: "user.name",
        title: 'AUDITOR',
    },
    {
        data: "type_audit",
        title: 'TIPO DE AUDITORIA',
        render: function (data) {
            if (data === '1') {
                return 'Arqueo';
            } else {
                return 'Inventario';
            }
        },
    },
];

</script>



<template>
    <Head title="Auditorias" />
    <BaseLayout>
        <template #header>
            <h1>Auditorias de {{ props.location.name }}</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Auditorias de {{ props.location.name }}</strong>
            </template>
            <Table :columns="columnsTable" :data="props.audits" />
            <div class="row my-5 text-center">
                <div class="container">
                    <PrimaryButton :href="route('locations.detail', props.location.location_id)" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>