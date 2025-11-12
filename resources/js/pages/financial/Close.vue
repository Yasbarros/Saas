<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import ViewBackground from '@/components/ViewBackground.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { useFlashToasts } from '@/composables/useFlashToasts';
import { toast } from 'vue-sonner';

useFlashToasts();

type CashAccount = {
  id: number
  initial_balance: string
  user_id: number
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
  }
}

const props = defineProps<{
  cash: CashAccount
  totalBudgets: number
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Caixas',
        href: '/financial',
    },
    {
        title: 'Fechamento do caixa ' + props.cash.id,
        href: `/financial`,
    }
];

const form = useForm({
    paymentMethod: '',
    receipt: '',
    paymentDate: '',
    amount: '',
    paid: false
});

const handleSubmit = () => {
    form.post(route('financial.disable', props.cash.id));
};

</script>

<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="flex h-full flex-1 flex-col gap-2 rounded-xl p-4">
      <Heading
          :title='"Fechar Caixa " + props.cash.user?.name'
          :breadcrumbs="breadcrumbs"
      />
      <form @submit.prevent="handleSubmit"  class="lg:max-w-2/4">
        <ViewBackground class="space-y-4">
          <h2 class="font-semibold mb-4">
            Valor:
            <span class="text-gray-800">{{ props.totalBudgets ?? '0.00' }}</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <Label for="date" class="block text-sm font-medium text-gray-700 mb-1">Data do pagamento</Label>
              <Input id="date" placeholder="Selecione a data" v-model="form.paymentDate" type="date"/>
            </div>
            <div class="flex items-center gap-3">
              <div class="flex-1">
                <Label class="block text-sm font-medium text-gray-700 mb-1">Valor a Cobrar</Label>
                <Input v-model="form.amount" placeholder="Digite o valor" type="number"/>
              </div>
              <div class="flex items-center mt-6">
                <input v-model="form.paid" type="checkbox" class="mr-2" />
                <span class="text-sm text-gray-700">Pago</span>
              </div>
            </div>
          </div>
        </ViewBackground>
        <div class="flex justify-end gap-3">
          <Button as-child type="button" variant="outline">
              <Link :href="route('financial.show', props.cash.id)">Cancelar</Link>
          </Button>
          <Button type="submit" variant="default" >
              Fechar Caixa
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
