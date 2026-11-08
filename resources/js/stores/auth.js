import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        // جلب البيانات من localStorage كقيم افتراضية
        user: JSON.parse(localStorage.getItem('auth')) || null,
        permissions: JSON.parse(localStorage.getItem('permissions')) || [],
    }),
    actions: {
        // دالة لتحديث البيانات بعد تسجيل الدخول أو عند فحص الـ Token
        setAuthData(user, permissions) {
            this.user = user;
            this.permissions = permissions;
            localStorage.setItem('auth', JSON.stringify(user));
            localStorage.setItem('permissions', JSON.stringify(permissions));
        },
        logout() {
            this.user = null;
            this.permissions = [];
            localStorage.removeItem('token');
            localStorage.removeItem('auth');
            localStorage.removeItem('permissions');
        }
    }
});
