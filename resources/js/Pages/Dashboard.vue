<script setup>
import Alert from '@/Components/Alert.vue';
import Notification from '@/Components/Notification.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const itemsTable = [
        { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
        { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
        { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
        { age: 38, first_name: 'Jami', last_name: 'Carney' }
    ];
</script>

<template>

    <Head title="Inicio" />

    <BaseLayout>
        <template #header>
            <h1>Inicio</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>{{ $page.props.auth.user.username }}</strong>
            </template>
            <div class="container">
                <Alert/>
                <Notification/>
                <Table :items="itemsTable" :perPage="10" />
            </div>
        </SectionCard>
    </BaseLayout>
</template>