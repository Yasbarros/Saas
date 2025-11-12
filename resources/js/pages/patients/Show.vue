<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PatientsLayout from '@/layouts/PatientsLayout.vue'
import { defineOptions } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useFlashToasts } from '@/composables/useFlashToasts';

useFlashToasts();

defineOptions({
  layout: PatientsLayout,
})

const props = defineProps<{
    patient: {
        id: number
        name: string
        social_name: string
        landline: string
        cpf: string
        rg: string
        phone: string
        email: string
        date_of_birth: string
        sex: string
        legal_guardian: string
        legal_guardian_cpf: string
        notes: string
        address: {
            street: string
            city: string
            state: string
            zip: string
            postal_code: string
            neighborhood: string
            number: string
            complement: string
        }
    },
}>()

</script>

<template>
    <Head title="Paciente" />
        <div>
            <div class="flex justify-between mb-6">
                <!-- TODO: Componentizar isso aqui -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Informações</h2>
                    <p class="text-sm text-gray-500">Dados cadastrais do paciente</p>
                    <div class="border-t mt-2"></div>
                </div>
                <div>
                    <!-- TODO: Alterar pra usar o componente de botão e o de link -->
                    <Link :href="route('patients.edit', props.patient?.id)" class="bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-800">
                    Editar
                    </Link>
                </div>
            </div>
            <!-- TODO: Componentizar os itens -->
            <div class="space-y-6 text-sm">
                <div>
                    <h3 class="text-gray-500">Nome</h3>
                    <p class="text-gray-900">{{ props.patient?.name }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Data de Nascimento</h3>
                    <p class="text-gray-900">{{ props.patient?.date_of_birth }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">CPF</h3>
                    <p class="text-gray-900">{{ props.patient?.cpf }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">RG</h3>
                    <p class="text-gray-900">{{ props.patient?.rg }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Nome Social</h3>
                    <p class="text-gray-900">{{ props.patient?.social_name }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Sexo</h3>
                    <p class="text-gray-900">{{ props.patient?.sex }}</p>
                </div>
                <div v-if="props.patient?.legal_guardian">
                    <h3 class="text-gray-500">Responsável Legal</h3>
                    <p class="text-gray-900">{{ props.patient?.legal_guardian }}</p>
                </div>
                <div v-if="props.patient?.legal_guardian_cpf">
                    <h3 class="text-gray-500">CPF do Responsável Legal</h3>
                    <p class="text-gray-900">{{ props.patient?.legal_guardian_cpf }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Observações</h3>
                    <p class="text-gray-900">{{ props.patient?.notes }}</p>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Contato</h1>
                    <p class="text-sm text-gray-500">Contato do paciente</p>
                    <div class="border-t mt-2"></div>
                </div>
                <div>
                    <h3 class="text-gray-500">Celular</h3>
                    <p class="text-gray-900">{{ props.patient?.phone }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Telefone Fixo</h3>
                    <p class="text-gray-900">{{ props.patient?.landline }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Email</h3>
                    <p class="text-gray-700">{{ props.patient?.email }}</p>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Endereço</h1>
                    <p class="text-sm text-gray-500">Endereço do paciente</p>
                    <div class="border-t mt-2"></div>
                </div>
                <div>
                    <h3 class="text-gray-500">Bairro</h3>
                    <p class="text-gray-900">{{ props.patient.address?.neighborhood || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Rua</h3>
                    <p class="text-gray-900">{{ props.patient.address?.street || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Número</h3>
                    <p class="text-gray-900">{{ props.patient.address?.number || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Cidade</h3>
                    <p class="text-gray-900">{{ props.patient.address?.city || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Estado</h3>
                    <p class="text-gray-900">{{ props.patient.address?.state || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">CEP</h3>
                    <p class="text-gray-900">{{ props.patient.address?.postal_code || 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-gray-500">Complemento</h3>
                    <p class="text-gray-900">{{ props.patient.address?.complement || 'Não informado' }}</p>
                </div>
            </div>
        </div>
</template>
