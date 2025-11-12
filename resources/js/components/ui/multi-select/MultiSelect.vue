<script setup lang="ts">
import { computed } from 'vue';
import VueSelect from 'vue-select';

const props = defineProps<{
  modelValue?: number[]; // Só os IDs (ex: [1, 3, 5])
  options: { label: string; value: number }[]; // ID = value, Nome = label
  placeholder?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', payload: number[]): void;
}>();

const selectedObjects = computed({
  get: () => props.options.filter(o => props.modelValue?.includes(o.value)),
  set: (val) => emit('update:modelValue', val.map(v => v.value)),
});
</script>

<template>
  <VueSelect
    v-model="selectedObjects"
    :options="options"
    :multiple="true"
    :placeholder="placeholder || 'Selecione...'"
    label="label"
    track-by="value"
    class="bg-white"
  />
</template>

<style>
@import "vue-select/dist/vue-select.css";
</style>
