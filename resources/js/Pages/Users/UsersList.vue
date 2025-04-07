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
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
    },
    roles: {
        type: Array,
    },
    boss: {
        type: Array,
    }
});


const form = useForm({
    name: '',
    username: '',
    role_id: '',
    boss_user: '',
    location_id: 1,
    enabled: '',
});


const formEdit = useForm({
    id: null,
    name: '',
    username: '',
    boss_user: null,
    enabled: '',
});

const openEditModal = (user) => {
    userToEdit.value = user;
    formEdit.id = user.user_id;
    formEdit.name = user.name;
    formEdit.username = user.username;
    formEdit.boss_user = parseInt(user.boss_user);
    formEdit.enabled = user.enabled;
    showEditModal.value = true;
};

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]))
const optionsBoss = ref(props.boss.map((user) => [{ 'title': user.name, 'value': user.user_id }][0]))
const userToDelete = ref(null);
const showModal = ref(false);
const showEditModal = ref(false);
const showSuccessCreateModal = ref(null);
const showSuccessEditModal = ref(null);
const showSuccessDeleteModal = ref(null);
const showConfirmDeleteModal = ref(null);
const showConfirmEditModal = ref(null);


const openModal = () => {
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            showSuccessCreateModal.value = true;
            showModal.value = false;
        },
        onError: (errors) => {
            
        }
    });
};


const submitEdit = () => {
    if (!userToEdit.value) return;
    formEdit.put(route('users.edit', userToEdit.value.user_id), {
        onSuccess: () => {
            showSuccessEditModal.value = true;
            showConfirmEditModal.value = false;
            showEditModal.value = false;
        },
    });
};
const deleteUser = () => {
    if (!userToDelete.value) return;
    form.delete(route('users.destroy', { user_id: userToDelete.value }), {
        onSuccess: () => {
            showSuccessDeleteModal.value = true;
            showConfirmDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};



const columnsTable = [
    {
        data: 'name',
        title: 'Nombre'
    },
    {
        data: "user_id",
        title: 'Roles / Permisos',
        render: function (data) {
            return '<a href="' + route("users.detail", data) + '"><i class="fa-solid fa-user-shield"></i></a>';
        }
    },
    {
        data: 'user_id',
        title: 'Editar',
        defaultContent: "",
        createdCell: function (td, cellData, rowData) {
            td.innerHTML = "";
            const icon = document.createElement("i");
            icon.className = "fa-solid fa-pen-to-square text-primary cursor-pointer";
            icon.style.cursor = "pointer";
            icon.addEventListener("click", () => openEditModal(rowData));
            td.appendChild(icon);
        }
    },
    {
    data: 'user_id',
    title: 'Eliminar',
    defaultContent: "",
    createdCell: function (td, cellData, rowData) {
        td.innerHTML = "";
        const icon = document.createElement("i");
        icon.className = "fa-solid fa-trash text-danger cursor-pointer";
        icon.style.cursor = "pointer";
        icon.addEventListener("click", () => {
            userToDelete.value = rowData.user_id;
            showConfirmDeleteModal.value = true ;
        });
        td.appendChild(icon);
    }
}

];

const userToEdit = ref(null);
</script>

<template>
    <Head title="Usuarios" />
    <BaseLayout :loading="form.processing || formEdit.processing ? true : false">
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
                        <PrimaryButton @click="openModal" class="px-5">
                            Nuevo Usuario
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <Table :columns="columnsTable" :data="props.users" />
        </SectionCard>
    </BaseLayout>

    <ModalPrais v-model="showSuccessCreateModal" @close="showSuccessCreateModal = false">
        <template #header>
            Crear Usuario
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Usuario creado con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showConfirmEditModal" @close="showConfirmEditModal = false" :loading="formEdit.processing ? true : false ">
        <template #header>
            Confirmar Edición
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>¿Estás seguro de que quieres editar este usuario?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="submitEdit(userToEdit.value)" class="px-5">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showConfirmEditModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showConfirmDeleteModal" @close="showConfirmDeleteModal = false">
        <template #header>
            Confirmar Eliminación
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>¿Estás seguro de que quieres eliminar este usuario?</h3>
            </div>
        </template>
        <template #footer>
            <PrimaryButton @click="deleteUser(userToDelete.value)" class="px-5">
                Confirmar
            </PrimaryButton>
            <PrimaryButton @click="showConfirmDeleteModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showSuccessEditModal" @close="showSuccessEditModal = false">
        <template #header>
            Editar Usuario
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Usuario editado con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showSuccessDeleteModal" @close="showSuccessDeleteModal = false">
        <template #header>
            Eliminar Usuario
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Usuario eliminado con éxito</h3>
            </div>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showModal" @close="showModal = false">
        <template #header>
            Nuevo Usuario
        </template>
        <template #body>
            <form @submit.prevent="submit">
                <div>
                    <TextInput labelValue="Nombre" id="name" type="text" v-model="form.name" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <TextInput labelValue="Nombre de usuario" id="username" type="text" v-model="form.username" required
                        autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <div class="mt-4">
                    <SelectSearch v-model="form.role_id" :options="optionsRoles" labelValue="Rol"
                        required />
                    <InputError class="mt-2" :message="form.errors.role_id" />
                </div>

                <div class="mt-4">
                    <SelectSearch v-model="form.boss_user" :options="optionsBoss" labelValue="Jefe"
                        required />
                    <InputError class="mt-2" :message="form.errors.boss_user" />
                </div>

                <div class="mt-4">
                    <label :for="enabled">¿Utilizara la caja? </label>
                    <input id="enabled" type="checkbox" v-model="form.enabled" required />
                    <InputError class="mt-2" :message="form.errors.enabled" />
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="submit" class="px-5">
                Crear
            </PrimaryButton>
            <PrimaryButton @click="showModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>

    <ModalPrais v-model="showEditModal" @close="showEditModal = false">
        <template #header>
            Editar Usuario
        </template>
        <template #body>
            <form @submit.prevent="submitEdit">
                <div>
                    <TextInput labelValue="Nombre" id="edit-name" type="text" v-model="formEdit.name"
                        required autofocus autocomplete="name" />
                    <InputError class="mt-2" :message="formEdit.errors.name" />
                </div>

                <div class="mt-4">
                    <TextInput labelValue="Nombre de usuario" id="edit-username" type="text"
                        v-model="formEdit.username" required autocomplete="username" />
                    <InputError class="mt-2" :message="formEdit.errors.username" />
                </div>

                <div class="mt-4">
                    <SelectSearch v-model="formEdit.boss_user" :options="optionsBoss" labelValue="Jefe"
                        required />
                    <InputError class="mt-2" :message="formEdit.errors.boss_user" />
                </div>

                <div class="mt-4">
                    <label for="edit-enabled">¿Utilizará la caja?</label>
                    <input id="edit-enabled" type="checkbox" v-model="formEdit.enabled" />
                    <InputError class="mt-2" :message="formEdit.errors.enabled" />
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
</template>