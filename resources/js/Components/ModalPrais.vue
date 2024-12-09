<script setup>
import PrimaryButton from './PrimaryButton.vue';

const model = defineModel();

</script>

<template>
    <Transition name="modal">
        <div v-if="model" class="modal modal-prais">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="Permission">
                            <slot name="header">Title</slot>
                        </h1>
                        <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <slot name="body"></slot>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <slot name="footer">
                            <PrimaryButton @click="$emit('close')" class="px-5">
                                Cerrar
                            </PrimaryButton>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>