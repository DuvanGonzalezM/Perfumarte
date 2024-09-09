<script setup>
import ButtonSidebar from './ButtonSidebar.vue';
</script>

<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-card">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="assets\images\Logo_1.png" style="width: 80px; height: 80px;" />
            <span class="mx-4"style="font-size: 21px;">Jefe de operaciones</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <ButtonSidebar href="#">
                    <i class="fa-solid fa-house-chimney me-4 i"></i>
                    Inicio
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-solid fa-rectangle-list me-4 i"></i>
                    Ordenes de compra
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-solid fa-truck me-4 i"></i>
                    Despachos
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-brands fa-wpforms me-4 i"></i>
                    Solicitud insumos
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-solid fa-flask me-4 i"></i>
                    Transformaciones
                </ButtonSidebar>
            </li>
            <li>
                <ButtonSidebar href="#">
                    <i class="fa-duotone fa-solid fa-boxes-stacked me-4 i"></i>
                    Stock
                </ButtonSidebar>
            </li>
        </ul>
        <hr>
    </div>
</template>
