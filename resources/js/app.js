import './bootstrap';
import '../scss/app.scss';
import * as bootstrap from 'bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import LaravelPermissionToVueJS from 'laravel-permission-to-vuejs';
import { VueReCaptcha } from 'vue-recaptcha-v3';

const appName = import.meta.env.VITE_APP_NAME || 'Perfumarte';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(LaravelPermissionToVueJS)
            .use(plugin)
            .use(ZiggyVue)
            .use(VueReCaptcha, {
                siteKey: props.initialPage.props.recaptcha_site_key,
                loaderOptions: {
                    useRecaptchaNet: true,
                    autoHideBadge: false
                }
            })
            .mount(el);
    },
    progress: {
        color: '#EC9F4F',
    },
});