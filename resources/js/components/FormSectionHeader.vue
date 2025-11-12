<template>
  <div>
    <Title>{{ title }}</Title>
    <p v-if="subtitle" class="text-sm text-gray-400">{{ subtitle }}</p>
  </div>
  
  <slot />
</template>

<script setup lang="ts">
import Title from '@/components/Title.vue';

defineProps<{
  title: string;
  subtitle?: string;
}>();
</script>


