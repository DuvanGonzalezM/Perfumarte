<script setup>
import Alert from '@/Components/Alert.vue';
import InformationCard from '@/Components/informationCard.vue';
import Notification from '@/Components/Notification.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const columnsTable = [
    {
        data: 'age',
        title: 'Edad'
    },
    {
        data: 'first_name',
        title: 'Nombre'
    },
    {
        data: 'last_name',
        title: 'Apellido'
    },
];
const itemsTable = [
    { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
    { age: 38, first_name: 'Jami', last_name: 'Carney' },
    { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
    { age: 38, first_name: 'Jami', last_name: 'Carney' },
    { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
    { age: 38, first_name: 'Jami', last_name: 'Carney' },
    { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
    { age: 38, first_name: 'Jami', last_name: 'Carney' },
];
</script>

<template>

    <Head title="Inicio" />



    <BaseLayout>
        <template #header>
            <div>
                <h1>Inicio</h1>
                <br></br>
                <br></br>
                <br></br>
            </div>

            <!-- Contenedor con clases de Bootstrap para organizar horizontalmente -->
            <div class="row">
                <div class="col-md-4">
                    <InformationCard />
                </div>
            </div>
        </template>
    </BaseLayout>

</template>
