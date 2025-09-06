    <script setup>
import InputError from '@/Components/InputError.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    locations: {
        type: Array,
        required: true
    },
    zones: {
        type: Array,
        required: true
    },
    warehouses: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: '',
    address: '',
    zone_id: null,
    cash_base: null,
    price30: null,
    price50: null,
    price100: null,
});

const formEdit = useForm({
    id: null,
    name: '',
    address: '',
    zone_id: null,
    cash_base: null,
    price30: '',
    price50: '',
    price100: '',
});
console.log(formEdit);

const openModal = () => {
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    form.post(route('locations.store'), {
        onSuccess: () => {
            showModal.value = false;
            showSuccessCreateModal.value = true;
        },
        onError: (errors) => {
            showSuccessCreateModal.value = false;
        }
    });
};

const optionsZones = computed(() => {
    return props.zones.map(zone => ({
        title: zone.zone_name,
        value: zone.zone_id
    }));
});

const userToDelete = ref(null);
const showEditModal = ref(false);
const showConfirmEditModal = ref(false);
const showSuccessEditModal = ref(null);
const showSuccessCreateModal = ref(null);
const showConfirmDeleteModal = ref(null);
const showSuccessDeleteModal = ref(null);

const showModal = ref(false);

const openEditModal = (location) => {
    locationToEdit.value = location;

    const warehouse = location.warehouses?.[0]; 

    formEdit.id = location.location_id;
    formEdit.name = location.name;
    formEdit.address = location.address;
    formEdit.zone_id = location.zone_id;
    formEdit.cash_base = location.cash_base;
    formEdit.price30 = warehouse?.price30 ?? null;
    formEdit.price50 = warehouse?.price50 ?? null;
    formEdit.price100 = warehouse?.price100 ?? null;

    showEditModal.value = true;
};

const submitEdit = () => {
    if (!locationToEdit.value) return;
    
    formEdit.put(route('locations.update', locationToEdit.value.location_id), {
        onSuccess: () => {
            showConfirmEditModal.value = false;
            showEditModal.value = false;
            showSuccessEditModal.value = true;
        },
        onError: (errors) => {
            showSuccessEditModal.value = false;
        }
    });
};

const submitDelete = () => {
    if (!userToDelete.value) return;
    
    form.delete(route('locations.destroy', { location_id: userToDelete.value }), {
        onSuccess: () => {
            showSuccessDeleteModal.value = true;
            showConfirmDeleteModal.value = false;
            userToDelete.value = null;
        },
        onError: (errors) => {
            showSuccessDeleteModal.value = false;
        }
    });
};

const locationToEdit = ref(null);


const columnsTable = [
    {
        data: 'location_id',
        title: 'Id Sede'
    },
    {
        data: "name",
        title: 'Nombre Sede',
    },
    {
        data: 'address',
        title: 'Dirección',
    },
    {
        data: 'cash_base',
        title: 'Base de Caja',
        render: function (data) {
            return new Intl.NumberFormat('es-CO', { style:'currency',currency:'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(data);
        }
        ,
    },
    {
        data: 'zone.zone_name',
        title: 'Zona',
    },
    {
        data: 'location_id',
        title: 'Detalle',
        render: function (data) {
            return '<a href="' + route("locations.detail", data) + '"><i class="fa-solid fa-eye"></i></a>';
        },
    },
    {
        data: 'location_id',
        title: 'Editar',
        defaultContent: "",
        createdCell: function (td, cellData, rowData) {
            td.innerHTML = "";
            const icon = document.createElement("i");
            icon.className = "fa-solid fa-pen-to-square cursor-pointer";
            icon.className = "fa-solid fa-pen-to-square  cursor-pointer";
            icon.style.cursor = "pointer";
            icon.addEventListener("click", () => openEditModal(rowData));
            td.appendChild(icon);
        }
    },
    {
        data: 'location_id',
        title: 'Eliminar',
        defaultContent: "",
        createdCell: function (td, cellData, rowData) {
            td.innerHTML = "";
            const icon = document.createElement("i");
            icon.className = "fa-solid fa-trash cursor-pointer";
            icon.style.cursor = "pointer";
            icon.addEventListener("click", () => {
                userToDelete.value = rowData.location_id;
                showConfirmDeleteModal.value = true;
            });
            td.appendChild(icon);
        }
    },
];

</script>



<template>
    <Head title="Sedes" />
    <BaseLayout :loading="form.processing || formEdit.processing ? true : false">
        <template #header>
            <h1>Sedes</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Sedes</strong>
            </template>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <PrimaryButton @click="openModal" class="px-5">
                            Nueva Sede
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <Table :columns="columnsTable" :data="props.locations" />
        </SectionCard>
    </BaseLayout>

    <ModalPrais v-model="showModal" @close="showModal = false">
        <template #header>
            Nuevo Sede
        </template>
        <template #body>
            <form @submit.prevent="submit">
                <div>
                    <TextInput labelValue="Nombre" id="name" name="name" type="text" v-model="form.name" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <TextInput labelValue="Dirección" id="address" name="address" type="text"
                        v-model="form.address" required autocomplete="address" />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Base de Caja" id="cash_base" name="cash_base" type="number"
                        v-model="form.cash_base" required autocomplete="cash_base" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 30 ml" id="price30" name="price30" type="number"
                        v-model="form.price30" required autocomplete="price30" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 50 ml" id="price50" name="price50" type="number"
                        v-model="form.price50" required autocomplete="price50" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 100 ml" id="price100" name="price100" type="number"
                        v-model="form.price100" required autocomplete="price100" />
                </div>
                <div class="mt-4">
                    <SelectSearch v-model="form.zone_id" :options="optionsZones" labelValue="Zona" required />
                    <InputError class="mt-2" :message="form.errors.zone_id" />
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="submit" class="px-5">
                Guardar
            </PrimaryButton>
            <PrimaryButton @click="showModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showEditModal" @close="showEditModal = false">
        <template #header>
            Editar Sede 
        </template>
        <template #body>
            <form @submit.prevent="submitEdit">
                <div>
                    <TextInput labelValue="Nombre" id="edit-name" type="text" v-model="formEdit.name"
                        required autofocus autocomplete="name" />
                    <InputError class="mt-2" :message="formEdit.errors.name" />
                </div>

                <div class="mt-4">
                    <TextInput labelValue="Dirección" id="edit-address" type="text"
                        v-model="formEdit.address" required autocomplete="address" />
                    <InputError class="mt-2" :message="formEdit.errors.address" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Base de Caja" id="edit-cash_base" type="number"
                        v-model="formEdit.cash_base" required autocomplete="cash_base" />
                    <InputError class="mt-2" :message="formEdit.errors.cash_base" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 30 ml" id="edit-price30" type="number"
                        v-model="formEdit.price30" required autocomplete="price30" />
                    <InputError class="mt-2" :message="formEdit.errors.price30" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 50 ml" id="edit-price50" type="number"
                        v-model="formEdit.price50" required autocomplete="price50" />
                    <InputError class="mt-2" :message="formEdit.errors.price50" />
                </div>
                <div class="mt-4">
                    <TextInput labelValue="Precio 100 ml" id="edit-price100" type="number"
                        v-model="formEdit.price100" required autocomplete="price100" />
                    <InputError class="mt-2" :message="formEdit.errors.price100" />
                </div>

                <div class="mt-4">
                    <SelectSearch v-model="formEdit.zone_id" :options="optionsZones" labelValue="Zona" required />
                    <InputError class="mt-2" :message="formEdit.errors.zone_id" />
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="showConfirmEditModal = true; showEditModal = false" class="px-5">
                Guardar
            </PrimaryButton>
            <PrimaryButton @click="showEditModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showConfirmEditModal" @close="showConfirmEditModal = false" :loading="formEdit.processing ? true : false">
        <template #header>
            Confirmar Edición
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>¿Estás seguro de que quieres editar esta sede?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="submitEdit" class="px-5">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showConfirmEditModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showSuccessEditModal" @close="showSuccessEditModal = false">
        <template #header>
            Editar Sede
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Sede editada con éxito</h3>
            </div>
        </template>
    </ModalPrais>


    <ModalPrais v-model="showSuccessCreateModal" @close="showSuccessCreateModal = false">
        <template #header>
            Crear Sede
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Sede creada con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showSuccessDeleteModal" @close="showSuccessDeleteModal = false">
        <template #header>
            Eliminar Sede
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Sede eliminada con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showConfirmDeleteModal" @close="showConfirmDeleteModal = false" :loading="form.processing ? true : false">
        <template #header>
            Confirmar Eliminación
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>¿Estás seguro de que quieres eliminar esta sede?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="submitDelete" class="px-5">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showConfirmDeleteModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

</template>