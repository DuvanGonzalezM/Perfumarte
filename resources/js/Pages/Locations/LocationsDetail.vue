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

            <div class="col mb-2">
                <div class="col-12 p-3 cardboxprais cardpurcheorder position-relative">
                    <h6>Direccion: {{ location.address }}</h6>
                    <h6>Zona: {{ location.zone.zone_name}}</h6>

                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <CardButton :link="route('locations.inventory', location.location_id)" title="Inventario" :icon="icons[0]" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <CardButton :link="route('locations.personal', location.location_id)" title="Personal" :icon="icons[1]" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <CardButton :link="route('locations.sales', location.location_id)" title="Ventas" :icon="icons[2]" />
                </div>
            </div>


                    
            <div class="container">
                <PrimaryButton :href="route('locations.list')" class="px-5">
                    Regresar
                </PrimaryButton>
            </div>
        </SectionCard>
    </BaseLayout>
</template>

