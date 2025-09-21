<script setup>
import { ref, onMounted, computed } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';

const model = defineModel();

const props = defineProps({
    rangeEnabled: {
        type: Boolean,
        default: false
    },
    labelValue: {
        type: String,
    },
    id: {
        type: String,
        required: true,
    },
});
const hasFocus = ref(false);
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
    <div class="inputContainer position-relative dateinput">
        <VueDatePicker
            :id="id" 
            v-model="model" 
            locale="es-CO" 
            :format="'dd/MM/yyyy HH:mm'"
            :range="rangeEnabled"
            :class="{ 
                'focus': hasFocus || model,
                'select-mobile': isMobile,
                'select-tablet': isTablet
            }"
        />
        <label class="position-absolute pe-none" 
               ref="label" 
               :for="id" 
               v-if="labelValue"
               :class="{
                   'label-mobile': isMobile,
                   'label-tablet': isTablet
               }">
            {{ labelValue }}
        </label>
    </div>

</template>
