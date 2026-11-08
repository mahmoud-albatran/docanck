import { createApp } from 'vue';
import './bootstrap';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import { useAuthStore } from './stores/auth';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

import '@/assets/tailwind.css';
import '@/assets/styles.scss';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia); // يجب تفعيل pinia قبل استخدامه في الـ global logic

// --- إضافة منطق الصلاحيات ---
const authStore = useAuthStore();

app.config.globalProperties.$can = (permission) => {
    // إذا كان المستخدم super-admin (اختياري)
    if (authStore.permissions.includes('super-admin')) return true;

    return authStore.permissions.includes(permission);
};
// --------------------------

app.use(router)
    .use(pinia)
    .use(ElementPlus)
    .use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '.app-dark'
            }
        }
    })
    .use(ToastService)
    .use(ConfirmationService)
    .mount('#app')

