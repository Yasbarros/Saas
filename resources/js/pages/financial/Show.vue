<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData } from '@/types/index';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DeleteDialog from  '@/components/DeleteDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';

useFlashToasts();

type Budget = {
  id: number
  cash_id: number
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
  payment_details?: {
    cash: number;
    credit_card: number;
    debit_card: number;
    pix: number;
    other: number;
  }
}

type Cash = {
  id: number
  name: string
  status: number
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
  budgets: PaginationData<Budget>;
  cash: Cash;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Caixas',
        href: '/financial',
    },
    {
        title: 'Caixa ' + props.cash.user?.name,
        href: '/',
    }
];

const columns: ColumnDef<any>[] = [
  {
    accessorKey: 'id',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      return h('div', { class: 'pl-3' }, row.getValue('id'))
    },
  },
  {
    accessorKey: 'procedure.name',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Procedimento', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const procedure = row.original.procedure
      return h('div', { class: 'pl-3 capitalize' }, procedure ? procedure.name : 'N/A')
    },
  },
  {
    accessorKey: 'payment_details.cash',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Dinheiro', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const value = row.original.payment_details?.cash
      return h('div', { class: 'pl-3 lowercase' }, value ?? '-')
    },
  },
  {
    accessorKey: 'payment_details.pix',
    header: ({ column }) => 
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Pix', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const value = row.original.payment_details?.pix
      return h('div', { class: 'pl-3 lowercase' }, value ?? '-')
    },
  },
  {
    accessorKey: 'payment_details.debit_card',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Cartão de Débito', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const value = row.original.payment_details?.debit_card
      return h('div', { class: 'pl-3 lowercase' }, value ?? '-')
    },
  },
  {
    accessorKey: 'payment_details.credit_card',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Cartão de Crédito', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const value = row.original.payment_details?.credit_card
      return h('div', { class: 'pl-3 lowercase' }, value ?? '-')
    },
  },
  {
    accessorKey: 'payment_details.other',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Outros', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => {
      const value = row.original.payment_details?.other
      return h('div', { class: 'pl-3 lowercase' }, value ?? '-')
    },
  },
  {
    accessorKey: 'status',
    header: ({ column }) =>
      h(Button, { variant: 'ghost', onClick: () => column.toggleSorting(column.getIsSorted() === 'asc') },
        () => ['Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) =>
      h('div', { class: 'pl-3 text-green-600 dark:text-green-300 font-medium bg-green-100 dark:bg-green-900 w-min px-4 rounded-md' }, row.getValue('status') == 1 ? 'Recebido' : 'Em aberto'),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const budget = row.original
      return h(DropdownAction, { item: budget }, {
        default: () => [
          h(DeleteDialog, {
            asChild: true,
            title: 'Excluir orçamento',
            description: `Tem certeza que deseja excluir o orçamento de ${budget.procedure.name}?`,
            onConfirm: () => {
              router.delete(route('patients.destroy', budget.id), {
                preserveState: false,
                preserveScroll: true,
              })
            },
          }),
        ],
      })
    },
  },
]
</script>

<template>
  <Head title="Caixa Detalhes" />
  <AppLayout>
      <div class="flex h-full flex-1 flex-col p-4">
        <Heading
          :title="'Caixa ' + props.cash.user?.name"
          :breadcrumbs="breadcrumbs"
        />
        <DataTable
          :data="props.budgets.data"
          :pagination="{
              currentPage: props.budgets.current_page,
              lastPage: props.budgets.last_page,
              total: props.budgets.total,
              perPage: props.budgets.per_page,
              from: props.budgets.from,
              to: props.budgets.to,
          }"
          :columns="columns"
          :route-name="'financial.show'"
        >
          <template #actions>
            <div>
                <Button v-if="props.cash.status === 1" as-child variant="default">
                  <Link :href="`/financial/${props.cash.id}/close`">Fechar Caixa</Link>
                </Button>
                <Button v-else as-child variant="default">
                  <Link :href="`/financial/${props.cash.id}/open`">Abrir Caixa</Link>
                </Button>
            </div>
          </template>
        </DataTable>
      </div>
  </AppLayout>
</template>
