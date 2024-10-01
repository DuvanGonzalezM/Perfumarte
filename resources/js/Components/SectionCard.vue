<script setup>
const props = defineProps({
    idSection: {
        type: Number,
    },
    subtitle: {
        type: String,
    },
});
</script>

<template>
    <section class="section-prais position-relative container py-0">
        <div class="over-section position-absolute d-flex align-items-center m-auto p-3">
            <slot name="headerSection" />
            <h4 class="idSection position-absolute mx-5" v-if="idSection">
                N° {{ idSection }}
            </h4>
            <div class="subtitle position-absolute my-2" v-if="subtitle">
                {{ subtitle }}
            </div>
        </div>
        <div class="m-auto section-content-prais">
            <slot />
        </div>
    </section>
</template>