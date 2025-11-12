<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Title from '@/components/Title.vue';
import ViewBackground from '@/components/ViewBackground.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { ref } from 'vue';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { z } from 'zod';
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';
import { validateField, validateForm } from '@/composables/useZodFormValidator';
import { usePermissionTranslations } from '@/composables/usePermissionTranslations';

interface Permission {
  id: number;
  name: string;
}

interface ModulePermission {
  id: number;
  name: string;
}

interface Module {
  key: string;
  name: string;
  permissions: ModulePermission[];
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[] | null;
}

const props = defineProps<{
  role?: Role;
  modules: Module[];
}>();

const { getPermissionDisplayName, getModuleDisplayName } = usePermissionTranslations();

const breadcrumbs: BreadcrumbItem[] = props.role ? [
  { title: 'Usuários', href: '/users' },
  { title: 'Funções', href: '/roles' },
  { title: props.role.name, href: `/roles/${props.role.id}` },
  { title: 'Editar', href: `/roles/${props.role.id}/edit` }
] : [
  { title: 'Usuários', href: '/users' },
  { title: 'Funções', href: '/roles' },
  { title: 'Cadastrar', href: '/roles/create' }
]

const expandedModules = ref<string[]>([]);

function toggleModule(moduleKey: string) {
  if (expandedModules.value.includes(moduleKey)) {
    expandedModules.value = expandedModules.value.filter(key => key !== moduleKey);
  } else {
    expandedModules.value.push(moduleKey);
  }
}

function togglePermission(permissionId: number) {
  const index = form.permissions.indexOf(permissionId);
  if (index === -1) {
    form.permissions.push(permissionId);
  } else {
    form.permissions.splice(index, 1);
  }
}

function hasPermission(permissionId: number): boolean {
  return form.permissions.includes(permissionId);
}

function toggleAllModulePermissions(module: Module) {
  const modulePermissionIds = module.permissions.map(p => p.id);
  const allSelected = modulePermissionIds.every(id => form.permissions.includes(id));
  
  if (!expandedModules.value.includes(module.key)) {
    expandedModules.value.push(module.key);
  }
  
  if (allSelected) {
    form.permissions = form.permissions.filter(id => !modulePermissionIds.includes(id));
  } else {
    modulePermissionIds.forEach(id => {
      if (!form.permissions.includes(id)) {
        form.permissions.push(id);
      }
    });
  }
}

function isModuleFullySelected(module: Module): boolean {
  const modulePermissionIds = module.permissions.map(p => p.id);
  return modulePermissionIds.length > 0 && modulePermissionIds.every(id => form.permissions.includes(id));
}

function isModulePartiallySelected(module: Module): boolean {
  const modulePermissionIds = module.permissions.map(p => p.id);
  return modulePermissionIds.some(id => form.permissions.includes(id)) && 
         !modulePermissionIds.every(id => form.permissions.includes(id));
}

const form = useForm({
  name: props.role?.name || '',
  permissions: props.role?.permissions?.map(permission => permission.id) || [],
});

const roleSchema = z.object({
  name: z.string().min(1, 'O nome é obrigatório'),
  permissions: z.array(z.number()),
});

//TODO: Tornar reutilizável
function handleFieldBlur(field: string) {
  validateField(form, roleSchema, field);
}

function submit() {
  if (!validateForm(roleSchema, form)) return;

  if (props.role) {
    form.put(route('roles.update', props.role.id));
    return;
  }

  form.post(route('roles.store'));
}

useFlashToasts();
</script>

<template>
  <Head title="Cadastrar Função" />

  <AppLayout>
    <div class="flex h-full flex-col rounded-xl p-4">
      <Heading
        :title="props.role ? `Editar Função ${props.role.name}` : 'Cadastrar Função'"
        :breadcrumbs="breadcrumbs"
      />

      <form @submit.prevent="submit" class="lg:max-w-2/4">
        <ViewBackground class="space-y-8">
          <div class="space-y-4">
            <div>
              <Title>Informações da função</Title>
              <p class="text-sm text-gray-400">Insira os dados da função</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label :required="true" for="name">Nome</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Digite o nome"
                  required
                  @blur="handleFieldBlur('name')"
                />
                <InputError :message="form.errors.name"/>
              </div>
            </div>
          </div>

          <Separator />

          <div class="space-y-4">
            <div>
              <Title>Permissões</Title>
              <p class="text-sm text-gray-400">Escolha as permissões da função</p>
            </div>

            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Módulo</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <template v-if="modules.length">
                  <template v-for="module in modules" :key="module.key">
                    <TableRow class="border-b">
                      <TableCell
                        class="p-4 cursor-pointer"
                        @click="toggleModule(module.key)"
                      >
                        <div class="flex items-center justify-between">
                          <div class="flex items-center gap-3">
                            <Checkbox
                              :modelValue="isModuleFullySelected(module)"
                              :indeterminate="isModulePartiallySelected(module)"
                              @update:modelValue="toggleAllModulePermissions(module)"
                              @click.stop
                            />
                            <span class="font-medium">{{ getModuleDisplayName(module.name) }}</span>
                            
                            <span 
                              v-if="!expandedModules.includes(module.key) && isModulePartiallySelected(module)"
                              class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full"
                            >
                              {{ module.permissions.filter(p => hasPermission(p.id)).length }}/{{ module.permissions.length }}
                            </span>
                          </div>
                          
                          <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            class="p-1 h-8 w-8"
                          >
                            <component
                              :is="expandedModules.includes(module.key) ? ChevronDown : ChevronRight"
                              class="h-4 w-4 transition-transform duration-200"
                            />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>

                    <TableRow v-if="expandedModules.includes(module.key)" class="bg-muted/30">
                      <TableCell class="p-0">
                        <div class="pl-12 pr-4 py-2 space-y-2">
                          <div 
                            v-for="permission in module.permissions" 
                            :key="permission.id"
                            class="flex items-center gap-3 py-1"
                          >
                            <Checkbox
                              :id="`permission-${permission.id}`"
                              :modelValue="hasPermission(permission.id)"
                              @update:modelValue="togglePermission(permission.id)"
                            />
                            <Label 
                              :for="`permission-${permission.id}`"
                              class="font-light"
                            >
                              {{ getPermissionDisplayName(permission.name) }}
                            </Label>
                          </div>
                        </div>
                      </TableCell>
                    </TableRow>
                  </template>
                </template>
                <TableRow v-else>
                  <TableCell class="h-24 text-center">
                    Sem módulos disponíveis.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </ViewBackground>

        <div class="flex justify-end gap-2 pt-4">
          <Button as-child type="button" variant="outline">
            <Link :href="route('roles.index')">Cancelar</Link>
          </Button>
          <Button type="submit" variant="default" :disabled="form.processing">
            {{ props.role ? 'Atualizar' : 'Cadastrar' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
