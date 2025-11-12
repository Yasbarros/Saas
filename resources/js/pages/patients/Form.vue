<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';

import PatientBasicInfo from '@/components/forms/patient/PatientBasicInfo.vue';
import PatientContact from '@/components/forms/patient/PatientContact.vue';
import PatientAddress from '@/components/forms/patient/PatientAddress.vue';
import FormActions from '@/components/FormActions.vue';
import FormBackground from '@/components/FormBackground.vue';
import FormWrapper from '@/components/FormWrapper.vue';

import { usePatientForm, type PatientFormData } from '@/composables/usePatientForm';

const props = defineProps<{
  //TODO: Remover tipagem any
  patient?: any;
  address?: any;
}>();

const isEdit = computed(() => !!props.patient);

const breadcrumbs: BreadcrumbItem[] = props.patient ? [
  { title: 'Pacientes', href: '/patients' },
  { title: props.patient.name, href: `/patients/${props.patient.id}` },
  { title: 'Editar', href: `/patients/${props.patient.id}/edit` }
] : [
  { title: 'Pacientes', href: '/patients' },
  { title: 'Cadastrar', href: '/patients/create' }
];

const initialData: Partial<PatientFormData> = {
  ...props.patient,
  ...props.address,
  photo: null,
};

const { form, validateForm, removeMasks } = usePatientForm(initialData);

function handleSubmit() {
  if (!validateForm()) return;
  
  const route_name = isEdit.value ? 'patients.update' : 'patients.store';
  const route_params = isEdit.value ? [props.patient.id] : [];
  
  form
    .transform((data: any) => ({
      ...removeMasks(data as PatientFormData),
      _method: isEdit.value ? 'PUT' : undefined,
    }))
    .post(route(route_name, ...route_params), {
      onError: (errors: Record<string, string>) => {
        Object.entries(errors).forEach(([field, message]) => {
          form.setError(field as keyof PatientFormData, message as string);
        });
      }
    });
}
</script>

<template>
  <Head title="Pacientes" />
  <AppLayout>
    <div class="p-4">
      <Heading 
        :title="isEdit ? `Editar Paciente ${props.patient.name}` : 'Cadastrar Paciente'" 
        :breadcrumbs="breadcrumbs" 
      />
      
      <FormWrapper @submit="handleSubmit">
        <FormBackground>
          <PatientBasicInfo v-model="form" :patient="props.patient" />
          <PatientContact v-model="form" />
          <PatientAddress v-model="form" />
        </FormBackground>
        
        <FormActions 
          :is-edit="isEdit"
          :processing="form.processing"
          cancel-route="patients.index"
        />
      </FormWrapper>
    </div>
  </AppLayout>
</template>