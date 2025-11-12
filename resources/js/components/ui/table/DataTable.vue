<script setup lang="ts">
import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  PaginationState,
  SortingState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ref, watch } from 'vue'
import { valueUpdater } from './utils'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'

interface PaginationInfo {
  currentPage: number
  lastPage: number
  total: number
  perPage: number
  from: number
  to: number
}

const props = defineProps<{
  data: any[]
  pagination: PaginationInfo
  columns: ColumnDef<any>[]
  routeName: string
  routeParams?: Record<string, any>
}>()

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})
const pagination = ref<PaginationState>({
  pageIndex: props.pagination.currentPage - 1,
  pageSize: props.pagination.perPage,
})
const pageSizes = [1, 2, 3, 5, 10, 15, 30, 40, 50, 100,];

const urlParams = new URLSearchParams(window.location.search)
const initialSearch = urlParams.get('search') || ''
const search = ref<string>(initialSearch)
let searchTimeout: ReturnType<typeof setTimeout>

// Temporariamente comentado pois com Watch está perdendo o focus no input de busca
// watch(search, (newSearch) => {
//   clearTimeout(searchTimeout)
//   searchTimeout = setTimeout(() => {
//     router.get(
//       route(props.routeName),
//       {
//         search: newSearch,
//         page: 1,
//         per_page: pagination.value.pageSize,
//       },
//       { preserveState: false, preserveScroll: true }
//     )
//   }, 500)
// })

const table = useVueTable({
  data: props.data,
  columns: props.columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  pageCount: props.pagination.lastPage,
  manualPagination: true,
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  onPaginationChange: updater => {
    if (typeof updater === 'function') {
      pagination.value = updater(pagination.value);
    } else {
      pagination.value = updater;
    }
    router.get(
      route(props.routeName, props.routeParams || {}),
      {
        page: pagination.value.pageIndex + 1,
        per_page: pagination.value.pageSize,
        search: search.value,
        // sort_field: sorting.value[0]?.id,
        // sort_direction: sorting.value.length == 0 ? undefined : (sorting.value[0]?.desc ? "desc" : "asc"),
      },
      { preserveState: false, preserveScroll: true }
    );
  },
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
    get pagination() { return pagination.value },
  },
})

const handleSubmit = (event: Event) => {
  event.preventDefault()

  router.get(
    route(props.routeName, props.routeParams || {}),
    {
      search: search.value,
      page: 1,
      per_page: pagination.value.pageSize,
    },
    { preserveState: false, preserveScroll: true }
  )
}
</script>

<template>
  <div class="w-full">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-4 justify-between">
      <form @submit="handleSubmit" class="flex items-center gap-2">
        <Input
          class="max-w-sm"
          placeholder="Buscar..."
          v-model="search"
          ref="searchInputRef"
        />
        <Button
          variant="secondary"
        >
          Buscar
        </Button>
      </form>
      <slot name="actions" />
    </div>
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  {{ JSON.stringify(row.original) }}
                </TableCell>
              </TableRow>
            </template>
          </template>

          <TableRow v-else>
            <TableCell
              :colspan="columns.length"
              class="h-24 text-center"
            >
              Sem resultados.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 mt-8 lg:mt-4 justify-between items-center">
      <div class="text-sm text-nowrap">
        Exibindo <span class="font-semibold">{{ props.pagination.from }}</span> - <span class="font-semibold">{{ props.pagination.to }}</span> de <span class="font-semibold">{{ props.pagination.total }}</span> resultados
      </div>

      <div class="flex flex-col lg:flex-row gap-6 lg:gap-4">
        <div class="flex items-center justify-center gap-4">
          <span class="text-sm text-nowrap overflow-hidden">Itens por página:</span>
          <!-- TODO: Trocar pelo DataSelect ou pelo ComboBox do Shadcn -->
          <Select :model-value="table.getState().pagination.pageSize.toString()" @update:model-value="(value) => table.setPageSize(Number(value))">
            <SelectTrigger class="h-8 w-[70px]">
              <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" />
            </SelectTrigger>
            <SelectContent side="top">
              <SelectItem v-for="pageSize in pageSizes" :key="pageSize" :value="pageSize.toString()">
                {{ pageSize }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <Pagination v-slot="{ page }" :items-per-page="props.pagination.perPage" :total="props.pagination.total" :page="props.pagination.currentPage">
          <PaginationContent v-slot="{ items }">
            <PaginationPrevious
              :disabled="!table.getCanPreviousPage()"
              @click="table.previousPage()"
            />

            <template v-for="(item, index) in items" :key="index">
              <PaginationItem
                v-if="item.type === 'page'"
                :value="item.value"
                :is-active="item.value === page"
                @click="table.setPageIndex(item.value - 1)"
              >
                {{ item.value }}
              </PaginationItem>
            </template>

            <PaginationEllipsis :index="4" />

            <PaginationNext
              :disabled="!table.getCanNextPage()"
              @click="table.nextPage()"
            />
          </PaginationContent>
        </Pagination>
      </div>
    </div>
  </div>
</template>