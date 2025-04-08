<script setup>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import SectionCard from '@/Components/SectionCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';
import CardButton from '@/Components/CardButton.vue';


const props = defineProps({
    location: {
        type: Object,
        required: true
    },
    currentInventory: Object
});

const icons = [
    'fa-boxes-stacked',
    'fa-solid fa-users',
    'fa-solid fa-cash-register',
    'fa-solid fa-file-signature'
];

    

</script>

<template>
    <Head title="Detalle de Sede" />
    <BaseLayout>
        <template #header>
            <h1>Detalle de Sede</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ location.name }}</strong>
            </template>
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 supplier-info">
                    <div class="info-card">
                        <strong>Direccion: </strong>
                        <span> {{ location.address }}</span><br>
                        <strong>Zona: </strong>
                        <span> {{ location.zone.zone_name}}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 p-5">
                    <CardButton :link="route('locations.inventory', location.location_id)" title="Inventario" :icon="icons[0]" />
                </div>

                <div class="col-6 p-5">
                    <CardButton :link="route('locations.personal', location.location_id)" title="Personal" :icon="icons[1]" />
                </div>

                <div class="col-6 p-5">
                    <CardButton :link="route('locations.sales', location.location_id)" title="Ventas" :icon="icons[2]" />
                </div>

                <div class="col-6 p-5">
                    <CardButton :link="route('locations.audit', location.location_id)" title="Auditorías" :icon="icons[3]" />
                </div>
            </div>

            <div class="row my-5 text-center">
                <div class="container">
                    <PrimaryButton :href="route('locations.list')" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>

