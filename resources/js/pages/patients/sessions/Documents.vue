<script setup lang="ts">
import { ref } from 'vue'
import PatientsLayout from '@/layouts/PatientsLayout.vue'

defineOptions({
  layout: PatientsLayout,
})
const search = ref('')
</script>

<template>
  <div class="flex justify-between items-center w-full mb-4">
    <div>campo</div>


    <button
      class="bg-black text-white text-sm px-4 py-1.5 rounded-md hover:bg-gray-800"
    >
      Novo Documento
    </button>
  </div>
</template>
