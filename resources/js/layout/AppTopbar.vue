<script setup>
import { useLayout } from "@/layout/composables/layout";
import AppConfigurator from "./AppConfigurator.vue";
import { ref } from "vue";

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

const openNotification = ref();
</script>

<template>
  <div class="layout-topbar">
    <div class="layout-topbar-logo-container">
      <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
        <i class="pi pi-bars"></i>
      </button>
      <router-link to="/" class="layout-topbar-logo">
        <img src="/public/logo.jpeg" class="w-16" alt="" />
      </router-link>
    </div>

    <div class="layout-topbar-actions">
      <div class="layout-config-menu">
        <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
          <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
        </button>
        <div class="relative">
          <i
            class="pi pi-bell layout-topbar-action"
            style="font-size: 1.5rem"
            @click.prevent="openNotification = !openNotification"
          ></i>

          <div
            v-if="$root.notifications"
            class="z-50 absolute top-15 left-0 bg-white shadow-md rounded-md w-96"
            :class="{ hidden: !openNotification }"
          >
            <div class="title bg-gray-200 border-b-2 mb-2 rounded-t-md p-2">
              الاشعارات
            </div>
            <a
              v-for="notification in $root.notifications.data"
              :key="notification.id"
              :href="notification.data.profile_photo_path"
              class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <div class="shrink-0">
                <img
                  class="rounded-full w-11 h-11 border"
                  :src="
                    'https://docank.mahmoudalbatran.com/storage/' +
                    notification.data.from.profile_photo_path
                  "
                  :alt="notification.data.from.name"
                />
              </div>
              <div v-if="notification.read_at == null" class="w-full ps-3">
                <strong class="text-black text-sm mb-1.5"
                  >{{ notification.data.title
                  }}<span class="font-semibold">{{ notification.data.from.name }}</span
                  >: "{{ notification.data.body }}"</strong
                >
                <!-- <div class="text-xs">{{new Date(notification.created_at)}}</div> -->
              </div>
              <div v-else class="w-full ps-3">
                <div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">
                  {{ notification.data.title
                  }}<span class="font-semibold text-gray-900 dark:text-white">{{
                    notification.data.from.name
                  }}</span
                  >: "{{ notification.data.body }}"
                </div>
                <!-- <div class="text-xs">{{new Date(notification.created_at)}}</div> -->
              </div>
              <!-- {{notification.created_at.diffForHumans()}} -->
            </a>
          </div>
        </div>
        <div class="relative">
          <button
            v-styleclass="{
              selector: '@next',
              enterFromClass: 'hidden',
              enterActiveClass: 'animate-scalein',
              leaveToClass: 'hidden',
              leaveActiveClass: 'animate-fadeout',
              hideOnOutsideClick: true,
            }"
            type="button"
            class="layout-topbar-action layout-topbar-action-highlight"
          >
            <i class="pi pi-palette"></i>
          </button>
          <AppConfigurator />
        </div>
      </div>

      <button
        class="layout-topbar-menu-button layout-topbar-action"
        v-styleclass="{
          selector: '@next',
          enterFromClass: 'hidden',
          enterActiveClass: 'animate-scalein',
          leaveToClass: 'hidden',
          leaveActiveClass: 'animate-fadeout',
          hideOnOutsideClick: true,
        }"
      >
        <i class="pi pi-ellipsis-v"></i>
      </button>

      <div class="layout-topbar-menu hidden lg:block">
        <div class="layout-topbar-menu-content">
          <button type="button" class="layout-topbar-action">
            <i class="pi pi-calendar"></i>
            <span>Calendar</span>
          </button>
          <button type="button" class="layout-topbar-action">
            <i class="pi pi-inbox"></i>
            <span>Messages</span>
          </button>
          <button type="button" class="layout-topbar-action relative">
            <i class="pi pi-user"></i>
            <span>Profile</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
