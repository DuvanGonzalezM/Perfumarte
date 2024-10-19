<script setup>
import ButtonSidebar from './ButtonSidebar.vue';
import PrimaryButton from './PrimaryButton.vue';
import { can } from 'laravel-permission-to-vuejs';

</script>

<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-card">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="\assets\images\Logo_1.png" />
            <h5 class="mx-2">{{ $page.props.auth.user.name }}</h5>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <ButtonSidebar :href="route('dashboard')" :class="{ 'active': $page.component.startsWith('Dashboard') }">
                    <i class="fa-solid fa-house-chimney me-4"></i>
                    Inicio
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Ordenes de Compra')">
                <ButtonSidebar :href="route('orders.list')"
                    :class="{ 'active': $page.component.startsWith('PurchaseOrder') }">
                    <i class="fa-solid fa-rectangle-list me-4"></i>
                    Ordenes de compra
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Despachos')">
                <ButtonSidebar :href="route('dispatch.list')"
                    :class="{ 'active': $page.component.startsWith('Dispatch') }">
                    <i class="fa-solid fa-truck me-4"></i>
                    Despachos
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Solicitudes Insumos')">
                <ButtonSidebar :href="route('suppliesrequest.list')"
                    :class="{ 'active': $page.component.startsWith('Requests') }">
                    <i class="fa-brands fa-wpforms me-4"></i>
                    Solicitud insumos
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Solicitudes Transformacion')">
                <ButtonSidebar :href="route('transformationRequest.list')"
                    :class="{ 'active': $page.component.startsWith('RequestTransformation') }">
                    <i class="fa-solid fa-flask me-4"></i>
                    Transformaciones
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Stock')">
                <ButtonSidebar :href="route('stock.dashboard')"
                    :class="{ 'active': $page.component.startsWith('Stock') }">
                    <i class="fa-duotone fa-solid fa-boxes-stacked me-4"></i>
                    Stock
                </ButtonSidebar>
            </li>
            <li v-if="can('Ver Reenvases')">
                <ButtonSidebar :href="route('repackage.list')" :class="{'active': $page.component.startsWith('Repackage')}">
                    <i class="fa-solid fa-vial me-4"></i>
                    Reenvase
                </ButtonSidebar>
            </li>
        </ul>
        <hr>
    </div>
</template>
