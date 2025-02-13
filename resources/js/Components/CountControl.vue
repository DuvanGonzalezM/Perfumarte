<script setup>

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

</script>

<template>
    <div class="row text-center justify-content-center">
        <div class="col-md-12">
            <h4 class="d-flex justify-content-center">{{ model }}</h4>
            {{props.title}}
        </div>
        <div class="col-md-5 addItem px-3 py-2 d-flex justify-content-center" @click="model > props.min ? model-- : ''">
            <i class="fa-solid fa-minus"></i>
        </div>
        <div class="col-md-2 px-3 py-2 d-flex justify-content-center"></div>
        <div class="col-md-5 addItem px-3 py-2 d-flex justify-content-center" @click="model++">
            <i class="fa-solid fa-plus"></i>
        </div>
    </div>
</template>