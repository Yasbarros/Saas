<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';

interface Props {
  links: { title: string; href: string }[];
}

defineProps<Props>();

const page = usePage();

const isActive = (href: string) => {
  const currentPath = page.url.split('?')[0];

  const hasDynamicId = /\/\d+($|\/)/.test(href);

  if (hasDynamicId) {
    return currentPath === href;
  }

  return currentPath === href || currentPath.startsWith(href + '/');
};
</script>
<template>
    <div class="space-x-4 border-b border-bg-border flex items-center"> 
        <Link v-for="(link, index) in links" :key="index" :href="link.href" :class="{'border-b border-black dark:border-white mb-[-1px] !text-foreground': isActive(link.href)}" class="text-sm text-muted-foreground pb-4">
            {{ link.title }}
        </Link>
    </div>
</template>