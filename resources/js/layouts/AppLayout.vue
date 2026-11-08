<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { home, login, logout } from '@/routes';
import { index as sports } from '@/routes/sports';
import { index as clubs } from '@/routes/clubs';
import { index as locations } from '@/routes/locations';
import { index as competitions } from '@/routes/competitions';
import { index as games } from '@/routes/games';
import { show as profile } from '@/routes/profile';

const page = usePage();
const navigation = [
    { label: 'Sports', route: sports },
    { label: 'Clubs', route: clubs },
    { label: 'Locations', route: locations },
    { label: 'Competitions', route: competitions },
    { label: 'Games', route: games },
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
                <div class="relative ml-auto text-sm font-medium">
                    <Link
                        v-if="!page.props.auth.user"
                        :href="login()"
                        class="text-slate-600 hover:text-slate-950"
                        >Log in</Link
                    >
                    <details v-else class="group">
                        <summary
                            class="flex cursor-pointer list-none items-center gap-2 rounded px-2 py-1 text-slate-700 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900"
                        >
                            {{ page.props.auth.user.name }}
                            <span aria-hidden="true" class="text-xs">▾</span>
                        </summary>
                        <div
                            class="absolute right-0 z-10 mt-2 w-44 rounded-md border border-slate-200 bg-white py-1 shadow-lg"
                        >
                            <Link
                                :href="profile()"
                                class="block px-4 py-2 text-slate-700 hover:bg-slate-100"
                                >Your profile</Link
                            >
                            <Link
                                :href="logout()"
                                as="button"
                                class="block w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-100"
                                >Log out</Link
                            >
                        </div>
                    </details>
                </div>
            </nav>
        </header>
        <slot />
    </div>
</template>
