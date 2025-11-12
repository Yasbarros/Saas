<script setup lang="ts">
// lógica da página aqui (opcional)
import { defineOptions } from 'vue';
import PatientsLayout from '@/layouts/PatientsLayout.vue';
defineOptions({
  layout: PatientsLayout,
});
</script>

<template>
  <div>
    <h1 class="text-xl font-bold">Tratamentos</h1>
    <p>Aqui vai o conteúdo da tabela: PROCEDIMENTO, DESCRIÇÃO, PROFISSIONAL, STATUS</p>
  </div>
</template>
