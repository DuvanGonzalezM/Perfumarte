<script setup>
import InformationCard from '@/Components/informationCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { is } from 'laravel-permission-to-vuejs';
import { ref, onMounted } from 'vue';

const isMobile = ref(false);
const isTablet = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 576;
    isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
}

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
    assignedPersonnel: {
        type: Array,
    },
    pendingRequestSupervisor:{
        type: Array,
    },
    getAuditInventory:{
        type: Array,
    },
    getAuditCash:{
        type: Array,
    },
    asesores:{
        type: Array
    }
});
</script>

<template>
    <Head title="Inicio" />

    <BaseLayout>
        <template #header>
            <h1 :class="{ 'text-center': isMobile }">Inicio</h1>
        </template>
        <div class="row" :class="{ 'g-2': isMobile, 'g-3': isTablet }">
            <!-- Stock bodega 1 -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard 
                    title="Stock bodega 1 Referencias:" 
                    :number="props.inventory.length"
                    icon-class="fa-solid fa-warehouse" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Stock bodega 2 -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard 
                    title="Stock bodega 2 Referencias:" 
                    :number="props.inventory2.length"
                    icon-class="fa-solid fa-box" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Stock almacén -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard 
                    title="Stock almacen Articulos:" 
                    :number="props.inventoryAlmacen.length"
                    icon-class="fa-solid fa-box-archive" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Solicitudes despachos -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard 
                    title="Solicitudes pendientes despachos" 
                    :number="props.pendingDispatches.length"
                    icon-class="fa-sharp-duotone fa-solid fa-rectangle-list" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Solicitudes transformaciones -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones | Laboratorio')">
                <InformationCard 
                    title="Solicitudes pendientes transformaciones"
                    :number="props.pendingTransformation.length" 
                    icon-class="fa-solid fa-flask" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Despachos finalizados -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Jefe de Operaciones')">
                <InformationCard 
                    title="Despachos finalizados" 
                    :number="props.finalized.length"
                    icon-class="fa-solid fa-truck" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Puntos de venta auditados -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Supervisor | Subdirector')">
                <InformationCard 
                    title="Puntos de venta Auditados" 
                    :number="props.getAuditInventory.length"
                    icon-class="fa-solid fa-file-signature" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Solicitudes por aprobar -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Supervisor | Subdirector')">
                <InformationCard 
                    title="Solicitudes de insumos por aprobar" 
                    :number="props.pendingRequestSupervisor.length"
                    icon-class="fa-solid fa-clipboard-list" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Asesores asignados (Supervisor) -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Supervisor')">
                <InformationCard 
                    title="Asesores Asignados" 
                    :number="props.assignedPersonnel.length"
                    icon-class="fa-solid fa-user-group" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Asesores asignados (Subdirector) -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Subdirector')">
                <InformationCard 
                    title="Asesores Asignados" 
                    :number="props.asesores.length"
                    icon-class="fa-solid fa-user-group" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
            
            <!-- Arqueos realizados -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Supervisor | Subdirector')">
                <InformationCard 
                    title="Arqueos Realizados" 
                    :number="props.getAuditCash.length"
                    icon-class="fa-solid fa-clipboard-check" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                /> 
            </div>
            
            <!-- Supervisores asignados -->
            <div :class="[
                isMobile ? 'col-12 mt-3' : isTablet ? 'col-sm-6 mt-4' : 'col-md-4 mt-5',
                { 'px-2': isMobile }
            ]" v-if="is('Administrador | Subdirector')">
                <InformationCard 
                    title="Supervisores Asignados" 
                    :number="props.assignedPersonnel.length"
                    icon-class="fa-solid fa-user-pen" 
                    :class="{ 'card-mobile': isMobile, 'card-tablet': isTablet }"
                />
            </div>
        </div>
    </BaseLayout>
</template>
