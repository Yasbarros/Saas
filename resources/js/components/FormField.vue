<script setup lang="ts">
import { computed, defineModel } from 'vue'
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import DataSelect from '@/components/ui/select/DataSelect.vue';
import InputError from '@/components/InputError.vue';
import { vMaska } from 'maska/vue';
import { cn } from '@/lib/utils';

interface FormFieldProps {
  id: string
  label: string
  type?: 'text' | 'email' | 'tel' | 'date' | 'textarea' | 'select'
  placeholder?: string
  required?: boolean
  disabled?: boolean
  mask?: string
  selectOptions?: Array<{ value: string | number; label: string }>
  error?: string
  class?: string
}

const props = withDefaults(defineProps<FormFieldProps>(), {
  type: 'text',
  required: false,
  disabled: false,
})

const modelValue = defineModel<string | number>({ required: true })

const inputClass = computed(() => props.class || '')
const isRequired = computed(() => props.required)
</script>

<template>
  <div :class="cn('grid gap-1', props.class)">
    <Label :for="id">
      {{ label }}
      <span v-if="isRequired" class="text-red-500">*</span>
    </Label>

    <!-- Input padrão -->
    <Input
      v-if="type !== 'textarea' && type !== 'select'"
      :id="id"
      v-model="modelValue"
      :type="type"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :class="inputClass"
      v-maska="props.mask ? props.mask : null"
    />

    <!-- Textarea -->
    <Textarea
      v-else-if="type === 'textarea'"
      :id="id"
      v-model="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :class="inputClass"
    />

    <!-- Select -->
    <DataSelect
      v-else-if="type === 'select'"
      :id="id"
      v-model="modelValue"
      :items="selectOptions || []"
      :placeholder="placeholder"
      :disabled="disabled"
    />

    <InputError :message="error" />
  </div>
</template>
