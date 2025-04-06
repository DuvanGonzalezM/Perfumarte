<script setup>
import PrimaryButton from './PrimaryButton.vue';
import { ref, onMounted } from 'vue';

const model = defineModel();
const isMobile = ref(false);
const isTablet = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 576;
    isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
}
</script>

<template>
    <Transition name="modal">
        <div 
            v-if="model" 
            class="modal modal-prais"
            :class="{
                'modal-mobile': isMobile,
                'modal-tablet': isTablet
            }"
        >
            <div 
                class="modal-dialog"
                :class="{
                    'modal-dialog-mobile': isMobile,
                    'modal-dialog-tablet': isTablet
                }"
            >
                <div 
                    class="modal-content"
                    :class="{
                        'modal-content-mobile': isMobile,
                        'modal-content-tablet': isTablet
                    }"
                >
                    <div 
                        class="modal-header"
                        :class="{
                            'modal-header-mobile': isMobile,
                            'modal-header-tablet': isTablet
                        }"
                    >
                        <h1 
                            class="modal-title fs-5" 
                            id="Permission"
                            :class="{
                                'modal-title-mobile': isMobile,
                                'modal-title-tablet': isTablet
                            }"
                        >
                            <slot name="header">Title</slot>
                        </h1>
                        <button 
                            type="button" 
                            class="btn-close" 
                            @click="$emit('close')" 
                            aria-label="Close"
                            :class="{
                                'btn-close-mobile': isMobile,
                                'btn-close-tablet': isTablet
                            }"
                        ></button>
                    </div>
                    <div 
                        class="modal-body pt-4"
                        :class="{
                            'modal-body-mobile': isMobile,
                            'modal-body-tablet': isTablet
                        }"
                    >
                        <slot name="body"></slot>
                    </div>
                    <div 
                        class="modal-footer justify-content-center"
                        :class="{
                            'modal-footer-mobile': isMobile,
                            'modal-footer-tablet': isTablet
                        }"
                    >
                        <slot name="footer">
                            <PrimaryButton 
                                @click="$emit('close')" 
                                class="px-5"
                                :class="{
                                    'btn-mobile': isMobile,
                                    'btn-tablet': isTablet
                                }"
                            >
                                Cerrar
                            </PrimaryButton>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>