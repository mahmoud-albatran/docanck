import {  createRouter, createWebHistory } from "vue-router";

import Login from "./pages/login.vue";
import Dashboard from "./pages/dashboard.vue";
import Category from "./pages/Category.vue";
import Products from "./pages/Products.vue";
import clients from "./pages/clients.vue";
import Sliders from "./pages/Sliders.vue";
import SubCategories from "./pages/SubCategories.vue";
import Orders from "./pages/Orders.vue";
import Stores from "./pages/Stores.vue";
import Roles from "./pages/Roles.vue";
import Permissions from "./pages/Permissions.vue";
import RolePermissions from "./pages/RolePermissions.vue";
import Employees from "./pages/Employees.vue";
import axios from "axios";
import DiscountCodes from "./pages/DiscountCodes.vue";
import http from "./helpers/http";
import { useAuthStore } from '@/stores/auth';



const routes = [
    // {
    //     path : '/',
    //     component : AppLayout,
    //     children: [

    //         {
    //             path : '/',
    //             name : 'login',
    //             component : Login
    //         },
    //         {
    //             path : '/dashboard',
    //             name : 'dashboard',
    //             component : Dashboard
    //         }
    //     ]
    // },
     {
        path : '/dashboard',
        name : 'dashboard',
        component : Dashboard
    },
    {
        path : '/',
        name : 'login',
        component : Login
    },
    {
        path : '/categories',
        name : 'Category',
        component : Category
    },
    {
        path : '/sub-categories',
        name : 'SubCategory',
        component : SubCategories
    },
    {
        path : '/products',
        name : 'Product',
        component : Products
    },
    {
        path : '/orders',
        name : 'Order',
        component : Orders
    },
    {
        path : '/clients',
        name : 'client',
        component : clients
    },
    {
        path : '/employees',
        name : 'Employee',
        component : Employees
    },
    {
        path : '/stores',
        name : 'store',
        component : Stores
    },
    {
        path : '/sliders',
        name : 'Slider',
        component : Sliders
    },
    {
        path : '/discountcodes',
        name : 'DiscountCode',
        component : DiscountCodes
    },
    {
        path : '/roles',
        name : 'Role',
        component : Roles,
        // meta: { permission: 'view roles' } // أضف هذا السطر
    },
    {
        path : '/permissions',
        name : 'Permission',
        component : Permissions
    },
    {
        path: '/role/:id',
        name: 'rolePermission',
        component: RolePermissions
    }



];



const router = createRouter({
    history: createWebHistory(),
    routes
});



router.beforeEach((to, from, next) => {
  if (to.name === 'login') {
    return next();
  }

 const token = localStorage.getItem('token');
  if (!token) {
    return next({ name: 'login' });
  }

  // فحص صلاحية الرابط
  const userPermissions = JSON.parse(localStorage.getItem('permissions') || '[]');
  if (to.meta.permission && !userPermissions.includes(to.meta.permission)) {
      return next({ name: 'dashboard' }); // أو صفحة 403
  }



//   checkRolesAuthenticity()
  checkTokenAuthenticity()
  next(); // تابع التنفيذ
})

const checkTokenAuthenticity = () => {
    const authStore = useAuthStore(); // الوصول للستور
    //   axios.get('/api/user', {
    http().get('/api/auth-permission', {
        // headers: {
        // Authorization: `Bearer ${localStorage.getItem('token')}`
        // }
    })
    .then((response) => {
      console.log(response.data)
      localStorage.setItem('auth', JSON.stringify(response.data.auth))
      // localStorage.setItem('phone', response.data.phone)
      // تخزين الصلاحيات كمصفوفة
      localStorage.setItem('permissions', JSON.stringify(response.data.permissions));
      // تحديث الـ Store والـ LocalStorage في خطوة واحدة
        authStore.setAuthData(response.data.auth, response.data.permissions);
    })
    .catch((error) => {
        if (error.response && (error.response.status === 401)) {
            authStore.logout(); // تنظيف البيانات
            router.push({ name: 'login' });
        }

    })
}

export default router;
