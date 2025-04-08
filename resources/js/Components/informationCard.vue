<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    number: {
        type: Number,
        required: true,
    },
    iconClass: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="card-prais">
        <div class="card-body">
            <div class="over-card">
                <div class="number mx-4">
                    {{ number }}
                </div>
                <i :class="iconClass"></i>
            </div>
        </div>
        <div class="text-card">
            {{ title }}
        </div>
    </div>
</template>