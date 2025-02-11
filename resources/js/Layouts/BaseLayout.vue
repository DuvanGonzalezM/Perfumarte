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
    <div class="container container-prais" v-cloak>
        <div class="row">
            <div class="col-3" v-if="!props.sidebarHidden">
                <Sidebar />
            </div>
            <div :class="props.sidebarHidden ? 'col-12' : 'col-9'">
                <header class="container p-0">
                    <div class="row mb-3">
                        <div class="col-10">
                            <!-- <Breadcrumbs /> -->
                        </div>
                        <div class="col-2 d-flex justify-content-end">
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

                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>