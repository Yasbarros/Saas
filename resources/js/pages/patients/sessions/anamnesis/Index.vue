<script setup lang="ts"> 
import { ref } from 'vue'
import { Users, FileText } from 'lucide-vue-next'
import PatientsLayout from '@/layouts/PatientsLayout.vue'

defineOptions({
  layout: PatientsLayout,
})

const open = ref<string | null>(null)

const toggle = (menu: string) => {
  open.value = open.value === menu ? null : menu
}

const mainMenu = [
  {
    title: 'Atual',
    icon: Users,
    key: 'patients',
    children: [
      { label: 'Lista', href: '/patients' },
      { label: 'Novo', href: '/patients/create' },
    ]
  },
  {
    title: 'Histórico',
    icon: FileText,
    key: 'budgets',
    children: [
      { label: 'Ver Todos', href: '/budgets' },
      { label: 'Novo Orçamento', href: '/newbudget' },
    ]
  },
]
</script>

<template>
  <div>
    <aside class="w-48 rounded-2xl bg-gray-100 dark:bg-gray-900 px-4 py-6 border-r dark:border-gray-700">
      <nav>
        <ul class="space-y-1">
          <li v-for="item in mainMenu" :key="item.key">
            <button
              @click="toggle(item.key)"
              class="w-full flex items-center justify-between px-3 py-2 rounded text-sm text-left hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300"
            >
              <span class="flex items-center gap-2">
                <component :is="item.icon" class="w-4 h-4" />
                {{ item.title }}
              </span>
              <!-- <component :is="open === item.key ? ChevronDown : ChevronRight" class="w-4 h-4" /> -->
            </button>
          </li>
        </ul>
      </nav>
    </aside>
    <!-- <div>
      atual
    </div> -->
  </div>

</template>


