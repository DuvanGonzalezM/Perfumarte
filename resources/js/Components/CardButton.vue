<script setup>
const props = defineProps({
    title: {
        type: String,
    },
    icon: {
        type: String,
    },
    link: {
        type: String,
    }
});

</script>
<template>
    <div>
        <a class="card mb-3 d-flex text-center button-stock mx-auto" :href="link">
            <div class="card-body">
                <div class="card card-icon text-center justify-content-center">
                    <i class="fa-solid " :class="icon"></i>
                </div>
                <div class="card-text button-text">{{ props.title }}</div>
            </div>
        </a>
    </div>
</template>