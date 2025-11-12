<script setup lang="ts">
import { defineEmits, useAttrs } from 'vue';

const emit = defineEmits<{ (e: 'submit'): void }>();
const attrs = useAttrs();

function onSubmit() {
  emit('submit');
}
</script>

<template>
  <form class="lg:max-w-[800px]" v-bind="attrs" @submit.prevent="onSubmit">
    <slot />
  </form>
  
</template>

<style scoped>
</style>


