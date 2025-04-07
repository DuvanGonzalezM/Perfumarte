<script setup>
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AccountControls from '@/Components/AccountControls.vue';
import { usePage } from '@inertiajs/vue3';
import { watchEffect, ref, onMounted } from 'vue';
import InfoLocation from '@/Components/InfoLocation.vue';
import { is } from 'laravel-permission-to-vuejs';
import Loader from '@/Components/Loader.vue';

const { props } = usePage();
const isMobile = ref(false);
const isTablet = ref(false);

const attributes = defineProps({
    loading: {
        type: Boolean,
        required: false
    },
});

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 576;
    isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
}

watchEffect(() => {
     window.Laravel = window.Laravel || {}
     if (props.jsPermissions) {  
          window.Laravel.jsPermissions = props.jsPermissions;
     }
})
</script>
<template>
    <Loader v-if="attributes.loading"/>
    <div class="container container-prais" v-cloak :class="{ 'container-mobile': isMobile, 'container-tablet': isTablet }">
        <div class="row responsive-flex" :class="{ 'g-2': isMobile, 'g-3': isTablet }">
            <div :class="[
                !props.sidebarHidden ? (isMobile ? 'col-12 mb-3' : isTablet ? 'col-sm-12 mb-3' : 'col-md-3 col-sm-12') : ''
            ]">
                <Sidebar v-if="!props.sidebarHidden" />
            </div>
            <div :class="[
                props.sidebarHidden ? 'col-12' : (isMobile ? 'col-12' : isTablet ? 'col-sm-12' : 'col-md-9 col-sm-12'), 
                isMobile && !props.sidebarHidden ? 'mt-4' : '', 
                'sidebar-hidden',
                { 'ps-md-4': !isMobile && !isTablet }
            ]">
                <header class="container p-0" :class="{ 'header-mobile': isMobile, 'header-tablet': isTablet }">
                    <div class="row mb-3" :class="{ 'g-2': isMobile }">
                        <div :class="[
                            isMobile ? 'col-12 mb-2' : isTablet ? 'col-sm-10' : 'col-md-10 col-sm-12'
                        ]">
                            <!-- <Breadcrumbs /> -->
                        </div>
                        <div :class="[
                            isMobile ? 'col-12 d-flex justify-content-center' : 
                            isTablet ? 'col-sm-2 d-flex justify-content-end' : 
                            'col-md-2 col-sm-12 d-flex justify-content-end',
                            'account-controls-container'
                        ]">
                            <AccountControls />
                        </div>
                        <div class="row mt-3 justify-content-end" v-if="is('Asesor comercial')">
                            <InfoLocation />
                        </div>
                    </div>
                    <div class="row">
                        <slot name="header" />
                    </div>
                </header>

                <main :class="{ 'main-mobile': isMobile, 'main-tablet': isTablet }">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>