<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Heading from '@/components/Heading.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const userName = (page.props.auth as { user: User }).user.name.split(' ')[0];

const getGreeting = () => {
    const currentHour = new Date().getHours();
    if (currentHour >= 5 && currentHour < 12) {
        return `Bom dia, ${userName}!`;
    } else if (currentHour >= 12 && currentHour < 18) {
        return `Boa tarde, ${userName}!`;
    } else {
        return `Boa noite, ${userName}!`;
    }
};

const greetingTitle = getGreeting();

const breadcrumbs: BreadcrumbItem[] = [
    {
        // TODO: Rever essa gambi
        title: 'Bem-vindo',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading
                :title="greetingTitle"
                :breadcrumbs="breadcrumbs"
                class="!mb-0"
            />
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
