<script setup>
import InformationCard from '@/Components/informationCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { is } from 'laravel-permission-to-vuejs';

const props = defineProps({

    inventory: {
        type: Array,
    },

    inventory2: {
        type: Array,
    },

    inventoryAlmacen: {
        type: Array,
    },

    finalized: {
        type: Array,
    },

    pendingDispatches: {
        type: Array,
    },

    pendingTransformation: {
        type: Array,
    },
});
</script>

<template>

    <Head title="Inicio" />

    <BaseLayout>
        <template #header>
            <h1>Inicio</h1>
        </template>
        <div class="row">
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard title="Stock bodega 1 Referencias:" :number=" props.inventory.length"
                    icon-class="fa-solid fa-warehouse" />
            </div>
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard title="Stock bodega 2 Referencias:" :number="props.inventory2.length"
                    icon-class="fa-solid fa-box" />
            </div>
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard title="Stock almacen Articulos:" :number="props.inventoryAlmacen.length"
                    icon-class="fa-solid fa-box-archive" />
            </div>
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard title="Solicitudes pendientes despachos" :number="props.pendingDispatches.length"
                    icon-class="fa-sharp-duotone fa-solid fa-rectangle-list" />
            </div>
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard title="Solicitudes pendientes transformaciones" :number="props.pendingTransformation.length"
                    icon-class="fa-solid fa-flask" />
            </div>
            <div class="col-md-4 mt-5" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard title="Despachos finalizados" :number="props.finalized.length"
                    icon-class="fa-solid fa-truck" />
            </div>
        </div>
    </BaseLayout>

</template>
