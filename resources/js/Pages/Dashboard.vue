<script setup>
import InformationCard from '@/Components/informationCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

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
        <!-- Contenedor con clases de Bootstrap para organizar horizontalmente -->
        <div class="row mt-5">
            <div class="col-md-4 ">
                <InformationCard title="Stock bodega 1" :number="props.inventory.length"
                    icon-class="fa-solid fa-warehouse" />
            </div>
            <div class="col-md-4">
                <InformationCard title="Stock bodega 2" :number="props.inventory2.length"
                    icon-class="fa-solid fa-box" />
            </div>
            <div class="col-md-4">
                <InformationCard title="Stock almacen" :number="props.inventoryAlmacen.length"
                    icon-class="fa-solid fa-box-archive" />
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-4 ">
                <InformationCard title="Solicitudes pendientes despachos" :number="props.pendingDispatches.length"
                    icon-class="fa-sharp-duotone fa-solid fa-rectangle-list" />
            </div>
            <div class="col-md-4">
                <InformationCard title="Solicitudes pendientes transformaciones" :number="props.pendingTransformation.length"
                    icon-class="fa-solid fa-flask" />
            </div>
            <div class="col-md-4">
                <InformationCard title="Despachos finalizados" :number="props.finalized.length"
                    icon-class="fa-solid fa-truck" />
            </div>
        </div>
    </BaseLayout>

</template>
