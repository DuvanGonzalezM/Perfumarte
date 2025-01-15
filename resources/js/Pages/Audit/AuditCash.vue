<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

const props = defineProps({
    dispatch: {
        type: Array,
    },
});


</script>
<template>
    <Head title="Auditoria Caja" />
    <BaseLayout>
        <template #header>
            <h1>Auditoria Caja</h1>
        </template>
    </BaseLayout>
</template>