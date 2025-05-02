<script setup>

import SectionCard from '@/Components/SectionCard.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import { ref } from 'vue';


const props = defineProps({
    getSede: {
        type: Object,
    },
    advisors: {
        type: Array,
    },
    users: {
        type: Array,
    },
});

const advisorList = ref([]);

const optionAdvisors = ref(props.advisors.map((advisor) => ({ 'title': advisor.name, 'value': advisor.user_id })));
const optionUsers = ref([
    ...props.users.map(user => ({ title: user.name, value: user.user_id })),
    ...props.advisors.map(advisor => ({ title: advisor.name, value: advisor.user_id })),
]);

const usersEnabled = props.getSede.users_location.filter((user) => user.enabled === "1").map((user) => ({ 'user_id': user.user_id }));

const form = useForm({
    location_id: props.getSede.location_id,
    caja1: 
        {
            'user_id': usersEnabled[0] ? usersEnabled[0].user_id : '',
        }
    ,
    caja2: 
        {
            'user_id': usersEnabled[1] ? usersEnabled[1].user_id : '',
        }
    ,       
    advisorList: props.getSede.users_location.filter((user) => !usersEnabled.find((u) => u.user_id === user.user_id)),
});


const addAdvisor = () => {
    form.advisorList.push({
        user_id: '',
    });
};

const removeAdvisor = (index) => {
    form.advisorList.splice(index, 1);
};

const submit = () => {
    form.post(route('store.Advisor'), {
        onSuccess: () => {
            resetForm(); 
        },
    });
};

const resetForm = () => {
    form.reset();
    advisorList.value = []; 
};

</script>

<template>

    <Head title="Detalle Asignacion" />

    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <!-- <Alert /> -->
            <h1>Detalle Asignacion</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong> {{ props.getSede.name }}</strong>

            </template>
                    <form class="table-prais">
                        <table class="table table-hover text-center dt-body-nowrap size-prais-2 mt-5">
                            <!-- <div class="row cardboxprais cardpurcheorder"> -->
                            <thead>
                                <tr>
                                    <th>
                                        <form @submit.prevent="submit">
                                            <SelectSearch class="mt-5" :options="optionAdvisors" v-model="form.caja1.user_id"
                                                labelValue="Asesor de caja 1" />
                                        </form>
                                    </th>
                                    <th>
                                        <form @submit.prevent="submit">
                                            <SelectSearch class="mt-5" :options="optionAdvisors" v-model="form.caja2.user_id"
                                                labelValue="Asesor de caja 2" />
                                        </form>
                                    </th>
                                </tr>
                            </thead>
                            <!-- </div> -->
                            <tbody id="AdvisorsList">
                                <tr v-for="(advisor, index) in form.advisorList" :key="index">
                                    <td>Asesor {{ index + 1 }}:</td>
                                    <td>
                                        <SelectSearch :options="optionUsers" v-model="advisor.user_id"
                                            labelValue="" />
                                    </td>
                                    <div class="removeItem" @click="removeAdvisor(index)">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row text-center justify-content-center my-5">
                            <div class="addItem" @click="addAdvisor">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        </div>
                        <div class="row my-5">
                            <div class="col-6">
                                <PrimaryButton :href="route('list.location')" class="px-5">
                                    Volver
                                </PrimaryButton>
                            </div>
                            <div class="col-6 text-end">
                                <PrimaryButton @click="submit" class="px-5">
                                    Registrar
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
        </SectionCard>
    </BaseLayout>
</template>