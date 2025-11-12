<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
  isEdit: boolean;
  processing: boolean;
  cancelRoute: string;
  cancelText?: string;
  submitText?: string;
  editText?: string;
  processingText?: string;
  processingEditText?: string;
}>();

const buttonTexts = {
  cancel: props.cancelText || 'Cancelar',
  submit: props.submitText || 'Cadastrar',
  edit: props.editText || 'Atualizar',
  processing: props.processingText || 'Cadastrando...',
  processingEdit: props.processingEditText || 'Atualizando...'
};
</script>

<template>
  <div class="flex justify-end gap-2 pt-4">
    <Button as-child type="button" variant="outline">
      <Link :href="route(cancelRoute)">{{ buttonTexts.cancel }}</Link>
    </Button>
    
    <Button 
      type="submit" 
      variant="default" 
      :disabled="processing"
    >
      <span v-if="processing">
        {{ isEdit ? buttonTexts.processingEdit : buttonTexts.processing }}
      </span>
      <span v-else>
        {{ isEdit ? buttonTexts.edit : buttonTexts.submit }}
      </span>
    </Button>
  </div>
</template>