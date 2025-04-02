<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';

DataTable.use(DataTablesCore);

const props = defineProps({
    columns: {
        type: Array,
    },
    data: {
        type: Array,
    },
});
let paginVisibility = props.data.length > 10 ? true : false;

const options = {
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-CO.json'
    },
    lengthChange: false,
    info: paginVisibility,
    order: [[0, 'desc']],
    paging: paginVisibility
};
</script>

<template>
    <div class="table-prais">
        <DataTable :options="options" :columns="columns" :data="data" class="table table-hover">
            <template #render="item">
                <slot name="templateRender" :item="item"/>
            </template>
            <template #rendertwo="item">
                <slot name="templateRendertwo" :item="item"/>
            </template>
            <slot />
        </DataTable>
    </div>
</template>