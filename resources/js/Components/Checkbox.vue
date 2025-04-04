<script setup>
import { computed, ref, onMounted } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
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

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});

const classes = computed(() => {
    let baseClass = 'rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 checkbox-prais';
    
    if (isMobile.value) {
        baseClass += ' checkbox-mobile';
    } else if (isTablet.value) {
        baseClass += ' checkbox-tablet';
    }
    
    return baseClass;
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        :class="classes"
    />
</template>
