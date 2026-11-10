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
    { label: 'Games', route: games },
    { label: 'Competitions', route: competitions },
    { label: 'Clubs', route: clubs },
    { label: 'Sports', route: sports },
    { label: 'Locations', route: locations },
];
</script>

<template>
    <div class="min-h-screen bg-club-paper text-club-ink">
        <header class="border-b-4 border-club-orange bg-club-ink text-white">
            <div
                class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-8"
            >
                <Link
                    :href="home()"
                    class="flex items-center gap-3"
                    aria-label="Clubhouse home"
                >
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-sm bg-club-orange text-xl font-black italic"
                        >C</span
                    >
                    <span class="text-xl font-black tracking-tight"
                        >CLUBHOUSE<span class="text-club-orange">.</span></span
                    >
                </Link>
                <div class="relative text-sm font-semibold">
                    <Link
                        v-if="!page.props.auth.user"
                        :href="login()"
                        class="rounded border border-white/30 px-4 py-2 transition hover:border-white hover:bg-white/10"
                        >Log in <span aria-hidden="true">↗</span></Link
                    >
                    <details
                        v-else
                        class="group"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-2 rounded border border-white/25 px-4 py-2 hover:bg-white/10"
                        >
                            {{ page.props.auth.user.name }}
                            <span aria-hidden="true">⌄</span>
                        </summary>
                        <div
                            class="absolute right-0 z-20 mt-2 w-44 rounded border border-club-line bg-white py-1 text-club-ink shadow-xl"
                        >
                            <Link
                                :href="profile()"
                                class="block px-4 py-2 hover:bg-club-paper"
                                >Your profile</Link
                            >
                            <Link
                                :href="logout()"
                                as="button"
                                class="block w-full px-4 py-2 text-left hover:bg-club-paper"
                                >Log out</Link
                            >
                        </div>
                    </details>
                </div>
            </div>
            <nav
                aria-label="Main navigation"
                class="border-t border-white/10"
            >
                <div
                    class="mx-auto flex max-w-6xl gap-7 overflow-x-auto px-5 sm:px-8"
                >
                    <Link
                        v-for="item in navigation"
                        :key="item.label"
                        :href="item.route()"
                        :aria-current="
                            page.url.startsWith(item.route.url())
                                ? 'page'
                                : undefined
                        "
                        class="shrink-0 border-b-2 border-transparent py-3 text-sm font-bold text-white/70 transition hover:text-white aria-[current=page]:border-club-orange aria-[current=page]:text-white"
                        >{{ item.label }}</Link
                    >
                </div>
            </nav>
        </header>
        <slot />
        <footer class="mt-16 border-t border-club-line bg-white">
            <div
                class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-5 py-7 text-xs font-semibold text-slate-500 sm:px-8"
            >
                <span class="font-black tracking-wide text-club-ink"
                    >CLUBHOUSE<span class="text-club-orange">.</span></span
                >
                <span>For the love of the game.</span>
            </div>
        </footer>
    </div>
</template>
