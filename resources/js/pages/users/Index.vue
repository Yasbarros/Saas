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
import ToggleUserStatusDialog from '@/components/ToggleUserStatusDialog.vue';
import { useFlashToasts } from '@/composables/useFlashToasts';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogTitle,
    DialogOverlay,
    DialogTrigger,
} from '@/components/ui/dialog';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Usuários',
        href: '/users',
    },
    {
        title: 'Listagem',
        href: '/users',
    }
];

const props = defineProps<{
    users: PaginationData<User>;
}>();

useFlashToasts();

const columns: ColumnDef<any>[] = [
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
    accessorKey: 'email',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
  },
  {
    accessorKey: 'phone',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Telefone', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', row.getValue('phone')),
  },
  {
    accessorKey: 'roles',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Funções', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const roles = row.getValue('roles');
      return h('div', (roles as { name: string }[]).map(role => role.name).join(', '));
    }
  },
  {
  accessorKey: 'is_active',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Status', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const isActive = row.getValue('is_active');
      // TODO: Componentizar a div de status
      return h('div', {
        class: isActive ? 'text-green-600 dark:text-green-300 font-medium bg-green-100 dark:bg-green-900 w-min px-4 rounded-md border-green-600 border' : 'text-red-600 dark:text-red-300 font-medium bg-red-100 dark:bg-red-900 w-min px-4 rounded-md border-red-600 border'
      }, isActive ? 'Ativo' : 'Inativo');
    }
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const user = row.original;

      return h(DropdownAction, { item: user }, {
        default: () => [
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/users/${user.id}` }, () => 'Visualizar')
          ),
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/users/${user.id}/edit` }, () => 'Editar')
          ),
          h(ToggleUserStatusDialog, { asChild: true, user }),
          h(DeleteDialog, {
            asChild: true,
            title: 'Excluir usuário',
            description: `Tem certeza que deseja excluir o usuário ${user.name}?`,
            onConfirm: () => {
              router.delete(route('users.destroy', user.id), {
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

</script>

<template>
    <Head title="Usuários" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col p-4">
          <Heading
            :title="'Usuários'"
            :breadcrumbs="breadcrumbs"
          />
          <NavBarLinks
            :links="usersNavBarLinks"
            class="mb-8"
          />
          <DataTable
            :data="props.users.data"
            :pagination="{
                currentPage: props.users.current_page,
                lastPage: props.users.last_page,
                total: props.users.total,
                perPage: props.users.per_page,
                from: props.users.from,
                to: props.users.to,
            }"
            :columns="columns"
            :route-name="'users.index'"
          >
            <template #actions>
              <div>
                  <Button as-child variant="default">
                    <Link href="/users/create">Cadastrar</Link>
                  </Button>
              </div>
            </template>
          </DataTable>
        </div>
    </AppLayout>
</template>
