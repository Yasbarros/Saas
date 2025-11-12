<script setup lang="ts">
import { defineModel } from 'vue';
import FormSectionHeader from '@/components/FormSectionHeader.vue';
import FormField from '@/components/FormField.vue';
import FormRow from '@/components/FormRow.vue';

const form = defineModel<any>({ required: true });
</script>

<template>
  <FormSectionHeader 
    title="Endereço do paciente" 
    subtitle="Insira o endereço do paciente" 
  />
  
  <FormRow class="md:grid-cols-3">
    <FormField
      id="postal_code"
      v-model="form.postal_code"
      label="CEP"
      placeholder="Digite o CEP"
      mask="#####-###"
      :required="true"
      :error="form.errors.postal_code"
    />
    
    <FormField
      id="city"
      v-model="form.city"
      label="Cidade"
      placeholder="Digite a cidade"
      :required="true"
      :error="form.errors.city"
    />
    
    <FormField
      id="state"
      v-model="form.state"
      label="Estado"
      placeholder="Digite o estado"
      :required="true"
      :error="form.errors.state"
    />
  </FormRow>
  
  <FormRow>
    <FormField
      id="street"
      v-model="form.street"
      label="Logradouro"
      placeholder="Digite o logradouro"
      :required="true"
      :error="form.errors.street"
    />
  </FormRow>
  
  <FormRow class="md:grid-cols-5">
      <FormField
        id="neighborhood"
        class="md:col-span-2"
        v-model="form.neighborhood"
        label="Bairro"
        placeholder="Digite o bairro"
        :required="true"
        :error="form.errors.neighborhood"
      />
    
      <FormField
        id="number"
        class="md:col-span-1"
        v-model="form.number"
        label="Número"
        placeholder="Digite o nº"
        :error="form.errors.number"
      />
    
      <FormField
        id="complement"
        class="md:col-span-2"
        v-model="form.complement"
        label="Complemento"
        placeholder="Digite o complemento"
        :error="form.errors.complement"
      />
  </FormRow>
</template>