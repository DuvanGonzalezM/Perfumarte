<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { Head } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SelectSearch from '@/Components/SelectSearch.vue';

const props = defineProps({
    initialInventory: Object,
    location: Object,
    sidebarHidden: Boolean
});

const columnsTable = [
    {
        data: 'product.reference',
        title: 'REFERENCIAS'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity + ' ' + row.product.measurement_unit.replace('KG', 'ml');
        }
    },
    {
        data: 'product.category',
        title: 'GENERO'
    },
];

const form = useForm({
    accepted: false,
    count_100_bill: null,
    count_50_bill: null,
    count_20_bill: null,
    count_10_bill: null,
    count_5_bill: null,
    count_2_bill: null,
    total_coins: null,
    inventoryData: props.initialInventory,
});

const showModal = ref(false);

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const handleSubmit = () => {
    form.accepted = true;
    form.post(route('inventory.accept'), {
        onSuccess: () => {
            window.location.href = route('inventory.current');
        }
    });
    closeModal();
};
</script>

<template>
    <Head title="Inventario Inicial" />
    <BaseLayout :loading="form.processing ? true : false">
        <SectionCard :subtitle="'Base: $'+props.location.cash_base">
            <template #headerSection>
                <strong>Inventario Inicial</strong>
            </template>
            
            <div class="container">
                <PrimaryButton @click="openModal" class="position-absolute" :disabled="form.processing">
                    Confirmar
                </PrimaryButton>
                <Table :data="props.initialInventory" :columns="columnsTable" />
            </div>
        </SectionCard>

        <ModalPrais v-model="showModal" @close="closeModal">
            <template #header>
                <h4>¿Confirma la base y el inventario inicial para continuar con el módulo?</h4>
            </template>
            <template #body>
                <div class="row">
                    <h5>Cantidades en caja ${{ (form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_100_bill * 100000) + (form.total_coins * 1) }}</h5>
                    
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_100_bill" id="count_100_bill" v-model="form.count_100_bill"
                            :focus="form.count_100_bill != null ? true : false" labelValue="Cantidad de billetes de 100 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_50_bill" id="count_50_bill" v-model="form.count_50_bill"
                            :focus="form.count_50_bill != null ? true : false" labelValue="Cantidad de billetes de 50 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_20_bill" id="count_20_bill" v-model="form.count_20_bill"
                            :focus="form.count_20_bill != null ? true : false" labelValue="Cantidad de billetes de 20 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_10_bill" id="count_10_bill" v-model="form.count_10_bill"
                            :focus="form.count_10_bill != null ? true : false" labelValue="Cantidad de billetes de 10 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_5_bill" id="count_5_bill" v-model="form.count_5_bill"
                            :focus="form.count_5_bill != null ? true : false" labelValue="Cantidad de billetes de 5 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-6 mt-4">
                        <TextInput type="number" name="count_2_bill" id="count_2_bill" v-model="form.count_2_bill"
                            :focus="form.count_2_bill != null ? true : false" labelValue="Cantidad de billetes de 2 mil"
                            :minimo="0"
                            :required="true" />
                    </div>
                    <div class="col-md-12 mt-4">
                        <TextInput type="number" name="total_coins" id="total_coins" v-model="form.total_coins"
                            :focus="form.total_coins != null ? true : false" labelValue="Cantidad total de monedas"
                            :minimo="0"
                            :required="true" />
                    </div>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="closeModal" class="mx-5 px-5">No</PrimaryButton>
                <PrimaryButton @click="handleSubmit" :class="((form.count_50_bill * 50000) + (form.count_20_bill * 20000) + (form.count_10_bill * 10000) + (form.count_5_bill * 5000) + (form.count_2_bill * 2000) + (form.count_100_bill * 100000) + (form.total_coins * 1)) != (props.location.cash_base) ? 'disabled' : ''" class="mx-5 px-5">Sí</PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>
