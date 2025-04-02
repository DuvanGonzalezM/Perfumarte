<script setup>
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import { ref, onMounted, computed } from 'vue';

DataTable.use(DataTablesCore);

const props = defineProps({
    columns: {
        type: Array,
    },
    data: {
        type: Array,
    },
});

const isMobile = ref(false);
const paginVisibility = computed(() => props.data.length > (isMobile.value ? 5 : 10));

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}

const options = computed(() => ({
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-CO.json'
    },
    lengthChange: false,
    info: paginVisibility.value,
    order: [[0, 'desc']],
    paging: paginVisibility.value,
    responsive: true,
    scrollX: isMobile.value,
    pageLength: isMobile.value ? 5 : 10
}));
</script>

<template>
    <div class="table-prais table-responsive">
        <DataTable :options="options" :columns="columns" :data="data" class="table table-hover">
            <template #render="item">
                <slot name="templateRender" :item="item"/>
            </template>
            <slot />
        </DataTable>
    </div>
</template>