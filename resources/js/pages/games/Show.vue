<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
import * as competitions from '@/routes/competitions';
defineOptions({ layout: AppLayout });
type Named = { id: string; name: string };
type ScoringEntry = {
    id: string;
    timestamp: number | null;
    team: Named | null;
    scoring_event: (Named & { value: number }) | null;
};
const props = defineProps<{
    record: Named & {
        start: string | null;
        description: string;
        summary: string;
        sport: Named | null;
        competition: Named | null;
        club1_id: string;
        club2_id: string;
        club1: (Named & { club: Named | null }) | null;
        club2: (Named & { club: Named | null }) | null;
        location: Named | null;
        scoring_events: ScoringEntry[];
    };
    scoreTotals: { club1: number; club2: number } | null;
}>();
function remove() {
    if (window.confirm(`Delete ${props.record.name}?`))
        router.delete(routes.destroy.url(props.record.id));
}
function teamName(team: Named | null) {
    if (!team) return 'Team not recorded';
    const gameTeam =
        team.id === props.record.club1_id
            ? props.record.club1
            : team.id === props.record.club2_id
              ? props.record.club2
              : null;
    return [gameTeam?.club?.name, team.name].filter(Boolean).join(' — ');
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
        <p v-if="record.club1 && record.club2" class="mt-5 text-xl font-medium">
            {{ record.club1.club?.name }} {{ record.club1.name }} vs
            {{ record.club2.club?.name }} {{ record.club2.name }}
        </p>
        <p
            v-if="scoreTotals"
            class="mt-3 text-2xl font-semibold"
            aria-label="Score"
        >
            {{ scoreTotals.club1 }} – {{ scoreTotals.club2 }}
        </p>
        <p v-else class="mt-3 text-slate-600">
            {{
                record.scoring_events.length
                    ? 'Score unavailable until every event has a team and scoring type.'
                    : 'No score recorded.'
            }}
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
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Scoring events</h2>
            <p v-if="!record.scoring_events.length" class="mt-2 text-slate-600">
                No scoring events recorded.
            </p>
            <ul v-else class="mt-2 space-y-2">
                <li
                    v-for="event in record.scoring_events"
                    :key="event.id"
                    class="rounded border border-slate-200 p-3"
                >
                    <span class="font-medium">{{ teamName(event.team) }}</span>
                    —
                    {{ event.scoring_event?.name ?? 'Scoring type unavailable'
                    }}<span v-if="event.scoring_event">
                        ({{ event.scoring_event.value }} points)</span
                    ><span v-if="event.timestamp !== null">
                        · {{ event.timestamp }} seconds</span
                    >
                </li>
            </ul>
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
