<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    idSection: {
        type: Number,
    },
    subtitle: {
        type: String,
    },
    subextra: {
        type: String,
    },
});
</script>

<template>
    <section class="section-prais">
        <div class="over-section">
            <slot name="headerSection" />
            <h4 class="idSection" v-if="idSection">
                N° {{ idSection }}
            </h4>
            <div class="subtitle" v-if="subtitle">
                {{ subtitle }}
            </div>
            <div class="subextra" v-if="subextra">
                {{ subextra }}
            </div>
        </div>
        <div class="section-content-prais">
            <slot />
        </div>
    </section>
</template>