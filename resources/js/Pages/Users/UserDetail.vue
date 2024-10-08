<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: {
        type: Object,
    },
    roles: {
        type: Array,
    },
    permissions: {
        type: Array,
    }
});

const form = useForm({
    roles: null,
    permissions: null
});

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]));
const optionsPermission = ref(props.permissions.map((permission) => [{ 'title': permission.name, 'value': permission.id }][0]));

const submit = () => {
    form.post(route('orders.store'));
};
</script>

<template>

    <Head title="Usuarios" />
    <BaseLayout>
        <template #header>
            <h1>Usuarios</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>{{ props.user.username }}</strong>
            </template>

            <form @submit.prevent="submit" class="table-prais">
                <div class="row">
                    <h4>Roles</h4>
                    <div class="col pb-5 align-middle">
                        <SelectSearch :multiple="true" v-model="form.roles" :options="optionsRoles" />
                    </div>
                </div>

                <div class="row">
                    <h4>Permisos</h4>
                    <div class="col mb-5 align-middle">
                        <SelectSearch :multiple="true" v-model="form.permissions" :options="optionsPermission" />
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton @click="submit" class="px-5">
                            Enviar
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </SectionCard>
    </BaseLayout>
</template>