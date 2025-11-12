<script setup lang="ts">
import PatientsLayout from '@/layouts/PatientsLayout.vue'
import { Link, router } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData } from '@/types/index';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DeleteDialog from  '@/components/DeleteDialog.vue';
import CustomDialog from '@/components/CustomDialog.vue';
import {useFlashToasts} from '@/composables/useFlashToasts';

useFlashToasts();

defineOptions({
  layout: PatientsLayout,
})

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
  cashAccount?: {
    id: number
    name: string
  }
}

type CashAccount = {
  id: number
  name: string
}

type Patient = {
  id: number
  name: string
}

const props = defineProps<{
  budgets: PaginationData<Budget>;
  patient: Patient;
  cashAccounts: CashAccount[];
}>()

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
    accessorKey: 'budget.procedure.name',
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
    accessorKey: 'status',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('status')==1? 'Aprovado' : 'Em aberto'),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const budget = row.original;
      const patient = props.patient;
      return h(DropdownAction, { item: budget }, {
        default: () => [
          budget.status === 0 &&h(CustomDialog, {
            asChild: true,
            title: 'Aprovar orçamento',
            description: `Tem certeza que deseja aprovar o orçamento ${budget.procedure.name}?`,
            onConfirm: () => {
              router.post(route('patients.budgets.approve', patient.id), {
                budgets: [budget.id]
              }, {
                preserveState: false ,
                preserveScroll: true
              });
            }
          }),
          h(DeleteDialog, {
            asChild: true,
            title: 'Excluir orçamento',
            description: `Tem certeza que deseja excluir o orçamento de ${budget.procedure.name}?`,
            onConfirm: () => {
              router.delete(route('patients.budgets.destroy', budget.id), {
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
      :route-name="'patients.budgets'"
      :route-params="{ id: props.patient.id }"
    >
      <template #actions>
        <div>
            <Button as-child variant="default">
              <Link :href="`/patients/${props.patient.id}/budgets/form`">Cadastrar orçamento</Link>
            </Button>
        </div>
      </template>
    </DataTable>
  </div>
</template>
