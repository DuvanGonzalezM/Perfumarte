<script setup>
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AccountControls from '@/Components/AccountControls.vue';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';

const { props } = usePage();

watchEffect(() => {
     window.Laravel = window.Laravel || {}
     if (props.jsPermissions) {  
          window.Laravel.jsPermissions = props.jsPermissions;
     }
})
</script>
<template>
    <div class="container container-prais">
        <div class="row">
            <div class="col-md-3" v-if="!props.sidebarHidden">
                <Sidebar />
            </div>
            <div :class="props.sidebarHidden ? 'col-md-12' : 'col-md-9'">
                <header class="container p-0">
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <!-- <Breadcrumbs /> -->
                        </div>
                        <div class="col-md-2 d-flex justify-content-end">
                            <AccountControls />
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