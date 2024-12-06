<script setup>
import ButtonSidebar from './ButtonSidebar.vue';
import { can } from 'laravel-permission-to-vuejs';

const buttons = [
    // {
    //     name: 'Inicio',
    //     href: route('dashboard'),
    //     icon: 'fa-solid fa-house-chimney',
    //     active: {{{$page.component.startsWith('Dashboard') ? 'true' : 'false'}}},
    // },
    // {
    //     name: 'Ordenes de Compra',
    //     href: route('orders.list'),
    //     icon: 'fa-solid fa-rectangle-list',
    //     active: $page.component.startsWith('PurchaseOrder'),
    //     can: 'Ver Ordenes de Compra',
    // },
    // {
    //     name: 'Despachos',
    //     href: route('dispatch.list'),
    //     icon: 'fa-solid fa-truck',
    //     active: $page.component.startsWith('Dispatch'),
    //     can: 'Ver Despachos',
    // },
    // {
    //     name: 'Solicitud insumos',
    //     href: route('suppliesrequest.list'),
    //     icon: 'fa-brands fa-wpforms',
    //     active: $page.component.startsWith('Requests'),
    //     can: 'Ver Solicitudes Insumos',
    // },
    // {
    //     name: 'Solicitud transformaciones',
    //     href: route('transformationRequest.list'),
    //     icon: 'fa-solid fa-flask',
    //     active: $page.component.startsWith('RequestTransformation'),
    //     can: 'Ver Solicitudes Transformacion',
    // },
    // {
    //     name: 'Stock',
    //     href: route('stock.dashboard'),
    //     icon: 'fa-duotone fa-solid fa-boxes-stacked',
    //     active: $page.component.startsWith('Stock'),
    //     can: 'Ver Stock',
    // },
    // {
    //     name: 'Reenvase',
    //     href: route('repackage.list'),
    //     icon: 'fa-solid fa-vial',
    //     active: $page.component.startsWith('Repackage'),
    //     can: 'Ver Reenvases',
    // },
    // {
    //     name: 'Transformaciones',
    //     href: route('LabTransformation.list'),
    //     icon: 'fa-solid fa-flask',
    //     active: $page.component.startsWith('LabTransformations'),
    //     can: 'Ver Transformaciones',
    // },
];

</script>

<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-card">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="\assets\images\Logo_1.png" />
            <h5 class="mx-2">{{ $page.props.auth.user.name }}</h5>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li v-for="button in buttons" :key="button.name">
                <ButtonSidebar v-if="can(button.can)" :href="button.href" :class="{ 'active': button.active }">
                    <i :class="button.icon" class="me-4"></i>
                    {{ button.name }}
                </ButtonSidebar>
            </li>
        </ul>
        <hr>
    </div>
</template>
