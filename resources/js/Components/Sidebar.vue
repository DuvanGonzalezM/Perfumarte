<script setup>
import Dashboard from '@/Pages/Dashboard.vue';
import ButtonSidebar from './ButtonSidebar.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-card">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="\assets\images\Logo_1.png" />
            <h5 class="mx-2">{{ $page.props.auth.user.username }}</h5>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <ButtonSidebar :href="route('dashboard')" :class="{'active': $page.component.startsWith('Dashboard')}">
                    <i class="fa-solid fa-house-chimney me-4 "></i>
                    Inicio
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar :href="route('orders.list')" :class="{'active': $page.component.startsWith('ChiefOperating')}">
                    <i class="fa-solid fa-rectangle-list me-4 "></i>
                    Ordenes de compra
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-solid fa-truck me-4 "></i>
                    Despachos
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar :href="route('suppliesrequest.list')" :class="{'active': $page.component.startsWith('Requests')}">
                    <i class="fa-brands fa-wpforms me-4 i"></i>
                    Solicitud insumos
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar :href="route('transformationRequest.list')" :class="{'active': $page.component.startsWith('RequestTransformation')}">
                    <i class="fa-solid fa-flask me-4 "></i>
                    Transformaciones
                </ButtonSidebar>  
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-duotone fa-solid fa-boxes-stacked me-4 "></i>
                    Stock
                </ButtonSidebar>
            </li>
        </ul>
        <Link :href="route('logout')" method="post" as="button">
        Log Out
        </Link>
        <hr>
    </div>
</template>
