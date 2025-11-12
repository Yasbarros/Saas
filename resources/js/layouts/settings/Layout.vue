<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import NavBarLinks from '@/components/NavBarLinks.vue';

const navBarLinks = [
    {
        title: 'Perfil',
        href: '/settings/profile',
    },
    {
        title: 'Preferências',
        href: '/settings/appearance',
    },
];

defineProps<{
    breadcrumbItems: { title: string; href: string }[];
}>();

</script>

<template>
    <div class="p-4">
        <Heading
            title="Configurações"
            :breadcrumbs="breadcrumbItems"
        />
        <NavBarLinks
            :links="navBarLinks"
            class="mb-8"
        />
        <div>
            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
