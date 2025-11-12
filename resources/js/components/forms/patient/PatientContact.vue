<script setup lang="ts">
import { defineModel } from 'vue';
import FormSectionHeader from '@/components/FormSectionHeader.vue';
import FormField from '@/components/FormField.vue';
import FormRow from '@/components/FormRow.vue';

const form = defineModel<any>({ required: true });
</script>

<template>
  <FormSectionHeader 
    title="Contato do paciente" 
    subtitle="Insira o contato do paciente" 
  />
  
  <FormRow class="md:grid-cols-2">
    <FormField
      id="phone"
      v-model="form.phone"
      label="Celular"
      type="tel"
      placeholder="Digite o número"
      mask="(##) #####-####"
      :required="true"
      :error="form.errors.phone"
    />
    
    <FormField
      id="landline"
      v-model="form.landline"
      label="Telefone"
      type="tel"
      placeholder="Digite o número"
      mask="(##) #####-####"
      :error="form.errors.landline"
    />
  </FormRow>
  
  <FormRow>
    <FormField
      id="email"
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="Digite o email"
      :error="form.errors.email"
    />
  </FormRow>
</template>