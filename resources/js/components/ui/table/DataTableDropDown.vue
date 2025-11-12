<script setup lang="ts">
import { MoreHorizontal } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

defineProps<{
  item: {
    id: string | number
  }
}>();
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Abrir menu</span>
        <MoreHorizontal class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <slot name="label">
        <DropdownMenuLabel>Ações</DropdownMenuLabel>
      </slot>

      <slot :item="item">
        <DropdownMenuItem disabled>Nenhuma ação</DropdownMenuItem>
      </slot>
    </DropdownMenuContent>
  </DropdownMenu>
</template>