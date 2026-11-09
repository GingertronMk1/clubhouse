<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import { computed } from 'vue';
import { useGameTeamEvents } from '@/composables/useGameTeamEvents';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

type GameEvent = {
    name: string;
    score: number;
    pivot: {
        id: string;
        competitor_type: string;
        competitor_id: string;
        occurred_at: string;
        description: string | null;
    };
};

const props = defineProps<{
    game: {
        id: string;
        name: string;
        team1_id: string;
        team2_id: string;
        team1: { name: string; club: { name: string } | null } | null;
        team2: { name: string; club: { name: string } | null } | null;
        users: { name: string; pivot: { id: string; team: number } }[];
        sport_events: GameEvent[];
    };
}>();

function refreshGame(): void {
    router.reload({ only: ['game'] });
}

const liveUpdatesAvailable = Boolean(
    import.meta.env.VITE_PUSHER_APP_KEY &&
    import.meta.env.VITE_PUSHER_APP_CLUSTER,
);

if (liveUpdatesAvailable) {
    const { channel } = useEchoPublic(
        `games.${props.game.id}`,
        'GameEventRecorded',
        refreshGame,
    );
    channel().subscribed(refreshGame);
}

const { team1Events, team2Events, team1Score, team2Score } = useGameTeamEvents(
    () => props.game,
);
const timeline = computed(() => {
    const team1Ids = new Set(team1Events.value.map((event) => event.pivot.id));
    const team2Ids = new Set(team2Events.value.map((event) => event.pivot.id));

    return props.game.sport_events
        .filter(
            (event) =>
                team1Ids.has(event.pivot.id) || team2Ids.has(event.pivot.id),
        )
        .slice()
        .sort(
            (a, b) =>
                b.pivot.occurred_at.localeCompare(a.pivot.occurred_at) ||
                a.pivot.id.localeCompare(b.pivot.id),
        )
        .map((event) => ({
            event,
            side: team1Ids.has(event.pivot.id) ? 1 : 2,
        }));
});

function competitorName(event: GameEvent): string | undefined {
    if (event.pivot.competitor_type !== 'App\\Models\\GameUser') {
        return undefined;
    }

    return props.game.users.find(
        (user) => user.pivot.id === event.pivot.competitor_id,
    )?.name;
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="`Live Game: ${game.name}`" />
        <h1 class="text-3xl font-semibold">{{ game.name }}</h1>
        <p v-if="!liveUpdatesAvailable" class="mt-2 text-sm text-slate-600">
            Live updates are unavailable. Refresh the page to see new events.
        </p>
        <h2 class="mt-8 text-xl font-semibold">Game events</h2>
        <div
            class="mt-4 grid grid-cols-2 gap-4 border-b border-slate-200 pb-3 font-semibold"
        >
            <h3>
                {{ game.team1?.club?.name }} {{ game.team1?.name ?? 'Team 1' }}
                <span>{{ team1Score }}</span>
            </h3>
            <h3>
                {{ game.team2?.club?.name }} {{ game.team2?.name ?? 'Team 2' }}
                <span>{{ team2Score }}</span>
            </h3>
        </div>
        <p v-if="timeline.length === 0" class="mt-4 text-slate-600">
            No events yet.
        </p>
        <ol v-else class="mt-4 space-y-3">
            <li
                v-for="{ event, side } in timeline"
                :key="event.pivot.id"
                class="grid grid-cols-2 gap-4"
            >
                <div
                    :class="side === 1 ? 'col-start-1' : 'col-start-2'"
                    class="rounded-lg border border-slate-200 p-4"
                >
                    <p class="font-medium">{{ event.name }}</p>
                    <p
                        v-if="competitorName(event)"
                        class="mt-1 text-sm text-slate-600"
                    >
                        {{ competitorName(event) }}
                    </p>
                    <time
                        class="mt-1 block text-sm text-slate-600"
                        :datetime="event.pivot.occurred_at"
                        >{{
                            new Date(event.pivot.occurred_at).toLocaleString()
                        }}</time
                    >
                    <p
                        v-if="event.pivot.description"
                        class="mt-2 text-sm whitespace-pre-wrap"
                    >
                        {{ event.pivot.description }}
                    </p>
                </div>
            </li>
        </ol>
    </main>
</template>
