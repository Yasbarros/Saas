<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { type BreadcrumbItem } from "@/types";
import { Head, Link } from "@inertiajs/vue3";
import DataTable from "@/components/ui/table/DataTable.vue";
import { PaginationData } from "@/types/index";
import { ArrowUpDown } from "lucide-vue-next";
import { h } from "vue";
import { Button } from "@/components/ui/button";
import DropdownAction from "@/components/ui/table/DataTableDropDown.vue";
import { ColumnDef } from "@tanstack/vue-table";
import DropdownMenuItem from "@/components/ui/dropdown-menu/DropdownMenuItem.vue";
import Heading from "@/components/Heading.vue";
import NavBarLinks from "@/components/NavBarLinks.vue";
import { useFlashToasts } from "@/composables/useFlashToasts";

useFlashToasts();

type CashAccount = {
  id: number
  name: string
  status: number
  user_id: number
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
  }
  total_cash: string;
  total_credit_card: string;
  total_debit_card: string;
  total_pix: string;
  total_other: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Caixas",
        href: "/financial",
    },
    {
        title: "Listagem",
        href: "/",
    }
];

const props = defineProps<{
    cashAccount: PaginationData<CashAccount>
}>();

const columns: ColumnDef<any>[] = [
  {
    accessorKey: "id",
    header: ({ column }) => {
        return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
        }, () => ["ID", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div", { class: "pl-3" }, row.getValue("id")),
  },
  {
    accessorKey: "name",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Nome", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div", { class: "pl-3" }, row.original.name || "-"),
  },
  // {
  //   accessorKey: "user.name",
  //   header: ({ column }) => {
  //     return h(Button, {
  //       variant: "ghost",
  //       onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
  //     }, () => ["Usuário", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
  //   },
  //   cell: ({ row }) => h("div", { class: "pl-3 capitalize" }, row.original.user?.name || "-"),
  // },
  {
    accessorKey: "total_cash",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Dinheiro", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div", { class: "pl-3" }, row.original.total_cash ?? "-"),
  },
  {
    accessorKey: "total_pix",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Pix", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div", { class: "pl-3" }, row.original.total_pix ?? "-"),
  },
  {
    accessorKey: "total_debit_card",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Cartão de débito", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div",{ class: "pl-3" }, row.original.total_debit_card ?? "-"),
  },
  {
    accessorKey: "total_credit_card",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Cartão de crédito", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div",{ class: "pl-3" }, row.original.total_credit_card ?? "-"),
  },
  {
    accessorKey: "total_other",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Outros", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => h("div",{ class: "pl-3" }, row.original.total_other ?? "-"),
  },
  {
    accessorKey: "status",
    header: ({ column }) => {
      return h(Button, {
        variant: "ghost",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      }, () => ["Status", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })])
    },
    cell: ({ row }) => {
      const isActive = row.original.status;
      // TODO: Componentizar a div de status
      return h("div", {
        class: isActive ? "text-green-600 dark:text-green-300 font-medium bg-green-100 dark:bg-green-900 w-min px-4 rounded-md" : "text-gray-600 dark:text-gray-300 font-medium bg-gray-200 dark:bg-gray-900 w-min px-4 rounded-md"
      }, isActive == 1 ? "Aberto" : "Fechado");
    }
  },
  {
    id: "actions",
    enableHiding: false,
    cell: ({ row }) => {
      const cash = row.original;
      return h(DropdownAction, { item: cash }, {
        default: () => [
          cash.status === 1 && h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/financial/${cash.id}/edit` }, () => "Editar")
          ),
          cash.status === 0 &&h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/financial/${cash.id}/open` }, () => "Abrir")
          ),
          h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/financial/${cash.id}` }, () => "Visualizar")
          ),
          cash.status === 1 && h(DropdownMenuItem, { asChild: true }, () =>
            h(Link, { href: `/financial/${cash.id}/close` }, () => "Fechar")
          ),
        ]
      });
    }
  }
];
</script>

<template>
    <Head title="Caixas" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col p-4">
          <Heading
            :title="'Caixas'"
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
            :data="props.cashAccount.data"
            :pagination="{
                currentPage: props.cashAccount.current_page,
                lastPage: props.cashAccount.last_page,
                total: props.cashAccount.total,
                perPage: props.cashAccount.per_page,
                from: props.cashAccount.from,
                to: props.cashAccount.to,
            }"
            :columns="columns"
            :route-name="'financial.index'"
          >
            <template #actions>
              <div>
                  <Button as-child variant="default">
                    <Link href="/financial/create">Cadastrar</Link>
                  </Button>
              </div>
            </template>
          </DataTable>
        </div>
    </AppLayout>
</template>
