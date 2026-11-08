<script setup>
import BestSellingWidget from '@/components/dashboard/BestSellingWidget.vue';
import NotificationsWidget from '@/components/dashboard/NotificationsWidget.vue';
import RecentSalesWidget from '@/components/dashboard/RecentSalesWidget.vue';
import RevenueStreamWidget from '@/components/dashboard/RevenueStreamWidget.vue';
import StatsWidget from '@/components/dashboard/StatsWidget.vue';
import AppLayout from '@/layout/AppLayout.vue';
import { ElButton } from 'element-plus'

</script>

<template>
    <AppLayout>

        <div class="grid grid-cols-12 gap-8">
          <!-- <el-button>I am ElButton</el-button> -->

            <StatsWidget />

            <div class="col-span-12 xl:col-span-6">
                <RecentSalesWidget />
                <BestSellingWidget />
            </div>
            <div class="col-span-12 xl:col-span-6">
                <RevenueStreamWidget />
                <NotificationsWidget />
            </div>
        </div>
    </AppLayout>
</template>
