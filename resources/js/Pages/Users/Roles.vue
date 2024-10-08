<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { onMounted, ref } from 'vue';

const props = defineProps({
    permissions: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'name',
        title: 'Nombre'
    },
    {
        data: 'name',
        title: 'Editar',
        render: '#render',
    },
];
let myModal;
onMounted(() => {
    myModal = new Modal('#permission', {})
})

const isModalVisible = ref(false);
let methodPermission = 'create';
const form = useForm({
    name: null,
    id: null,
});

const editClick = (row) => {
    methodPermission = 'edit';
    form.name = row.name;
    form.id = row.id;
    myModal.show();
};

const createClick = () => {
    methodPermission = 'create';
    form.name = null;
    form.id = null;
    myModal.show();
};

const closeModal = () => {
    myModal.hide();
};

const submit = () => {
    if(methodPermission == 'create'){
        form.post(route('permissions.store'));
    } else if(methodPermission == 'edit') {
        form.put(route('permissions.update', form.id));
    }
    myModal.hide();
};

</script>

<template>

    <Head title="Permisos" />
    <BaseLayout>
        <template #header>
            <h1>Permisos</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Permisos</strong>
            </template>
            <div class="container">
                <PrimaryButton class="px-5" @click="createClick()">
                    Agregar
                </PrimaryButton>
                <Table :data="permissions" :columns="columnsTable">
                    <template #templateRender="items">
                        <a href="#" @click="editClick(items.item.rowData)"> <i class="fa-solid fa-pen-to-square"></i> </a>
                    </template>
                </Table>
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
                <div class="modal fade" id="permission" tabindex="-1" aria-labelledby="Permission" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="Permission">Crear Permiso</h1>
                                <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pt-4">
                                <form @submit.prevent="submit">
                                    <TextInput type="text" name="permission_name" id="permission_name"
                                        v-model="form.name" :focus="form.name != null ? true : false" labelValue="Nombre del permiso" :required="true" />
                                </form>
                            </div>
                            <div class="modal-footer">
                                <PrimaryButton @click="submit" class="px-5">
                                    Guardar
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>