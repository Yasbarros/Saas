<script setup lang="ts">
import PatientsLayout from '@/layouts/PatientsLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData, User } from '@/types/index';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DeleteDialog from  '@/components/DeleteDialog.vue';
import {useFlashToasts} from '@/composables/useFlashToasts';

useFlashToasts();

defineOptions({
  layout: PatientsLayout,
})

type Patient = {
  id: number;
  name: string;
}

type Budget = {
  id: number
  cash_id: number
  notes: string
  unit_value: number
  value: number
  paid: boolean
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
  cashAccount: {
    id: number
    name: string
  }
}

type CashAccount = {
  id: number
  name: string
}

const props = defineProps<{
  budgets: PaginationData<Budget>;
  patient: Patient;
  cashAccounts: CashAccount[];
}>();

const columns: ColumnDef<any>[] = [
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
    id: 'cashAccount',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Caixa', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    accessorFn: (row: Budget) => {
      const cash = props.cashAccounts.find(c => c.id === row.cash_id);
      return cash ? cash.name : 'N/A';
    },
    cell: ({ row }) => {
      return h('div', { class: 'pl-3 capitalize' }, row.getValue('cashAccount'));
    },
  },
  {
    accessorKey: 'procedure.name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Procedimento', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const procedure = row.original.procedure;
      return h('div', { class: 'pl-3 capitalize' }, procedure ? procedure.name : 'N/A');
    },
  },
  {
    accessorKey: 'date',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Data', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div',{ class: 'pl-3' }, row.getValue('date')),
  },
  {
    accessorKey: 'value',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Valor', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 lowercase ' }, row.getValue('value')),
  },
  {
    accessorKey: 'paid',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('paid')==1? 'Recebido' : 'Aguardando'),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const budget = row.original;

      return h(DropdownAction, { item: budget }, {
        default: () => [
          !budget.paid && h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/patients/${props.patient.id}/invoices/${budget.id}/checkout` }, () => 'Checkout')
          ),
          h(DeleteDialog, {
            asChild: true,
            triggerText: 'Cancelar',
            title: 'Cancelar pagamento',
            description: `Tem certeza que deseja cancelar o pagamento de ${budget.procedure.name} do paciente ${props.patient.name}?`,
            onConfirm: () => {
              router.delete(route('patients.invoices.destroy', budget.id), {
                preserveState: false ,
                preserveScroll: true
              });
            }
          })
        ]
      });
    }
  }
]

</script>

<template>
  <div class="flex h-full flex-1 flex-col p-4">
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
      :route-name="'patients.invoices'"
      :route-params="{ id: props.patient.id }"
    >
    </DataTable>
  </div>
</template>
