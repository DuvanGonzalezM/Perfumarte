import { ref, onMounted, onBeforeUnmount } from 'vue';

export function useResponsive() {
    const isMobile = ref(false);
    const isTablet = ref(false);
    const isDesktop = ref(false);

    const checkScreenSize = () => {
        isMobile.value = window.innerWidth < 576;
        isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
        isDesktop.value = window.innerWidth >= 992;
    };

    onMounted(() => {
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', checkScreenSize);
    });

    return {
        isMobile,
        isTablet,
        isDesktop,
        checkScreenSize
    };
}
