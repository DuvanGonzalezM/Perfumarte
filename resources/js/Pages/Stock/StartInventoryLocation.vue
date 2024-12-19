<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    initialInventory: Object,
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
        <SectionCard :subtitle="'Base: $500.000'">
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
                <h3>Confirmación</h3>
            </template>
            <template #body>
                <p>¿Confirma la base y el inventario inicial para iniciar el módulo?</p>
            </template>
            <template #footer>
                <PrimaryButton @click="closeModal" class="mx-5 px-5">No</PrimaryButton>
                <PrimaryButton @click="handleSubmit" class="mx-5 px-5">Sí</PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>
