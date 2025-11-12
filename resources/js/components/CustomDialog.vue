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
import { reactive } from 'vue';

const props = defineProps<{
  title?: string;
  description?: string;
  onConfirm: () => void;
  triggerText?: string;
}>();

const title = reactive({ title: props.title || "Aprovar item" });
const description = reactive({ description: props.description || "Tem certeza que deseja aprovar este item?" });

</script>

<template>
  <Dialog>
    <DropdownMenu>
      <DialogTrigger as-child>
        <DropdownMenuItem class="cursor-pointer">
          {{ props?.triggerText || 'Aprovar' }}
        </DropdownMenuItem>
      </DialogTrigger>
    </DropdownMenu>

    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ title.title }}</DialogTitle>
        <DialogDescription>
          {{ description.description }}
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button :variant="'default'" @click="props.onConfirm">
          {{ props?.triggerText || 'Aprovar' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
