<script setup lang="ts">
import { ref, computed } from "vue" 
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import ViewBackground from '@/components/ViewBackground.vue';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';
import { Trash2 } from 'lucide-vue-next';
import { z } from "zod";

type Budget = {
  id: number
  cash_id: number
  cashier_session_id: number
  notes: string
  unit_value: number
  value: number
  date: string
  registration_dentist: string
  procedure_dentist: string
  execute_all_procedures: boolean
  status: number
  procedure: {
    id: number
    name: string
  }
  priceTable: {
    id: number
    name: string
  }
  patient: {
    id: number
    name: string
  }
}

const props = defineProps<{
    budget: Budget
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Financeiro',
        href: '/patients/invoices',
    },
    {
        title: 'Orçamento ' + props.budget.id,
        href: ``,
    }
];

const paymentMethodOptions = [
  { id: 1, name: 'Dinheiro', field: 'cash' },
  { id: 2, name: 'Cartão de Crédito', field: 'credit_card' },
  { id: 3, name: 'Cartão de Débito', field: 'debit_card' },
  { id: 4, name: 'Pix', field: 'pix' },
  { id: 5, name: 'Outro', field: 'other' },
];

const form = useForm({
  budget_id: props.budget.id,
  cash_id: props.budget.cash_id,
  cashier_session_id: props.budget.cashier_session_id,
  methods: [] as {
    method: string;
    amount: number;
    installments?: number | null;
  }[]
});

const paymentForms = ref([
  { method: null as number | null, value: 0, installments: 1, isPaid: false },
]);

const availablePaymentOptions = computed(() => {
  const selectedMethods = paymentForms.value
    .map(p => p.method)
    .filter(methodId => methodId !== null && methodId !== 2); 

  return paymentMethodOptions.filter(option => !selectedMethods.includes(option.id));
});

const addPaymentMethod = () => {
  paymentForms.value.push({ method: null, value: 0, installments: 1, isPaid: false });
};

const removePaymentMethod = (index: number) => {
  if (paymentForms.value.length > 1) {
    paymentForms.value.splice(index, 1);
  }
};

const handleSubmit = () => {
  form.methods = [];

  paymentForms.value.forEach(p => {
    if (!p.method || p.value <= 0) return;

    const option = paymentMethodOptions.find(o => o.id === p.method);
    if (option) {
      form.methods.push({
        method: option.field,
        amount: Number(p.value),
        installments: option.field === 'credit_card' ? p.installments : null,
      });
    }
  });

  if (form.methods.length === 0) {
    toast.error('Adicione pelo menos uma forma de pagamento com valor maior que zero.');
    return;
  }

  if( form.methods.length > 1 ) {
    const total = form.methods.reduce((sum, m) => sum + m.amount, 0);
    if (total !== Number(props.budget.value)) {
      toast.error('O valor total dos métodos de pagamento deve ser igual ao valor do orçamento.');
      return;
    }
  } else {
    if (form.methods[0].amount !== Number(props.budget.value)) {
      toast.error('O valor do pagamento deve ser igual ao valor do orçamento.');
      return;
    }
  }
  
  form.post(route('patients.invoices.storeCheckout', { id: props.budget.patient.id, budget: props.budget.id}));
};

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

</script>

<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="flex h-full flex-1 flex-col gap-2 rounded-xl p-4">
      <Heading
        :title='"Checkout " + props.budget.procedure.name'
        :breadcrumbs="breadcrumbs"
      />

      <form @submit.prevent="handleSubmit" class="lg:max-w-2/4">
        <ViewBackground class="space-y-4">
          <h2 class="font-semibold text-lg mb-4">
            Valor: <span class="text-gray-800">{{ formatCurrency(props.budget.value) }}</span>
          </h2>

          <div v-for="(payment, index) in paymentForms" :key="index" class="border rounded-lg p-4 relative">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-semibold">Forma de Pagamento {{ index + 1 }}</h3>
              <Button
                v-if="paymentForms.length > 1"
                type="button"
                @click="removePaymentMethod(index)"
                variant="ghost"
                size="sm"
                class="text-red-600 hover:text-red-800"
              >
                <Trash2 class="h-4 w-4" />
              </Button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
              <div>
                <Label class="block text-sm font-medium mb-1">Forma</Label>
                <Select v-model="payment.method">
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione" />
                  </SelectTrigger>
                  <SelectContent>
                    <template v-for="option in paymentMethodOptions" :key="option.id">
                       <SelectItem
                          v-if="!availablePaymentOptions.some(p => p.id === option.id) && payment.method !== option.id && option.id !== 2"
                          :value="option.id"
                          disabled
                        >
                          {{ option.name }} (Já adicionado)
                        </SelectItem>
                        <SelectItem v-else :value="option.id">
                          {{ option.name }}
                        </SelectItem>
                    </template>
                  </SelectContent>
                </Select>
              </div>

              <div v-if="payment.method === 2">
                <Label class="block text-sm font-medium mb-1">Parcelas</Label>
                <Input
                  type="number"
                  v-model="payment.installments"
                  placeholder="Nº de parcelas"
                />
              </div>
            </div>

            <div class="mb-3">
              <Label class="block text-sm font-medium mb-1">Valor</Label>
              <Input type="number" v-model="payment.value" placeholder="Digite o valor" />
            </div>

          </div>

          <Button @click.prevent="addPaymentMethod" variant="outline" class="mb-4">
            Adicionar outra forma
          </Button>
        </ViewBackground>

        <div class="flex justify-end gap-3">
          <Button as-child type="button" variant="outline">
            <Link :href="route('patients.invoices', { id: props.budget.patient.id })">Cancelar</Link>
          </Button>
          <Button type="submit" variant="default" :disabled="form.processing">Finalizar Checkout</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
