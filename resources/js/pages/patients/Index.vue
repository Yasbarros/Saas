<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData, User } from '@/types/index';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import Heading from '@/components/Heading.vue';
import NavBarLinks from '@/components/NavBarLinks.vue';
import DeleteDialog from  '../../components/DeleteDialog.vue'
import { useFlashToasts } from '@/composables/useFlashToasts';

useFlashToasts();

type Patient = {
  id: number
  name: string
  date_of_birth: string
  sex: string
  phone?: string
  cpf: string
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: '/patients',
    },
    {
        title: 'Listagem',
        href: '/patients',
    }
];

const props = defineProps<{
    patients: PaginationData<Patient>
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
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Nome', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 capitalize' }, row.getValue('name')),
  },
  {
    accessorKey: 'cpf',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['CPF', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div',{ class: 'pl-3' }, row.getValue('cpf')),
  },
  {
    accessorKey: 'phone',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Telefone', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3 lowercase ' }, row.getValue('phone')),
  },
  {
    accessorKey: 'sex',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Sexo', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('sex')),
  },
  {
    accessorKey: 'date_of_birth',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Data de Nascimento', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div',{ class: 'pl-3' }, row.getValue('date_of_birth')),
  },
  // Temporariamente comentado pois ainda não existe agendamento de consultas
  // {
  //   accessorKey: 'id',
  //   header: ({ column }) => {
  //     return h(Button, {
  //       variant: 'ghost',
  //       onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
  //     }, () => ['Ultima consulta', h(ArrowUpDown, { class: 'ml-2 h-4 w-4 ' })])
  //   },
  //   cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('id')),
  // },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const patient = row.original;

      return h(DropdownAction, { item: patient }, {
        default: () => [
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/patients/${patient.id}` }, () => 'Visualizar')
          ),
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/patients/${patient.id}/edit` }, () => 'Editar')
          ),
          h(DeleteDialog, {
            asChild: true,
            title: 'Excluir paciente',
            description: `Tem certeza que deseja excluir o paciente ${patient.name}?`,
            onConfirm: () => {
              router.delete(route('patients.destroy', patient.id), {
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
    <Head title="Pacientes" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col p-4">
          <Heading
            :title="'Pacientes'"
            :breadcrumbs="breadcrumbs"
          />
          <NavBarLinks
            :links="[
              { title: 'Listagem', href: '/patients' },
              { title: 'Aniversariantes', href: '/birthdays' }
            ]"
            class="mb-8"
          />
          <DataTable
            :data="props.patients.data"
            :pagination="{
                currentPage: props.patients.current_page,
                lastPage: props.patients.last_page,
                total: props.patients.total,
                perPage: props.patients.per_page,
                from: props.patients.from,
                to: props.patients.to,
            }"
            :columns="columns"
            :route-name="'patients.index'"
          >
            <template #actions>
              <div>
                  <Button as-child variant="default">
                    <Link href="/patients/create">Cadastrar</Link>
                  </Button>
              </div>
            </template>
          </DataTable>
        </div>
    </AppLayout>
</template>