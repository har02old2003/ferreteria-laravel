import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';

const appName = 'FERRECHINCHA';

createInertiaApp({
    title: (title) => title ? `${title} | ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Configuración global de rutas
        app.config.globalProperties.$route = (name) => {
            const baseUrl = window.location.origin;
            switch (name) {
                case 'login':
                    return `${baseUrl}/login`;
                case 'register':
                    return `${baseUrl}/register`;
                case 'dashboard':
                    return `${baseUrl}/dashboard`;
                case 'admin.categories.index':
                    return `${baseUrl}/admin/categories`;
                case 'admin.products.index':
                    return `${baseUrl}/admin/products`;
                case 'admin.users.index':
                    return `${baseUrl}/admin/users`;
                case 'profile.edit':
                    return `${baseUrl}/profile`;
                default:
                    console.warn(`Route "${name}" not found`);
                    return '#';
            }
        };

        app.use(plugin);
        app.use(ZiggyVue);
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
