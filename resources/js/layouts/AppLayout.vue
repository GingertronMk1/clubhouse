<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { home } from '@/routes';
import { index as sports } from '@/routes/sports';
import { index as clubs } from '@/routes/clubs';
import { index as locations } from '@/routes/locations';

const page = usePage();
const navigation = [
    { label: 'Sports', route: sports },
    { label: 'Clubs', route: clubs },
    { label: 'Locations', route: locations },
];
</script>

<template>
    <div class="min-h-screen bg-white text-slate-900">
        <header class="border-b border-slate-200 bg-white">
            <nav
                aria-label="Main navigation"
                class="mx-auto flex max-w-4xl flex-wrap items-center gap-x-8 gap-y-4 px-6 py-5"
            >
                <Link
                    :href="home()"
                    class="text-lg font-semibold tracking-tight"
                    >Clubhouse</Link
                >
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-medium">
                    <Link
                        v-for="item in navigation"
                        :key="item.label"
                        :href="item.route()"
                        :aria-current="
                            page.url.startsWith(item.route.url())
                                ? 'page'
                                : undefined
                        "
                        class="text-slate-600 hover:text-slate-950 aria-[current=page]:text-slate-950 aria-[current=page]:underline aria-[current=page]:underline-offset-8"
                        >{{ item.label }}</Link
                    >
                </div>
            </nav>
        </header>
        <slot />
    </div>
</template>
