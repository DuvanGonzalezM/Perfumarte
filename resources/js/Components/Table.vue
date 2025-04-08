<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import { computed } from 'vue';

DataTable.use(DataTablesCore);

const props = defineProps({
    columns: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        required: true
    }
});

const paginVisibility = computed(() => props.data.length > 10);

const options = computed(() => ({
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-CO.json'
    },
    lengthChange: false,
    info: paginVisibility.value,
    order: [[0, 'desc']],
    paging: paginVisibility.value,
    pagingType: 'simple_numbers',
    responsive: true,
    pageLength: 10,
    classes: {
        sPageButton: 'paginate_button page-item',
        sPageButtonActive: 'paginate_button page-item active',
        sPageButtonDisabled: 'paginate_button page-item disabled'
    }
}));
</script>

<template>
    <div class="table-prais table-responsive">
        <DataTable :options="options" :columns="columns" :data="data" class="table">
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