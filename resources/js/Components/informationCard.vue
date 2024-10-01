<script setup>

const props = defineProps({
    title: {
        type: String,
    },
    number: {
        type: String,
        required: true,
    },
    iconClass: {
        type: String,
        required: true,
    },
});

</script>


<template>
    <div class="card mb-3 d-flex text-end card-prais">
        <div class="card-body">
            <div class="over-card">
                <i :class="iconClass"></i>
            </div>
        </div>
        <div class="text-card"> 
           {{ title}}
            <div>{{number}}</div>
        </div>
    </div>
</template>