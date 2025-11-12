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
import { computed, onMounted } from 'vue';
import { toast } from 'vue-sonner';
// import DeleteDialog from  '../../components/DeleteDialog.vue'

type Patient = {
  id: number
  name: string
  date_of_birth: string
  phone?: string
  sex: string
  age: string
}

const props = defineProps<{
    patients: PaginationData<Patient>
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: '/patients',
    },
    {
        title: 'Aniversariantes',
        href: '/birthdays',
    }
];

const age = computed(() => {
    if (!props.patients.data[0].date_of_birth) return ''

    const today = new Date()
    const birthDate = new Date(props.patients.data[0].date_of_birth)

    let age = today.getFullYear() - birthDate.getFullYear()
    const month = today.getMonth() - birthDate.getMonth()

    if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
        age--
    }

    return age
})

const page = usePage();

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

onMounted(() => {
  if (flashSuccess.value) {
    toast.success(flashSuccess.value);
  }

  if (flashError.value) {
    toast.error('Erro', {
      description: flashError.value,
    });
  }
});

const columns: ColumnDef<any>[] = [
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
    accessorKey: 'age',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Idade', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'pl-3' }, row.getValue('age') || age.value),
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
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const patient = row.original;

      return h(DropdownAction, { item: patient }, {
        default: () => [
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `https://wa.me/55${patient.phone.replace(/\D/g, '')}` }, () => 'WhatsApp')
          ),
        ]
      });
    }
  }
]
</script>

<template>
    <Head title="Aniversariantes" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col p-4">
          <Heading
            :title="'Aniversariantes'"
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
            :route-name="'birthdays.index'"
          >
          </DataTable>
        </div>
    </AppLayout>
</template>