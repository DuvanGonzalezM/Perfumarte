<script setup>
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AccountControls from '@/Components/AccountControls.vue';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import InfoLocation from '@/Components/InfoLocation.vue';
import { is } from 'laravel-permission-to-vuejs';
import Loader from '@/Components/Loader.vue';

const { props } = usePage();

const attributes = defineProps({
    loading: {
        type: Boolean,
        required: false
    },
});

watchEffect(() => {
     window.Laravel = window.Laravel || {}
     if (props.jsPermissions) {  
          window.Laravel.jsPermissions = props.jsPermissions;
     }
})
</script>

<template>
    <Loader v-if="attributes.loading"/>
    <div class="container-prais">
        <div class="layout-container">
            <div class="sidebar-wrapper" v-if="!props.sidebarHidden">
                <Sidebar />
            </div>
            <div class="content-wrapper" :class="{'full-width': props.sidebarHidden}">
                <header>
                    <div class="header-container">
                        <div class="header-slot-wrapper">
                            <slot name="header" />
                        </div>
                        <div class="account-controls-container">
                            <AccountControls />
                        </div>
                        <div class="breadcrumbs-wrapper">
                            <!-- <Breadcrumbs /> -->
                        </div>
                        <div class="supplier-info info-location-wrapper" v-if="is('Asesor comercial')">
                            <InfoLocation />
                        </div>
                    </div>
                </header>

                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>