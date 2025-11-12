<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css' 

</script>

<template>
    <AppLayout>
        <slot />
        <Toaster closeButton richColors />
    </AppLayout>
</template>
