<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import * as routes from '@/routes/games';
defineOptions({ layout: AppLayout });
type Named = { name: string };
const props = defineProps<{
    filters: {
        sport?: string | null;
        team?: string | null;
        club?: string | null;
    };
    sports: { id: string; name: string }[];
    clubs: { id: string; name: string }[];
    teams: { id: string; name: string; club_id: string; club: Named | null }[];
    records: {
        data: {
            id: string;
            name: string;
            start: string | null;
            team1: Named | null;
            team2: Named | null;
            competition: Named | null;
            location: Named | null;
        }[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        total: number;
    };
}>();
const filters = useForm({
    sport: props.filters.sport ?? '',
    team: props.filters.team ?? '',
    club: props.filters.club ?? '',
});
const teamGroups = computed(() => {
    const groups = props.clubs
        .map((club) => ({
            id: club.id,
            name: club.name,
            teams: props.teams.filter((team) => team.club_id === club.id),
        }))
        .filter((group) => group.teams.length > 0);
    const otherTeams = props.teams.filter(
        (team) => !props.clubs.some((club) => club.id === team.club_id),
    );
    if (otherTeams.length)
        groups.push({ id: '', name: 'Other clubs', teams: otherTeams });
    return groups;
});
function applyFilters() {
    filters.get(routes.index.url(), {
        preserveState: true,
        preserveScroll: true,
    });
}
function formatDate(value: string | null) {
    return value ? new Date(value).toLocaleString() : 'Kickoff to be announced';
}
</script>
<template>
    <main class="mx-auto max-w-6xl px-5 py-10 text-club-ink sm:px-8 sm:py-14">
        <Head title="Games" />
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="club-eyebrow">The match centre</p>
                <h1 class="mt-1 text-4xl font-black tracking-tight sm:text-5xl">
                    Games<span class="text-club-orange">.</span>
                </h1>
                <p class="mt-3 text-sm text-slate-600">
                    Find a fixture and follow the action.
                </p>
            </div>
            <Link
                :href="routes.create()"
                class="rounded-sm bg-club-orange px-5 py-3 text-sm font-bold text-white hover:bg-club-orange-dark"
                >Add game
                <span
                    aria-hidden="true"
                    class="ml-2"
                    >+</span
                ></Link
            >
        </div>
        <form
            class="club-panel mb-8 flex flex-wrap items-end gap-4 rounded-sm p-5"
            @submit.prevent="applyFilters"
        >
            <label
                class="flex min-w-40 flex-1 flex-col gap-1 text-sm font-medium"
                >Sport
                <select
                    v-model="filters.sport"
                    class="rounded border border-slate-300 p-2"
                >
                    <option value="">All sports</option>
                    <option
                        v-for="sport in sports"
                        :key="sport.id"
                        :value="sport.id"
                    >
                        {{ sport.name }}
                    </option>
                </select>
            </label>
            <label
                class="flex min-w-40 flex-1 flex-col gap-1 text-sm font-medium"
                >Club
                <select
                    v-model="filters.club"
                    class="rounded border border-slate-300 p-2"
                >
                    <option value="">All clubs</option>
                    <option
                        v-for="club in clubs"
                        :key="club.id"
                        :value="club.id"
                    >
                        {{ club.name }}
                    </option>
                </select>
            </label>
            <label
                class="flex min-w-40 flex-1 flex-col gap-1 text-sm font-medium"
                >Team
                <select
                    v-model="filters.team"
                    class="rounded border border-slate-300 p-2"
                >
                    <option value="">All teams</option>
                    <optgroup
                        v-for="group in teamGroups"
                        :key="group.id"
                        :label="group.name"
                    >
                        <option
                            v-for="team in group.teams"
                            :key="team.id"
                            :value="team.id"
                        >
                            {{ team.name }}
                        </option>
                    </optgroup>
                </select>
            </label>
            <button
                type="submit"
                class="rounded-sm bg-club-ink px-5 py-2.5 text-sm font-bold text-white hover:bg-club-orange"
            >
                Filter games
            </button>
        </form>
        <p
            v-if="!records.data.length"
            class="text-slate-600"
        >
            {{
                records.total === 0
                    ? filters.sport || filters.team || filters.club
                        ? 'No games match these filters.'
                        : 'No games yet. Add one to get started.'
                    : 'No games on this page.'
            }}
        </p>
        <div
            v-if="records.data.length"
            class="mb-3 flex items-center justify-between border-b-2 border-club-ink pb-3"
        >
            <h2 class="text-xs font-black tracking-[0.16em] uppercase">
                Fixtures &amp; results
            </h2>
            <span class="text-xs font-semibold text-slate-500"
                >{{ records.total }}
                {{ records.total === 1 ? 'game' : 'games' }}</span
            >
        </div>
        <ul
            v-if="records.data.length"
            class="space-y-3"
        >
            <li
                v-for="record in records.data"
                :key="record.id"
                class="club-panel rounded-sm border-l-4 border-l-club-orange p-5 transition hover:shadow-md"
            >
                <p
                    class="mb-3 text-xs font-bold tracking-wide text-club-orange uppercase"
                >
                    {{ record.competition?.name ?? 'Game' }}
                    <span class="text-slate-400">
                        / {{ formatDate(record.start) }}</span
                    >
                </p>
                <h2 class="text-xl font-black tracking-tight">
                    <Link
                        :href="routes.show(record.id)"
                        class="hover:text-club-orange"
                        >{{ record.name }}
                        <span
                            aria-hidden="true"
                            class="text-club-orange"
                            >↗</span
                        ></Link
                    >
                </h2>
                <p
                    v-if="record.team1 && record.team2"
                    class="mt-2 font-semibold text-slate-700"
                >
                    {{ record.team1.name }} vs {{ record.team2.name }}
                </p>
                <p
                    v-if="record.location"
                    class="mt-2 text-sm text-slate-500"
                >
                    {{ record.location.name }}
                </p>
            </li>
        </ul>
        <nav
            v-if="records.last_page > 1"
            aria-label="Game pages"
            class="mt-8 flex items-center justify-between gap-4 text-sm"
        >
            <Link
                v-if="records.prev_page_url"
                :href="records.prev_page_url"
                class="underline"
                >Previous</Link
            >
            <span
                v-else
                class="text-slate-400"
                >Previous</span
            >
            <span
                >Page {{ records.current_page }} of
                {{ records.last_page }}</span
            >
            <Link
                v-if="records.next_page_url"
                :href="records.next_page_url"
                class="underline"
                >Next</Link
            >
            <span
                v-else
                class="text-slate-400"
                >Next</span
            >
        </nav>
    </main>
</template>
