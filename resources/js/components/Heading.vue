<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

interface Props {
    title: string;
    breadcrumbs: BreadcrumbItemType[];
}

const props = defineProps<Props>();
</script>

<template>
    <div class="mb-8 space-y-0.5">
        <Breadcrumbs :breadcrumbs="props.breadcrumbs" />
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
    </div>
</template>
