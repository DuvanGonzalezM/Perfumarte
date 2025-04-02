<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white dark:bg-gray-700',
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

const isMobile = ref(false);

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    window.removeEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}

const widthClass = computed(() => {
    return {
        48: isMobile.value ? 'w-auto' : 'w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else {
        return 'origin-top';
    }
});

const open = ref(false);
</script>

<template>
    <div class="relative dropdown-container">
        <div @click="open = !open" class="dropdown-trigger">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40 dropdown-overlay" @click="open = false"></div>

        <Transition
            enter-active-class="transition ease-out duration-200 dropdown-enter-active"
            enter-from-class="opacity-0 scale-95 dropdown-enter-from"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75 dropdown-leave-active"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95 dropdown-leave-to"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-md shadow-lg dropdown-content"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>
