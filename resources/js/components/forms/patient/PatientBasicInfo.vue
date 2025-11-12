<script setup lang="ts">
import { computed, defineModel } from 'vue';
import FormSectionHeader from '@/components/FormSectionHeader.vue';
import FormField from '@/components/FormField.vue';
import PhotoUpload from '@/components/PhotoUpload.vue';
import FormRow from '@/components/FormRow.vue';

defineProps<{
  patient?: any;
}>();

const form = defineModel<any>({ required: true });

const sexOptions = [
  { value: 'M', label: 'Masculino' },
  { value: 'F', label: 'Feminino' },
  { value: 'O', label: 'Outro' },
  { value: 'N', label: 'Não Informado' }
];

const age = computed(() => {
  if (!form.value.date_of_birth) return '';
  
  const today = new Date();
  const birthDate = new Date(form.value.date_of_birth);
  let age = today.getFullYear() - birthDate.getFullYear();
  const month = today.getMonth() - birthDate.getMonth();
  
  if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  
  form.value.legal_guardian = age < 18 ? form.value.legal_guardian : '';
  form.value.cpf_legal_guardian = age < 18 ? form.value.cpf_legal_guardian : '';
  
  return age;
});

const isAdult = computed(() => typeof age.value === 'number' && age.value >= 18);
</script>

<template>
  <FormSectionHeader 
    title="Informações do paciente" 
    subtitle="Insira os dados do paciente" 
  />
  
  <FormRow class="md:grid-cols-2">
    <PhotoUpload 
      v-model="form.photo" 
      :existing-photo="patient?.photo" 
    />
    
    <FormRow>
      <FormField
        id="name"
        v-model="form.name"
        label="Nome Completo"
        placeholder="Digite o nome"
        :required="true"
        :error="form.errors.name"
      />
      
      <FormField
        id="socialName"
        v-model="form.social_name"
        label="Nome Social"
        placeholder="Digite o nome social"
        :error="form.errors.social_name"
      />
    </FormRow>
  </FormRow>
  
  <FormRow class="md:grid-cols-5">
      <FormField
        id="sex"
        class="md:col-span-2"
        v-model="form.sex"
        label="Sexo"
        type="select"
        :select-options="sexOptions"
        placeholder="Selecione o sexo"
        :required="true"
        :error="form.errors.sex"
      />
    
      <FormField
        id="date_of_birth"
        class="md:col-span-2"
        v-model="form.date_of_birth"
        label="Data de Nascimento"
        type="date"
        :required="true"
        :error="form.errors.date_of_birth"
      />
    
      <FormField
        id="age"
        class="md:col-span-1"
        :model-value="age"
        label="Idade"
        :disabled="true"
      />
  </FormRow>
  
  <FormRow v-if="!isAdult" class="md:grid-cols-2">
    <FormField
      id="legal_guardian"
      v-model="form.legal_guardian"
      label="Nome do responsável"
      placeholder="Digite o Nome do responsável"
      :required="true"
      :error="form.errors.legal_guardian"
    />
    
    <FormField
      id="cpf_legal_guardian"
      v-model="form.cpf_legal_guardian"
      label="CPF do responsável"
      placeholder="Digite o CPF do responsável"
      mask="###.###.###-##"
      :required="true"
      :error="form.errors.cpf_legal_guardian"
    />
  </FormRow>
  
  <FormRow class="md:grid-cols-2">
    <FormField
      id="cpf"
      v-model="form.cpf"
      label="CPF"
      placeholder="Digite o CPF"
      mask="###.###.###-##"
      :required="isAdult"
      :error="form.errors.cpf"
    />
    
    <FormField
      id="rg"
      v-model="form.rg"
      label="RG"
      placeholder="Digite o RG"
      :error="form.errors.rg"
    />
  </FormRow>
  
  <FormRow class="md:grid-cols-2">
    <FormField
      id="notes"
      v-model="form.notes"
      label="Observações"
      type="textarea"
      placeholder="Digite a observação"
      :error="form.errors.notes"
    />
  </FormRow>
</template>