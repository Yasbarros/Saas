<script setup lang="ts">
const props = defineProps<{
  title: string | number | null | undefined;
  info: string | number | null | undefined;
}>();
</script>

<template>
  <div>
    <h5 class="font-semibold text-sm text-foreground">{{ props.title }}</h5>
    <p class="text-gray-400 text-sm">{{ props.info ?? '-' }}</p>
  </div>
</template>