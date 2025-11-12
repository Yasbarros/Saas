<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { SharedData } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage<SharedData>();

const sidebarNavItems = [
    {
        title: 'Tema',
        href: '/settings/appearance',
    },
];

const breadcrumbItems = [
    {
        title: 'Configurações',
        href: '/settings',
    },
    {
        title: 'Preferências',
        href: '/settings/appearance',
    },
];

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <AppLayout>
        
        <Head title="Appearance settings" />

        <SettingsLayout :breadcrumb-items="breadcrumbItems">
            <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
                <!-- TODO: Componentizar isso -->
                <aside class="w-full max-w-xl lg:w-48">
                    <nav class="flex flex-col space-x-0 space-y-1">
                        <Button
                            v-for="item in sidebarNavItems"
                            :key="item.href"
                            variant="ghost"
                            :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                            as-child
                        >
                            <Link :href="item.href">
                                {{ item.title }}
                            </Link>
                        </Button>
                    </nav>
                </aside>

                <Separator class="my-6 md:hidden" />

                <div class="space-y-6">
                    <HeadingSmall title="Configurações de aparência" description="Atualize as configurações de aparência da sua conta" />
                    <AppearanceTabs />
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
