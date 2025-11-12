<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import DataTable from '@/components/ui/table/DataTable.vue';
import { PaginationData, User } from '@/types/index';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
// import { Checkbox } from '@/components/ui/checkbox';
import DropdownAction from '@/components/ui/table/DataTableDropDown.vue';
import { ColumnDef } from '@tanstack/vue-table';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import Heading from '@/components/Heading.vue';
import NavBarLinks from '@/components/NavBarLinks.vue';
import { usersNavBarLinks } from '@/utils/navBarLinks';
import DeleteDialog from '@/components/DeleteDialog.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';

const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Usuários',
      href: '/users',
    },
    {
      title: 'Funções',
      href: '/roles',
    },
];

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
  roles: PaginationData<Role>;
}>();

const columns: ColumnDef<Role>[] = [
//   {
//     id: 'select',
//     header: ({ table }) => h(Checkbox, {
//       'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
//       'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
//       'ariaLabel': 'Select all',
//     }),
//     cell: ({ row }) => h(Checkbox, {
//       'modelValue': row.getIsSelected(),
//       'onUpdate:modelValue': value => row.toggleSelected(!!value),
//       'ariaLabel': 'Select row',
//     }),
//     enableSorting: false,
//     enableHiding: false,
//   },
  {
    accessorKey: 'id',
    header: ({ column }) => {
        return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', row.getValue('id')),
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Nome', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const role = row.original;

      return h(DropdownAction, { item: role }, {
        default: () => [
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/roles/${role.id}` }, () => 'Visualizar')
          ),
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/roles/${role.id}/edit` }, () => 'Editar')
          ),
          h(DeleteDialog, {
            asChild: true,
            title: 'Excluir função',
            description: `Tem certeza que deseja excluir a função ${role.name}?`,
            onConfirm: () => {
              router.delete(route('roles.destroy', role.id), {
                preserveState: false,
                preserveScroll: true
              });
            }
          })
        ]
      });
    }
  }
]

useFlashToasts()

</script>

<template>
  <Head title="Funções" />
  <AppLayout>
    <div class="flex h-full flex-1 flex-col p-4">
      <Heading
        :title="'Funções'"
        :breadcrumbs="breadcrumbs"
      />
      <NavBarLinks
        :links="usersNavBarLinks"
        class="mb-8"
      />
      <DataTable
        :data="props.roles.data"
        :pagination="{
          currentPage: props.roles.current_page,
          lastPage: props.roles.last_page,
          total: props.roles.total,
          perPage: props.roles.per_page,
          from: props.roles.from,
          to: props.roles.to,
        }"
        :columns="columns"
        :route-name="'roles.index'"
      >
        <template #actions>
          <div>
            <Button as-child variant="default">
              <Link :href="route('roles.create')">Cadastrar</Link>
            </Button>
          </div>
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>
