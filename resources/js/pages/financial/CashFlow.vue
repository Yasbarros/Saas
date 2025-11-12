<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData } from '@/types/index';
import { h, computed} from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import Heading from '@/components/Heading.vue';
import NavBarLinks from '@/components/NavBarLinks.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';
import { ArrowUpDown } from 'lucide-vue-next';

useFlashToasts();

type paid_budget = {
  id: number
  budget_id: number
  cash_id: number
  cash: string
  credit_card: string
  debit_card: string
  pix: string
  created_at: string
}

type CashierSession = {
  id: number
  cash_account_id: number
  final_balance: string
  initial_balance: string
  status: number
  closed_at: string
  user_id_opened: number
  user_id_closed: number
  created_at: string
  created_at_formatted: string
  updated_at: string
  user?: {
    id: number
    name: string
  }
  cash_account?: {
    id: number
    name: string
  }
  paid_budgets: paid_budget[]
};

const props = defineProps<{
  cashierSession: PaginationData<CashierSession>
}>();

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

const aggregatedCashData = computed(() => {
  return props.cashierSession.data.map(cash => {
    const parseNumber = (v: any) => {
      if (v === null || v === undefined || v === '') return 0;
      if (typeof v === 'number') return v;
      const normalized = String(v).replace(/\./g, '').replace(',', '.');
      const n = parseFloat(normalized);
      return isNaN(n) ? 0 : n;
    };

    const totalEntries = parseNumber(cash.total_entries).toFixed(2);
    const totalExits = parseNumber(cash.total_exits).toFixed(2);
    const initialBalance = parseNumber(cash.initial_balance);
    const finalBalance = (initialBalance + parseFloat(totalEntries) - parseFloat(totalExits)).toFixed(2);

    return {
      ...cash,
      total_entries: totalEntries,
      total_exits: totalExits,
      final_balance: finalBalance,
    };
  });
});

const columns: ColumnDef<any>[] = [
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
    cell: ({ row }) => h('div', { class: 'pl-3 capitalize' }, row.original.cash_account?.name || '-'),
  },
  {
    accessorKey: 'total_entries',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Total de entradas', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 text-green-600 font-medium' }, row.original.total_entries ?? '0,00'),
    //TODO: Testar essa logica de exibição
    // cell: ({ row }) => {
    //   const value = Number(row.original.total_entries)
    //   const displayValue = value === 0 ? '-' : value.toFixed(2)

    //   return h(
    //     'div',
    //     { class: 'pl-3 text-green-600 font-medium' },
    //     displayValue
    //   )
    // },
  },
  {
    accessorKey: 'total_exits',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Total de saídas', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 text-red-600 font-medium' }, row.original.total_exits ?? '0,00'),
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

      <DataTable
        :data="aggregatedCashData"
        :columns="columns"
        :pagination="{
          currentPage: props.cashierSession.current_page,
          lastPage: props.cashierSession.last_page,
          total: props.cashierSession.total,
          perPage: props.cashierSession.per_page,
          from: props.cashierSession.from,
          to: props.cashierSession.to,
        }"
        :route-name="'cash-flow'"
      >
      </DataTable>
    </div>
  </AppLayout>
</template>
