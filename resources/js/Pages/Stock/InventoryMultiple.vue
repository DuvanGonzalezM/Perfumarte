<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import TextInput from '@/Components/TextInput.vue';
import Table from '@/Components/Table.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import ModalPrais from '@/Components/ModalPrais.vue';
import { computed } from 'vue';

const props = defineProps({
    locations: {
        type: Array,
    },
    inventory: {
        type: Array,
    },
    warehouses: {
        type: Array,
    },
    products: {
        type: Array,
    },

});

const location = ref(null);
const inventory = ref([]);
const showTable = ref(false);
const warehouse = ref(null);
const locationSelect = [];
const locationFor = ref(props.locations.map((location) => location.warehouses.map((warehouse) => locationSelect.push({ 'title': warehouse.name + ' - ' + location.name, 'value': warehouse.warehouse_id }))));

const selectedWarehouse = async () => {
    try {
        const response = await axios.get(route('api.warehouse', location.value));
        inventory.value = response.data['inventory'];
        warehouse.value = response.data['warehouse'];
        showTable.value = true;
    } catch (error) {
        showTable.value = false;
        console.error('Error al cargar el inventario:', error);
    }
};

const openEditModal = () => {

    showEditModal.value = true;
    formEdit.warehouse_id = location.value;
    formEdit.products = inventory.value.map(item => ({
        product_id: item.product_id,
        reference: item.product.reference,
        name: item.product.name,
        quantity: item.quantity,
        action: 'add'
    }));
};

const showConfirmEditModal = ref(false);
const showEditModal = ref(false);
const showSuccessEditModal = ref(null);

const formEdit = useForm({
    warehouse_id: null,
    products: []
});

const submitEdit = () => {
    if (!formEdit.warehouse_id) return;
    formEdit.put(route('stock.inventory.update', formEdit.warehouse_id), {
        onSuccess: () => {
            showEditModal.value = false;
            showConfirmEditModal.value = false;
            showSuccessEditModal.value = true;
        },
        onError: (errors) => {
            showConfirmEditModal.value = false;
            showSuccessEditModal.value = false;
        }
    });
};

const newProduct = ref({
    product_id: null,
    quantity: null
});

const availableProducts = computed(() => {
    const currentProducts = formEdit.products || [];
    return props.products.filter(product =>
        !currentProducts.some(item => item.product_id === product.product_id)
    );
});

const addNewProduct = () => {

    formEdit.products.push({
        product_id: newProduct.value.product_id,
        reference: availableProducts.value.find(p => p.product_id === newProduct.value.product_id).reference,
        name: availableProducts.value.find(p => p.product_id === newProduct.value.product_id).name,
        quantity: newProduct.value.quantity,
        action: 'add'
    });

    newProduct.value = {
        product_id: null,
        quantity: null
    };
};

const removeProduct = (index) => {
    if (formEdit.products.length > 0) {
        formEdit.products.splice(index, 1);
    }
};

const columnsTable = [
    {
        data: 'product.reference',
        title: 'REFERENCIAS'
    },
    {
        data: 'product.category',
        title: 'CATEGORIA'
    },
    {
        data: 'null',
        title: 'CANTIDAD',
        render: function (data, type, row) {
            return row.quantity + ' ' + row.product.measurement_unit.replace('KG', 'ml');
        }
    },
    {
        data: 'product.supplier.name',
        title: 'PROVEEDOR'
    },
];
</script>
<template>

    <Head :title="warehouse ? warehouse.name : 'Sedes'" />

    <BaseLayout>
        <template #header>
            <h1>{{ warehouse ? warehouse.name : 'Sedes' }}</h1>
        </template>

        <SectionCard>
            <template #headerSection>
                <strong>Stock {{ warehouse ? warehouse.name : 'Sedes' }}</strong>
            </template>
            <div class="container ">
                <div class="row">
                    <div class="col-md-8">
                        <SelectSearch v-model="location" :options="locationSelect" :changeFunction="selectedWarehouse"
                            labelValue="Bodega" />
                    </div>
                    <div class="col-md-4">
                        <PrimaryButton @click="openEditModal" class="px-5 w-100" :disabled="!location">
                            Actualizar
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="row my-5">
                <div class="col">
                    <Table :data="inventory" :columns="columnsTable" />
                </div>
            </div>
            <div class="row my-5 text-center">
                <div class="col">
                    <PrimaryButton :href="route('stock.dashboard')" class="px-5">
                        Volver
                    </PrimaryButton>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>

    <ModalPrais v-model="showConfirmEditModal" @close="showConfirmEditModal = false">
        <template #header>
            Editar
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>¿Estás seguro de que quieres editar este inventario?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="submitEdit" class="px-5">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showEditModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showSuccessEditModal" @close="showSuccessEditModal = false">
        <template #header>
            Editar
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Inventario editado con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showEditModal" @close="showEditModal = false">
        <template #header>
            Editar inventario
        </template>
        <template #body>
            <div class="mb-3">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label fw-bold">Productos en inventario</label>
                    </div>
                </div>
                <div class="overflow-y-auto" style="max-height: 300px; height: 300px; overflow-x: hidden;">
                    <div v-for="(product, index) in formEdit.products" :key="product.product_id" class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{ product.reference }} - {{ product.category }}</label>
                            </div>
                            <div class="col-md-3">
                                <input v-model="product.quantity" type="number" class="form-control" min="0"
                                    :placeholder="product.quantity">
                            </div>
                            <div class="col-md-2">
                                <i class="fa-solid fa-trash " style="cursor: pointer;" @click="removeProduct(index)">
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Agregar nuevo producto</label>
                <div class="row">
                    <div class="col-md-5">
                        <SelectSearch v-model="newProduct.product_id" :options="availableProducts.map(product => ({
                            'title': `${product.reference} - ${product.category}`,
                            'value': product.product_id
                        }))" placeholder="Seleccionar producto" />
                    </div>
                    <div class="col-md-3">
                        <TextInput v-model="newProduct.quantity" type="number" min="1"
                            placeholder="Cantidad" />
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" :disabled="!newProduct.product_id || !newProduct.quantity"
                            @click="addNewProduct">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <button type="button" class="btn btn-secondary" @click="showEditModal = false">
                Cancelar
            </button>
            <button type="button" class="btn btn-primary" @click="showConfirmEditModal = true; showEditModal = false"
                :disabled="!formEdit.products.length">
                Guardar cambios
            </button>
        </template>
    </ModalPrais>
</template>