// Mixin para detección de dispositivos responsivos
export default {
    data() {
        return {
            isMobile: false,
            isTablet: false
        };
    },
    mounted() {
        this.checkScreenSize();
        window.addEventListener('resize', this.checkScreenSize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.checkScreenSize);
    },
    methods: {
        checkScreenSize() {
            this.isMobile = window.innerWidth < 576;
            this.isTablet = window.innerWidth >= 576 && window.innerWidth < 992;
        }
    }
};
