<script setup>
import { usePage } from '@inertiajs/vue3';
import ButtonSidebar from './ButtonSidebar.vue';
import { can } from 'laravel-permission-to-vuejs';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const page = usePage();
const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    
    if (isMobileMenuOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
};

const handleEscKey = (event) => {
    if (event.key === 'Escape' && isMobileMenuOpen.value) {
        toggleMobileMenu();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleEscKey);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleEscKey);
});

const buttons = [
    {
        name: 'Inicio',
        href: route('dashboard'),
        icon: 'fa-solid fa-house-chimney',
        active: page.component.startsWith('Dashboard'),
        can: 'Ver Dashboard',
    },
    {
        name: 'Inventario',
        href: route('inventory.current'),
        icon: 'fa-duotone fa-solid fa-boxes-stacked',
        active: page.component.startsWith('Stock'),
        can: 'Ver Inventario Sede',
    },
    {
        name: 'Ordenes de Compra',
        href: route('orders.list'),
        icon: 'fa-solid fa-rectangle-list',
        active: page.component.startsWith('PurchaseOrder'),
        can: 'Ver Ordenes de Compra',
    },
    {
        name: 'Despachos',
        href: route('dispatch.list'),
        icon: 'fa-solid fa-truck',
        active: page.component.startsWith('Dispatch'),
        can: 'Ver Despachos',
    },
    {
        name: 'Ventas',
        href: route('sales.list'),
        icon: 'fa-solid fa-cash-register',
        active: page.component.startsWith('Sales'),
        can: 'Ver Ventas',
    },
    {
        name: 'Solicitud Insumos',
        href: route('suppliesrequest.list'),
        icon: 'fa-brands fa-wpforms',
        active: page.component.startsWith('Requests'),
        can: 'Ver Solicitudes Insumos',
    },
    {
        name: 'Solicitud Transformaciones',
        href: route('transformationRequest.list'),
        icon: 'fa-solid fa-flask',
        active: page.component.startsWith('RequestTransformation'),
        can: 'Ver Solicitudes Transformacion',
    },
    {
        name: 'Stock',
        href: route('stock.dashboard'),
        icon: 'fa-duotone fa-solid fa-boxes-stacked',
        active: page.component.startsWith('Stock'),
        can: 'Ver Stock',
    },
    {
        name: 'Reenvase',
        href: route('repackage.list'),
        icon: 'fa-solid fa-vial',
        active: page.component.startsWith('Repackage'),
        can: 'Ver Reenvases',
    },
    {
        name: 'Transformaciones',
        href: route('LabTransformation.list'),
        icon: 'fa-solid fa-flask',
        active: page.component.startsWith('LabTransformations'),
        can: 'Ver Transformaciones',
    },
    {
        name: 'Asignar Supervisores',
        href: route('assignment.supervisor'),
        icon: 'fa-solid fa-user-pen',
        active: page.component.startsWith('Assignment/AssignmentSupervisor'),
        can: 'Asignar Supervisor',
    },
    {
        name: 'Asignar Asesores',
        href: route('list.location'),
        icon: 'fa-solid fa-user-group',
        active: page.component.startsWith('Assignment/ListLocation'),
        can: 'Asignar Personal',
    },
    {
        name: 'Recepción Insumos',
        href: route('dispatch.show'),
        icon: 'fa-solid fa-list-check',
        active: page.component.startsWith('Reception'),
        can: 'Recibir Insumos',
    },
    {
        name: 'Auditorias ',
        href: route('audits'),
        icon: 'fa-solid fa-file-signature',
        active: page.component.startsWith('Audit'),
        can: 'Auditar',
    },
    {
        name: 'Productos',
        href: route('products.list'),
        icon: 'fa-solid fa-box-open',
        active: page.component.startsWith('Product'),
        can: 'Ver Productos',
    },
    {
        name: 'Reportes ',
        href: route('reports'),
        icon: 'fa-solid fa-clipboard-list',
        active: page.component.startsWith('Reports'),
        can: 'Ver Reportes',
    },
    {
        name: 'Proveedores ',
        href: route('suppliers.list'),
        icon: 'fa-regular fa-address-book',
        active: page.component.startsWith('Supplier'),
        can: 'Ver Proveedores',
    },
    {
        name: 'Novedades',
        href: route('novelties.list'),
        icon: 'fa-solid fa-file-circle-exclamation',
        active: page.component.startsWith('Novelty'),
        can: 'Ver Novedades',
    },
];
</script>

<template>
    <div class="mobile-menu-toggle" :class="{'toggle-open': isMobileMenuOpen}">
        <button @click="toggleMobileMenu" class="btn-prais btn-primary">
            <i :class="isMobileMenuOpen ? 'fa-solid fa-times' : 'fa-solid fa-bars'"></i>
        </button>
    </div>

    <div 
        v-if="isMobileMenuOpen" 
        class="sidebar-overlay"
        :class="{'active': isMobileMenuOpen}"
        @click="toggleMobileMenu">
    </div>

    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-card" 
         :class="{ 
             'sidebar-mobile-open': isMobileMenuOpen,
             'd-none d-md-block': !isMobileMenuOpen
         }">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="\assets\images\Logo_1.avif" alt="Logo" />
            <h5 class="mx-2">{{ $page.props.auth.user.roles[0]?.name }}</h5>
        </a>
       
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li v-for="button in buttons" :key="button.name" class="nav-item">
                <ButtonSidebar 
                    v-if="can(button.can)" 
                    :href="button.href" 
                    :class="{ 
                        'active': button.active,
                        'button-mobile': isMobileMenuOpen
                    }"
                    @click="isMobileMenuOpen ? toggleMobileMenu() : null">
                    <i :class="button.icon" class="me-4"></i>
                    {{ button.name }}
                </ButtonSidebar>
            </li>
        </ul>
    </div>
</template>
