<script setup>
import { ref, onMounted } from 'vue';

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
    <section class="section-prais position-relative container py-0 responsive-container" 
        :class="{'section-prais-mobile': isMobile, 'section-prais-tablet': isTablet}">
        <div class="over-section position-absolute d-flex flex-wrap align-items-center m-auto p-3"
            :class="{'over-section-mobile': isMobile, 'over-section-tablet': isTablet}">
            <slot name="headerSection" />
            <h4 class="idSection position-absolute mx-4" v-if="idSection" 
                :class="{'id-section-mobile': isMobile, 'id-section-tablet': isTablet}">
                N° {{ idSection }}
            </h4>
            <div class="subtitle position-absolute my-2" v-if="subtitle"
                :class="{'subtitle-mobile': isMobile, 'subtitle-tablet': isTablet}">
                {{ subtitle }}
            </div>
            <div class="subextra position-absolute my-2 mx-4" v-if="subextra"
                :class="{'subextra-mobile': isMobile, 'subextra-tablet': isTablet}">
                {{ subextra }}
            </div>
        </div>
        <div class="m-auto section-content-prais"
            :class="{'section-content-mobile': isMobile, 'section-content-tablet': isTablet}">
            <slot />
        </div>
    </section>
</template>