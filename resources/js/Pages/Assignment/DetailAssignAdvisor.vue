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
});

const advisorList = ref([]);


const form = useForm({
    caja1: 
        {
            'location_id': props.getSede.location_id,
            'user_id': '',
        }
    ,
    caja2: 
        {
            'location_id': props.getSede.location_id,
            'user_id': '',
        }
    ,       
    advisorList: [
    ],
});
const addAdvisor = () => {
    form.advisorList.push({
        location_id: props.getSede.location_id,
        user_id: '',
    });
};

const removeAdvisor = (index) => {
    form.advisorList.splice(index, 1);
};

// const optionSedes = ref(props.getSede.map((location) => ({ 'title': location.name, 'value': location.location_id })));
const optionAdvisors = ref(props.advisors.map((advisor) => ({ 'title': advisor.name, 'value': advisor.user_id })));
console.log(form);
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

    <BaseLayout>
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
                                    <!-- <th>
                                        <form @submit.prevent="submit">
                                            <SelectSearch class="mt-5" :options="optionSedes"  labelValue="Sede" />
                                        </form>
                                    </th> -->
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
                                        <SelectSearch :options="optionAdvisors" v-model="advisor.user_id"
                                            labelValue=" " />
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
                                <PrimaryButton :href="route('dashboard')" class="px-5">
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