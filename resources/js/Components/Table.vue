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
    },
    order: {
        type: Array,
        default: () => [[0, 'desc']]
    }
});

const paginVisibility = computed(() => props.data.length > 10);

/*
 * Traducción incrustada. Antes se cargaba desde
 * https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-CO.json y DataTables no
 * inicializa la tabla hasta que ese $.ajax —sin timeout— resuelve: si la red
 * corporativa descarta la salida HTTPS, la tabla no se dibuja durante todo el
 * tiempo de espera del navegador. Era la única dependencia externa en runtime
 * del frontend y la compartían 35 páginas.
 */
const spanishLanguage = {
    processing: 'Procesando...',
    lengthMenu: 'Mostrar _MENU_ registros',
    zeroRecords: 'No se encontraron resultados',
    emptyTable: 'Ningún dato disponible en esta tabla',
    info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
    infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
    infoFiltered: '(filtrado de un total de _MAX_ registros)',
    search: 'Buscar:',
    loadingRecords: 'Cargando...',
    paginate: {
        first: 'Primero',
        last: 'Último',
        next: 'Siguiente',
        previous: 'Anterior',
    },
    aria: {
        sortAscending: ': Activar para ordenar la columna de manera ascendente',
        sortDescending: ': Activar para ordenar la columna de manera descendente',
    },
};

const options = computed(() => ({
    language: spanishLanguage,
    lengthChange: false,
    info: true, // siempre mostrar info
    order: props.order,
    paging: true, // siempre activar paginación
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