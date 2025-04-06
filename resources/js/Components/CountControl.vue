<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    title: {
        type: String,
    },
    min: {
        type: Number,
        default: 0
    }
});

const model = defineModel();
const isMobile = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>

<template>
    <div class="row text-center justify-content-center count-control">
        <div class="col-12">
            <h4 class="d-flex justify-content-center mb-2 count-value">{{ model }}</h4>
            <div class="count-title">{{ props.title }}</div>
        </div>
        <div class="count-buttons-row d-flex justify-content-center">
            <div class="col-4 count-button d-flex justify-content-center align-items-center mx-2" @click="model > props.min ? model-- : ''">
                <i class="fa-solid fa-minus"></i>
            </div>
            <div class="col-4 count-button d-flex justify-content-center align-items-center mx-2" @click="model++">
                <i class="fa-solid fa-plus"></i>
            </div>
        </div>
    </div>
</template>