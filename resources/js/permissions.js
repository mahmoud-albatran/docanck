import { ref, onMounted } from 'vue';
import axios from 'axios';
import http from './helpers/http';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const userRoles = ref([]);
const userPermissions = ref([]);

onMounted(async () => {
    const res = await http().get('/api/users/permissions');
    userRoles.value = res.data.roles;
    userPermissions.value = res.data.permissions;
});

// دوال مساعدة
const can = (permission) => userPermissions.value.includes(permission);
const hasRole = (role) => userRoles.value.includes(role);

export { userRoles, userPermissions, can, hasRole };
