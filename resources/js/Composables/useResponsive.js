import { ref, onMounted, onBeforeUnmount } from 'vue';

export function useResponsive() {
    const isMobile = ref(false);
    const isTablet = ref(false);
    const isDesktopMini = ref(false);
    const isDesktop = ref(false);

    const checkScreenSize = () => {
        isMobile.value = window.innerWidth < 576;
        isTablet.value = window.innerWidth >= 576 && window.innerWidth < 768;
        isDesktopMini.value = window.innerWidth >= 768 && window.innerWidth < 1200;
        isDesktop.value = window.innerWidth >= 1200;
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
        isDesktopMini,
        isDesktop,
        checkScreenSize
    };
}
