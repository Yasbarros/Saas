<script setup lang="ts">
import { ref, defineModel, watch } from 'vue';
import { UserRound } from 'lucide-vue-next';

const props = defineProps<{
  existingPhoto?: string | null;
}>();

const file = defineModel<File | null>({ required: true });

const fileInput = ref<HTMLInputElement | null>(null);
const hover = ref(false);
const previewUrl = ref<string | null>(props.existingPhoto || '');

const allowedFiles = ['jpg', 'png', 'bmp', 'jpeg', 'gif'];

const openFileDialog = () => {
  fileInput.value?.click();
};

const handleFileChange = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const selectedFile = target.files?.[0];
  
  previewUrl.value = selectedFile ? URL.createObjectURL(selectedFile) : '';
  
  if (!selectedFile) {
    file.value = null;
    return;
  }
  
  const fileName = selectedFile.name || '';
  const arr = fileName.split('.');
  const ext = arr[arr.length - 1].toLowerCase();
  const typeIsAllowed = allowedFiles.includes(ext);
  const sizeIsAllowed = selectedFile.size <= 4000000;
  
  if (!typeIsAllowed && !sizeIsAllowed) {
    console.warn(`Tipo e tamanho inválidos: ${fileName}`);
    return;
  } else if (!typeIsAllowed) {
    console.warn(`Tipo de arquivo inválido: ${fileName}`);
    return;
  } else if (!sizeIsAllowed) {
    console.warn(`Arquivo muito grande: ${fileName}`);
    return;
  }
  
  file.value = selectedFile;
};
    
watch(() => file.value, (newValue) => {
  if (!newValue && previewUrl.value && !props.existingPhoto) {
    previewUrl.value = '';
  }
});
</script>

<template>
  <div 
    class="relative w-[150px] h-[150px] cursor-pointer" 
    @click="openFileDialog" 
    @mouseover="hover = true" 
    @mouseleave="hover = false"
  >
    <img 
      v-if="previewUrl" 
      :src="previewUrl" 
      alt="Avatar" 
      class="w-full h-full object-cover rounded-full"
    />
    
    <div v-else class="w-full h-full object-cover rounded-full relative">
      <div class="absolute top-0 left-0 w-full h-full rounded-full bg-gray-200 flex items-center justify-center">
        <UserRound class="absolute top-12 right-14 w-10 h-10 text-black" />
      </div>
    </div>
    
    <div 
      v-if="hover" 
      class="absolute top-0 left-0 w-full h-full rounded-full bg-black/75 text-white flex items-center justify-center font-bold text-sm text-center px-2"
    >
      Selecionar Foto
    </div>
    
    <input 
      type="file" 
      ref="fileInput" 
      class="hidden" 
      accept="image/*"
      @change="handleFileChange"
    />
  </div>
</template>