<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    teams: { id: string; name: string; club: Named | null }[];
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
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Games" />
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Games</h1>
            <Link
                :href="routes.create()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add game</Link
            >
        </div>
        <form
            class="mb-8 flex flex-wrap items-end gap-4"
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
                    <option
                        v-for="team in teams"
                        :key="team.id"
                        :value="team.id"
                    >
                        {{ team.club?.name }} — {{ team.name }}
                    </option>
                </select>
            </label>
            <button
                type="submit"
                class="rounded bg-slate-900 px-4 py-2 text-white"
            >
                Filter games
            </button>
        </form>
        <p v-if="!records.data.length" class="text-slate-600">
            {{
                records.total === 0
                    ? filters.sport || filters.team || filters.club
                        ? 'No games match these filters.'
                        : 'No games yet. Add one to get started.'
                    : 'No games on this page.'
            }}
        </p>
        <ul v-else class="space-y-4">
            <li
                v-for="record in records.data"
                :key="record.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link :href="routes.show(record.id)" class="underline">{{
                        record.name
                    }}</Link>
                </h2>
                <p
                    v-if="record.team1 && record.team2"
                    class="mt-2 text-slate-700"
                >
                    {{ record.team1.name }} vs {{ record.team2.name }}
                </p>
                <p class="mt-2 text-sm text-slate-600">
                    {{ formatDate(record.start)
                    }}<span v-if="record.location">
                        · {{ record.location.name }}</span
                    ><span v-if="record.competition">
                        · {{ record.competition.name }}</span
                    >
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
            <span v-else class="text-slate-400">Previous</span>
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
            <span v-else class="text-slate-400">Next</span>
        </nav>
    </main>
</template>
