<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'username',
        title: 'Nombre'
    },
    {
        data: "user_id",
        title: 'Roles / Permisos',
        render: function (data) {
            return '<a href="' + route("users.detail", data) + '"><i class="fa-solid fa-user-shield"></i></a>';
        }
    },
];

</script>

<template>

    <Head title="Usuarios" />
    <BaseLayout>
        <template #header>
            <h1>Usuarios</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Usuarios</strong>
            </template>
            <div class="container">
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('roles.list')" class="px-5">
                            Roles
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton :href="route('permissions.list')" class="px-5">
                            Permisos
                        </PrimaryButton>
                    </div>
                </div>
                <Table :data="users" :columns="columnsTable" />
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            <i class="fa-solid fa-user-plus"></i>
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>