<script setup lang="ts">
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    is_active: boolean;
  };
}>();

function toggleStatus() {
  if (props.user.is_active) {
    router.post(route('users.deactivate', props.user.id), {}, {
      preserveState: false,
      preserveScroll: true,
    });
  } else {
    router.post(route('users.activate', props.user.id), {}, {
      preserveState: false,
      preserveScroll: true,
    });
  }
}
</script>

<template>
  <Dialog>
    <DropdownMenu>
      <DialogTrigger as-child>
        <DropdownMenuItem class="cursor-pointer">
          {{ user.is_active ? 'Desativar' : 'Reativar' }}
        </DropdownMenuItem>
      </DialogTrigger>
    </DropdownMenu>

    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ user.is_active ? 'Desativar usuário' : 'Reativar usuário' }}</DialogTitle>
        <DialogDescription>
          Tem certeza que deseja {{ user.is_active ? 'desativar' : 'reativar' }} o usuário <strong>{{ user.name }}</strong>?
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button @click="toggleStatus">
          {{ user.is_active ? 'Desativar' : 'Reativar' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
