<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';

DataTable.use(DataTablesCore);
const options = {
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-CO.json'
    }
};

const props = defineProps({
    columns: {
        type: Array,
    },
    data: {
        type: Array,
    },
});
</script>

<template>
    <div class="table-responsive table-prais">
        <DataTable :options="options" :columns="columns" :data="data" class="table table-hover">
            <slot />
        </DataTable>
    </div>
</template>