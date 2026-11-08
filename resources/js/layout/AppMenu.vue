<script setup>
import { ref } from "vue";

import AppMenuItem from "./AppMenuItem.vue";

const model = ref([
  {
    label: "الرئيسية",
    items: [{ label: "لوحة التحكم", icon: "pi pi-fw pi-home", to: "/dashboard" }],
  },
  {
    label: "الصفحات",
    items: [
      //   { label: "التصنيفات", icon: "pi pi-fw pi-id-card", to: "/categories" },
      { label: "التصنيفات الاساسية", icon: "pi pi-fw pi-calendar", to: "/categories" },
      { label: "التصنيفات الفرعية", icon: "pi pi-fw pi-calendar", to: "/sub-categories" },
      { label: "المنتجات", icon: "pi pi-fw pi-calendar", to: "/products" },
      { label: "العملاء", icon: "pi pi-fw pi-calendar", to: "/clients" },
      { label: "المتاجر", icon: "pi pi-fw pi-calendar", to: "/stores" },
      { label: "الموظفين", icon: "pi pi-fw pi-calendar", to: "/employees" },
      { label: "الاعلانات", icon: "pi pi-fw pi-calendar", to: "/sliders" },
      { label: "الطلبات", icon: "pi pi-fw pi-calendar", to: "/orders" },
      { label: "الادوار", icon: "pi pi-fw pi-calendar", to: "/roles" },
      { label: "الصلاحيات", icon: "pi pi-fw pi-calendar", to: "/permissions" },
      { label: "اكواد الخصم", icon: "pi pi-fw pi-calendar", to: "/discountcodes" },
      //   { label: "المهارات", icon: "pi pi-fw pi-calendar", to: "/skills" },
      //   { label: "المشاريع", icon: "pi pi-fw pi-calendar", to: "/projects" },
      //   { label: "الدورات", icon: "pi pi-fw pi-calendar", to: "/courses" },

      //   { label: "Input", icon: "pi pi-fw pi-check-square", to: "/uikit/input" },
      //   {
      //     label: "Button",
      //     icon: "pi pi-fw pi-mobile",
      //     to: "/uikit/button",
      //     class: "rotated-icon",
      //   },
      //   { label: "Table", icon: "pi pi-fw pi-table", to: "/uikit/table" },
      //   { label: "List", icon: "pi pi-fw pi-list", to: "/uikit/list" },
      //   { label: "Tree", icon: "pi pi-fw pi-share-alt", to: "/uikit/tree" },
      //   { label: "Panel", icon: "pi pi-fw pi-tablet", to: "/uikit/panel" },
      //   { label: "Overlay", icon: "pi pi-fw pi-clone", to: "/uikit/overlay" },
      //   { label: "Media", icon: "pi pi-fw pi-image", to: "/uikit/media" },
      //   { label: "Menu", icon: "pi pi-fw pi-bars", to: "/uikit/menu" },
      //   { label: "Message", icon: "pi pi-fw pi-comment", to: "/uikit/message" },
      //   { label: "File", icon: "pi pi-fw pi-file", to: "/uikit/file" },
      //   { label: "Chart", icon: "pi pi-fw pi-chart-bar", to: "/uikit/charts" },
      //   { label: "Misc", icon: "pi pi-fw pi-circle", to: "/uikit/misc" },
    ],
  },
  //  {
  //     label: "Prime Blocks",
  //     icon: "pi pi-fw pi-prime",
  //     items: [
  //       {
  //         label: "Free Blocks",
  //         icon: "pi pi-fw pi-eye",
  //         to: "/blocks",
  //       },
  //       {
  //         label: "All Blocks",
  //         icon: "pi pi-fw pi-globe",
  //         url: "https://blocks.primevue.org/",
  //         target: "_blank",
  //       },
  //     ],
  //   },
  //   {
  //     label: "Pages",
  //     icon: "pi pi-fw pi-briefcase",
  //     to: "/pages",
  //     items: [
  //       {
  //         label: "Landing",
  //         icon: "pi pi-fw pi-globe",
  //         to: "/landing",
  //       },
  //       {
  //         label: "Auth",
  //         icon: "pi pi-fw pi-user",
  //         items: [
  //           {
  //             label: "Login",
  //             icon: "pi pi-fw pi-sign-in",
  //             to: "/auth/login",
  //           },
  //           {
  //             label: "Error",
  //             icon: "pi pi-fw pi-times-circle",
  //             to: "/auth/error",
  //           },
  //           {
  //             label: "Access Denied",
  //             icon: "pi pi-fw pi-lock",
  //             to: "/auth/access",
  //           },
  //         ],
  //       },
  //       {
  //         label: "Crud",
  //         icon: "pi pi-fw pi-pencil",
  //         to: "/pages/crud",
  //       },
  //       {
  //         label: "Not Found",
  //         icon: "pi pi-fw pi-exclamation-circle",
  //         to: "/pages/notfound",
  //       },
  //       {
  //         label: "Empty",
  //         icon: "pi pi-fw pi-circle-off",
  //         to: "/pages/empty",
  //       },
  //     ],
  //   },
  //   {
  //     label: "Hierarchy",
  //     items: [
  //       {
  //         label: "Submenu 1",
  //         icon: "pi pi-fw pi-bookmark",
  //         items: [
  //           {
  //             label: "Submenu 1.1",
  //             icon: "pi pi-fw pi-bookmark",
  //             items: [
  //               { label: "Submenu 1.1.1", icon: "pi pi-fw pi-bookmark" },
  //               { label: "Submenu 1.1.2", icon: "pi pi-fw pi-bookmark" },
  //               { label: "Submenu 1.1.3", icon: "pi pi-fw pi-bookmark" },
  //             ],
  //           },
  //           {
  //             label: "Submenu 1.2",
  //             icon: "pi pi-fw pi-bookmark",
  //             items: [{ label: "Submenu 1.2.1", icon: "pi pi-fw pi-bookmark" }],
  //           },
  //         ],
  //       },
  //       {
  //         label: "Submenu 2",
  //         icon: "pi pi-fw pi-bookmark",
  //         items: [
  //           {
  //             label: "Submenu 2.1",
  //             icon: "pi pi-fw pi-bookmark",
  //             items: [
  //               { label: "Submenu 2.1.1", icon: "pi pi-fw pi-bookmark" },
  //               { label: "Submenu 2.1.2", icon: "pi pi-fw pi-bookmark" },
  //             ],
  //           },
  //           {
  //             label: "Submenu 2.2",
  //             icon: "pi pi-fw pi-bookmark",
  //             items: [{ label: "Submenu 2.2.1", icon: "pi pi-fw pi-bookmark" }],
  //           },
  //         ],
  //       },
  //     ],
  //   },
  //   {
  //     label: "Get Started",
  //     items: [
  //       {
  //         label: "Documentation",
  //         icon: "pi pi-fw pi-book",
  //         to: "/documentation",
  //       },
  //       {
  //         label: "View Source",
  //         icon: "pi pi-fw pi-github",
  //         url: "https://github.com/primefaces/sakai-vue",
  //         target: "_blank",
  //       },
  //     ],
  //   },
]);
</script>

<template>
  <ul class="layout-menu">
    <template v-for="(item, i) in model" :key="item">
      <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
      <li v-if="item.separator" class="menu-separator"></li>
    </template>
  </ul>
</template>

<style lang="scss" scoped></style>
