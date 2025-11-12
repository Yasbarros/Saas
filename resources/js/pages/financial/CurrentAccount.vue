<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData } from '@/types/index';
import { h, computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import Heading from '@/components/Heading.vue';
import NavBarLinks from '@/components/NavBarLinks.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowUpDown } from 'lucide-vue-next';

useFlashToasts();

type CashAccount = {
  id: number
  name: string
}

type CashierSession = {
  id: number
  cash_account_id: number
  opened_at: string
  initial_balance: string
  status: number
  closed_at?: string
  user_id_opened: number
  user_id_closed?: number
  created_at: string
  created_at_formatted: string
  updated_at: string
  user?: {
    id: number
    name: string
  }
  cash_account: CashAccount
  total_cash_entries: number;
  total_credit_card_entries: number;
  total_debit_card_entries: number;
  total_pix_entries: number;
  total_other_entries: number;
  total_entries: number;
  total_exits: number;
  final_balance_calculated: number;
};

type Summary = {
  total_initial_balance: string; 
  total_inflows: string;
  total_outflows: string;
  final_balance: string;
};

const props = defineProps<{
  cashierSessions: PaginationData<CashierSession>,
  summary: Summary
}>();

console.log(props.cashierSessions)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Financeiro',
    href: '/financial',
  },
  {
    title: 'Conta Corrente',
    href: '/current-account',
  }
];

const startDate = ref('');
const endDate = ref('');

const applyDateFilters = () => {
  const params = new URLSearchParams(window.location.search);
  
  params.delete('start_date');
  params.delete('end_date');
  
  if (startDate.value) {
    params.set('start_date', startDate.value);
  }
  
  if (endDate.value) {
    params.set('end_date', endDate.value);
  }
  
  router.get(window.location.pathname + '?' + params.toString());
};

const urlParams = new URLSearchParams(window.location.search);
startDate.value = urlParams.get('start_date') || '';
endDate.value = urlParams.get('end_date') || '';

const totalCash = computed(() => props.cashierSessions.data.length);
const totalInitialBalance = computed(() => props.summary.total_initial_balance);
const totalEntries = computed(() => props.summary.total_inflows);
const totalExits = computed(() => props.summary.total_outflows);
const totalFinalBalance = computed(() => props.summary.final_balance);

const columns: ColumnDef<CashierSession>[] = [
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('id')),
  },
  {
    accessorKey: 'created_at_formatted',
    header: ({ column }) => {
        return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Data de Abertura', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('created_at_formatted')),
  },
  {
    accessorKey: 'cash_account.name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Caixa', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 capitalize' }, row.original.cash_account?.name || '-')
  },
  {
    accessorKey: 'initial_balance',
    header: ({ column }) => {
        return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Balanço Inicial', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, parseFloat(row.original.initial_balance).toFixed(2))
  },
  {
    accessorKey: 'total_entries',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Total de entradas', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 text-green-600 font-medium' }, row.original.total_entries.toFixed(2))
  },
  {
    accessorKey: 'total_exits',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Total de saídas', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 text-red-600 font-medium' }, row.original.total_exits.toFixed(2))
  },
  {
    accessorKey: 'final_balance_calculated',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Saldo Final', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const saldo = row.original.final_balance_calculated;
      const classe = saldo >= 0 ? 'text-blue-600' : 'text-red-600';
      return h('div', { class: `pl-3 ${classe}` }, saldo.toFixed(2));
    }
  },
  {
    accessorKey: 'status',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const isActive = row.original.status;
      const closed_at = row.original.closed_at;
      return h('div', {
        class: closed_at ? 'text-gray-600 dark:text-gray-300 font-medium bg-gray-200 dark:bg-gray-900 w-min px-4 rounded-md' : 'text-green-600 dark:text-green-300 font-medium bg-green-100 dark:bg-green-900 w-min px-4 rounded-md' 
      }, closed_at ? 'Fechado' : 'Aberto');
    }
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const cash = row.original;
      return h(DropdownAction, { item: cash }, {
        default: () => [
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/financial/${cash.id}` }, () => 'Detalhes')
          )
        ]
      });
    }
  }
];
</script>

<template>
  <Head title="Conta Corrente" />

  <AppLayout>
    <div class="flex h-full flex-1 flex-col p-4">
      <Heading
        title="Conta Corrente"
        :breadcrumbs="breadcrumbs"
      />
      
      <NavBarLinks
        :links="[
          { title: 'Caixas', href: '/financial' },
          { title: 'Conta Corrente', href: '/current-account' },
          { title: 'Fluxo de Caixa', href: '/cash-flow' }
        ]"
        class="mb-8"
      />

      <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Total de Caixas
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ totalCash }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Balanço Inicial Total
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">R$ {{ totalInitialBalance }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Entradas Total
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">R$ {{ totalEntries }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Saídas Total
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">R$ {{ totalExits }}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Saldo Final Total
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-blue-600">R$ {{ totalFinalBalance }}</div>
          </CardContent>
        </Card>
      </div>

      <DataTable
        :data="props.cashierSessions.data"
        :columns="columns"
        :pagination="{
          currentPage: props.cashierSessions.current_page,
          lastPage: props.cashierSessions.last_page,
          total: props.cashierSessions.total,
          perPage: props.cashierSessions.per_page,
          from: props.cashierSessions.from,
          to: props.cashierSessions.to,
        }"
        :route-name="'current-account'"
      >
        <template #actions>
          <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="space-y-2">
                <Label for="data-inicio">Data Início</Label>
                <Input
                  id="data-inicio"
                  type="date"
                  v-model="startDate"
                />
              </div>
              
              <div class="space-y-2">
                <Label for="data-fim">Data Fim</Label>
                <Input
                  id="data-fim"
                  type="date"
                  v-model="endDate"
                />
              </div>
              
              <div class="flex items-end">
                <Button @click="applyDateFilters" class="w-full">
                  Buscar
                </Button>
              </div>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
