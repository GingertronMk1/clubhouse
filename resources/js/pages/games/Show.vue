<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
import * as competitions from '@/routes/competitions';
defineOptions({ layout: AppLayout });
type Named = { id: string; name: string };
const props = defineProps<{
    record: Named & {
        start: string | null;
        description: string;
        summary: string;
        sport: Named | null;
        competition: Named | null;
        team1: (Named & { club: Named | null }) | null;
        team2: (Named & { club: Named | null }) | null;
        location: Named | null;
    };
    lineups: {
        id: string;
        name: string;
        team: number;
        number: number | null;
        position: { name: string; preview_x: number; preview_y: number } | null;
    }[];
}>();
function initials(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}
function fieldCoordinate(value: number): string {
    return `${Math.max(0, Math.min(100, value))}%`;
}
function remove() {
    if (window.confirm(`Delete ${props.record.name}?`))
        router.delete(routes.destroy.url(props.record.id));
}
</script>
<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="record.name" /><Link
            :href="routes.index()"
            class="text-sm underline"
            >Back to games</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ record.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link :href="routes.edit(record.id)" class="underline"
                    >Edit</Link
                ><button
                    type="button"
                    class="text-red-700 underline"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </div>
        <p v-if="record.team1 && record.team2" class="mt-5 text-xl font-medium">
            {{ record.team1.club?.name }} {{ record.team1.name }} vs
            {{ record.team2.club?.name }} {{ record.team2.name }}
        </p>
        <p class="mt-3 text-slate-600">
            {{
                record.start
                    ? new Date(record.start).toLocaleString()
                    : 'Kickoff to be announced'
            }}<span v-if="record.location"> · {{ record.location.name }}</span
            ><span v-if="record.sport"> · {{ record.sport.name }}</span>
        </p>
        <p v-if="record.competition" class="mt-2 text-slate-600">
            Competition:
            <Link
                :href="competitions.show(record.competition.id)"
                class="underline"
                >{{ record.competition.name }}</Link
            >
        </p>
        <section class="mt-8" aria-labelledby="lineups-heading">
            <h2 id="lineups-heading" class="text-xl font-semibold">Lineups</h2>
            <div class="mt-4 grid gap-8 md:grid-cols-2">
                <section v-for="side in [1, 2]" :key="side">
                    <h3 class="text-lg font-semibold">
                        {{
                            (side === 1 ? record.team1 : record.team2)?.club
                                ?.name
                        }}
                        {{ (side === 1 ? record.team1 : record.team2)?.name }}
                    </h3>
                    <p
                        v-if="!lineups.some((player) => player.team === side)"
                        class="mt-3 text-slate-600"
                    >
                        No players selected.
                    </p>
                    <template v-else>
                        <div
                            class="relative mt-4 aspect-square w-full overflow-hidden rounded-lg border-4 border-emerald-800 bg-emerald-700"
                            :aria-label="`Team ${side} lineup field`"
                        >
                            <div
                                class="pointer-events-none absolute inset-4 border-2 border-white/70"
                                aria-hidden="true"
                            >
                                <div
                                    class="absolute top-1/2 w-full border-t-2 border-white/70"
                                ></div>
                                <div
                                    class="absolute top-1/2 left-1/2 h-1/4 w-1/4 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white/70"
                                ></div>
                            </div>
                            <div class="absolute inset-5" aria-hidden="true">
                                <div
                                    v-for="player in lineups.filter(
                                        (item) =>
                                            item.team === side && item.position,
                                    )"
                                    :key="player.id"
                                    :style="{
                                        left: fieldCoordinate(
                                            player.position!.preview_x,
                                        ),
                                        top: fieldCoordinate(
                                            player.position!.preview_y,
                                        ),
                                    }"
                                    class="absolute flex min-h-10 min-w-10 -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full border-2 border-white bg-slate-900 px-1 text-xs font-semibold text-white shadow"
                                >
                                    <span>{{ initials(player.name) }}</span
                                    ><span v-if="player.number !== null">{{
                                        player.number
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <ul class="mt-4 space-y-1 text-sm text-slate-700">
                            <li
                                v-for="player in lineups.filter(
                                    (item) => item.team === side,
                                )"
                                :key="player.id"
                            >
                                <span v-if="player.number !== null"
                                    >{{ player.number }} — </span
                                >{{ player.name
                                }}<span v-if="player.position">
                                    · {{ player.position.name }}</span
                                >
                            </li>
                        </ul>
                    </template>
                </section>
            </div>
        </section>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Description</h2>
            <p class="mt-2 whitespace-pre-wrap text-slate-600">
                {{ record.description }}
            </p>
        </section>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Summary</h2>
            <p class="mt-2 whitespace-pre-wrap text-slate-600">
                {{ record.summary }}
            </p>
        </section>
    </main>
</template>
