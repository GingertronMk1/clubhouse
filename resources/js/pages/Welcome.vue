<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as sportsIndex, show as sportShow } from '@/routes/sports';
import { index as clubsIndex, show as clubShow } from '@/routes/clubs';
import { index as gamesIndex } from '@/routes/games';

defineOptions({ layout: AppLayout });
defineProps<{
    sports: { id: string; slug: string; name: string; description: string }[];
    clubs: { id: string; slug: string; name: string; description: string }[];
}>();

function initials(name: string): string {
    return name
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();
}
</script>

<template>
    <main>
        <Head title="Home" />
        <section class="relative overflow-hidden bg-club-ink text-white">
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -top-28 right-[-5%] h-96 w-96 rotate-12 rounded-full border-[48px] border-white/5"
            ></div>
            <div
                aria-hidden="true"
                class="pointer-events-none absolute right-[12%] bottom-0 hidden h-64 w-px rotate-45 bg-white/15 md:block"
            ></div>
            <div
                class="relative mx-auto grid max-w-6xl gap-8 px-5 py-14 sm:px-8 md:grid-cols-[1fr_auto] md:items-end md:py-20"
            >
                <div class="max-w-2xl">
                    <p
                        class="mb-5 flex items-center gap-2 text-xs font-bold tracking-[0.2em] text-orange-300 uppercase"
                    >
                        <span
                            class="h-2 w-2 rounded-full bg-club-orange"
                        ></span>
                        Your sporting community
                    </p>
                    <h1
                        class="text-5xl leading-[0.95] font-black tracking-tight sm:text-6xl lg:text-7xl"
                    >
                        The game brings<br /><span class="text-orange-300"
                            >us together.</span
                        >
                    </h1>
                    <p
                        class="mt-6 max-w-lg text-base leading-7 text-white/70 sm:text-lg"
                    >
                        Find your clubs, follow the action and get closer to the
                        sports you love.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="gamesIndex()"
                            class="rounded-sm bg-club-orange px-5 py-3 text-sm font-bold text-white transition hover:bg-club-orange-dark"
                            >Explore games
                            <span
                                aria-hidden="true"
                                class="ml-3"
                                >↗</span
                            ></Link
                        >
                        <Link
                            :href="clubsIndex()"
                            class="rounded-sm border border-white/35 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                            >Find a club
                            <span
                                aria-hidden="true"
                                class="ml-3"
                                >→</span
                            ></Link
                        >
                    </div>
                </div>
                <div
                    class="flex gap-8 border-t border-white/20 pt-5 md:border-t-0 md:border-l md:pt-0 md:pl-8"
                >
                    <div>
                        <p class="text-3xl font-black">{{ sports.length }}</p>
                        <p
                            class="mt-1 text-xs font-bold tracking-wider text-white/55 uppercase"
                        >
                            Sports
                        </p>
                    </div>
                    <div>
                        <p class="text-3xl font-black">{{ clubs.length }}</p>
                        <p
                            class="mt-1 text-xs font-bold tracking-wider text-white/55 uppercase"
                        >
                            Clubs
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-6xl px-5 py-12 sm:px-8 sm:py-16">
            <section aria-labelledby="sports-heading">
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <p class="club-eyebrow">Find your game</p>
                        <h2
                            id="sports-heading"
                            class="mt-1 text-3xl font-black tracking-tight"
                        >
                            Explore sports<span class="text-club-orange"
                                >.</span
                            >
                        </h2>
                    </div>
                    <Link
                        :href="sportsIndex()"
                        class="shrink-0 text-sm font-bold text-club-orange hover:underline"
                        >All sports <span aria-hidden="true">→</span></Link
                    >
                </div>
                <p
                    v-if="sports.length === 0"
                    class="club-panel rounded-sm p-6 text-sm text-slate-600"
                >
                    No sports yet.
                </p>
                <ul
                    v-else
                    class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <li
                        v-for="(sport, index) in sports"
                        :key="sport.id"
                    >
                        <Link
                            :href="sportShow(sport.slug)"
                            class="club-panel group flex h-full min-h-40 flex-col justify-between rounded-sm border-t-4 border-t-club-orange p-5 transition hover:-translate-y-1 hover:shadow-lg"
                        >
                            <span
                                class="text-xs font-black tracking-widest text-slate-400"
                                >{{ String(index + 1).padStart(2, '0') }} /
                                SPORT</span
                            >
                            <div>
                                <div
                                    class="flex items-end justify-between gap-3"
                                >
                                    <h3
                                        class="text-xl font-black tracking-tight"
                                    >
                                        {{ sport.name }}
                                    </h3>
                                    <span
                                        aria-hidden="true"
                                        class="text-xl text-club-orange transition group-hover:translate-x-1"
                                        >↗</span
                                    >
                                </div>
                                <p
                                    class="mt-2 line-clamp-2 text-sm leading-5 text-slate-500"
                                >
                                    {{ sport.description }}
                                </p>
                            </div>
                        </Link>
                    </li>
                </ul>
            </section>

            <section
                aria-labelledby="clubs-heading"
                class="mt-14"
            >
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div>
                        <p class="club-eyebrow">The heart of the game</p>
                        <h2
                            id="clubs-heading"
                            class="mt-1 text-3xl font-black tracking-tight"
                        >
                            Our clubs<span class="text-club-orange">.</span>
                        </h2>
                        <p class="mt-2 text-sm text-slate-500">
                            Meet the communities making sport happen.
                        </p>
                    </div>
                    <Link
                        :href="clubsIndex()"
                        class="shrink-0 text-sm font-bold text-club-orange hover:underline"
                        >All clubs <span aria-hidden="true">→</span></Link
                    >
                </div>
                <p
                    v-if="clubs.length === 0"
                    class="club-panel rounded-sm p-6 text-sm text-slate-600"
                >
                    No clubs yet.
                </p>
                <ul
                    v-else
                    class="club-panel grid divide-y divide-club-line rounded-sm md:grid-cols-2 md:divide-y-0"
                >
                    <li
                        v-for="club in clubs.slice(0, 8)"
                        :key="club.id"
                        class="border-club-line md:border-b md:odd:border-r"
                    >
                        <Link
                            :href="clubShow(club.slug)"
                            class="group flex h-full items-center gap-4 p-4 transition hover:bg-orange-50/50 sm:p-5"
                        >
                            <span
                                aria-hidden="true"
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-sm bg-club-ink text-sm font-black text-white"
                                >{{ initials(club.name) }}</span
                            >
                            <span class="min-w-0 flex-1"
                                ><span
                                    class="block font-bold group-hover:text-club-orange"
                                    >{{ club.name }}</span
                                ><span
                                    class="mt-1 line-clamp-1 block text-sm text-slate-500"
                                    >{{ club.description }}</span
                                ></span
                            >
                            <span
                                aria-hidden="true"
                                class="text-club-orange"
                                >→</span
                            >
                        </Link>
                    </li>
                </ul>
            </section>
        </div>
    </main>
</template>
