<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import RadioOption from '@/components/RadioOption.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
import * as competitions from '@/routes/competitions';
import { store as storeEvent } from '@/routes/games/events';
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
        game_user_id: string;
        name: string;
        team: number;
        number: number | null;
        position: { name: string; preview_x: number; preview_y: number } | null;
    }[];
    sportEvents: (Named & { score: number })[];
    score: { team1: number; team2: number };
    events: {
        id: string;
        name: string;
        occurred_at: string;
        description: string | null;
        competitor: string | null;
    }[];
}>();
function currentLocalDateTime(): string {
    const now = new Date();
    const pad = (value: number) => String(value).padStart(2, '0');

    return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
}
const eventForm = useForm({
    sport_event_id: '',
    competitor_type: 'player',
    competitor_id: '',
    occurred_at: currentLocalDateTime(),
    description: '',
});
function submitEvent() {
    eventForm.post(storeEvent.url(props.record.id), {
        preserveScroll: true,
        onSuccess: () => {
            eventForm.reset();
            eventForm.occurred_at = currentLocalDateTime();
        },
    });
}

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
                <Link
                    :href="routes.edit(record.id)"
                    class="underline"
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
        <p
            v-if="record.team1 && record.team2"
            class="mt-5 text-xl font-medium"
        >
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
        <p
            v-if="record.competition"
            class="mt-2 text-slate-600"
        >
            Competition:
            <Link
                :href="competitions.show(record.competition.id)"
                class="underline"
                >{{ record.competition.name }}</Link
            >
        </p>
        <section
            class="mt-8"
            aria-labelledby="lineups-heading"
        >
            <h2
                id="lineups-heading"
                class="text-xl font-semibold"
            >
                Lineups
            </h2>
            <div class="mt-4 grid gap-8 md:grid-cols-2">
                <section
                    v-for="side in [1, 2]"
                    :key="side"
                >
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
                            <div
                                class="absolute inset-5"
                                aria-hidden="true"
                            >
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
        <hr class="my-2" />
        <section
            class="club-panel mt-8 rounded-sm p-5 sm:p-7"
            aria-labelledby="add-event-heading"
        >
            <p class="club-eyebrow">Match centre</p>
            <h2 class="mt-1 text-2xl font-black tracking-tight">
                Score<span class="text-club-orange">.</span>
            </h2>
            <p
                class="mt-4 rounded-sm bg-club-ink px-5 py-6 text-center text-lg font-semibold text-white sm:text-xl"
                aria-label="Game score"
            >
                {{ record.team1?.club?.name }} {{ record.team1?.name }}
                <strong
                    class="mx-3 inline-block text-3xl font-black text-orange-300"
                    >{{ score.team1 }} – {{ score.team2 }}</strong
                >
                {{ record.team2?.club?.name }} {{ record.team2?.name }}
            </p>
            <ul
                v-if="events.length"
                class="mt-4 space-y-2 text-sm text-slate-700"
            >
                <li
                    v-for="event in events"
                    :key="event.id"
                >
                    {{ new Date(event.occurred_at).toLocaleString() }} ·
                    {{ event.name }} ·
                    {{ event.competitor ?? 'Former competitor' }}
                    <p
                        v-if="event.description"
                        class="whitespace-pre-wrap"
                    >
                        {{ event.description }}
                    </p>
                </li>
            </ul>
            <hr class="my-2" />
            <h2
                id="add-event-heading"
                class="text-xl font-semibold"
            >
                Add event
            </h2>
            <p
                v-if="sportEvents.length === 0"
                class="mt-3 text-slate-600"
            >
                No events are defined for this sport.
            </p>
            <form
                v-else
                class="mt-4 grid gap-4"
                @submit.prevent="submitEvent"
            >
                <fieldset
                    :aria-describedby="
                        eventForm.errors.sport_event_id
                            ? 'event-error'
                            : undefined
                    "
                >
                    <legend class="text-sm font-medium">Event</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <RadioOption
                            v-for="event in sportEvents"
                            :key="event.id"
                            v-model="eventForm.sport_event_id"
                            name="sport_event_id"
                            :value="event.id"
                            required
                        >
                            {{ event.name }} ({{ event.score }})
                        </RadioOption>
                    </div>
                    <p
                        v-if="eventForm.errors.sport_event_id"
                        id="event-error"
                        class="mt-1 text-sm text-red-700"
                        role="alert"
                    >
                        {{ eventForm.errors.sport_event_id }}
                    </p>
                </fieldset>
                <fieldset
                    :aria-describedby="
                        eventForm.errors.competitor_type
                            ? 'competitor-type-error'
                            : undefined
                    "
                >
                    <legend class="text-sm font-medium">Competitor type</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <RadioOption
                            v-model="eventForm.competitor_type"
                            name="competitor_type"
                            value="team"
                            @update:model-value="eventForm.competitor_id = ''"
                            >Team</RadioOption
                        >
                        <RadioOption
                            v-model="eventForm.competitor_type"
                            name="competitor_type"
                            value="player"
                            @update:model-value="eventForm.competitor_id = ''"
                            >Player</RadioOption
                        >
                    </div>
                    <p
                        v-if="eventForm.errors.competitor_type"
                        id="competitor-type-error"
                        class="mt-1 text-sm text-red-700"
                        role="alert"
                    >
                        {{ eventForm.errors.competitor_type }}
                    </p>
                </fieldset>
                <fieldset
                    :aria-describedby="
                        eventForm.errors.competitor_id
                            ? 'competitor-error'
                            : undefined
                    "
                >
                    <legend class="text-sm font-medium">Competitor</legend>
                    <div
                        v-if="eventForm.competitor_type === 'team'"
                        class="mt-2 flex flex-wrap gap-2"
                    >
                        <RadioOption
                            v-for="team in [record.team1, record.team2].filter(
                                (item): item is NonNullable<typeof item> =>
                                    item !== null,
                            )"
                            :key="team.id"
                            v-model="eventForm.competitor_id"
                            name="competitor_id"
                            :value="team.id"
                            required
                        >
                            {{ team.club?.name }} {{ team.name }}
                        </RadioOption>
                    </div>
                    <template v-else>
                        <p
                            v-if="!lineups.length"
                            class="mt-2 text-sm text-slate-600"
                        >
                            No players selected for this game.
                        </p>
                        <div
                            v-for="side in [1, 2]"
                            :key="side"
                            class="mt-3"
                        >
                            <p class="text-sm text-slate-600">
                                {{
                                    (side === 1 ? record.team1 : record.team2)
                                        ?.club?.name
                                }}
                                {{
                                    (side === 1 ? record.team1 : record.team2)
                                        ?.name
                                }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <RadioOption
                                    v-for="player in lineups.filter(
                                        (item) => item.team === side,
                                    )"
                                    :key="player.game_user_id"
                                    v-model="eventForm.competitor_id"
                                    name="competitor_id"
                                    :value="player.game_user_id"
                                    required
                                >
                                    {{ player.number ?? 'No number' }} -
                                    {{ player.name }} -
                                    {{ player.position?.name ?? 'No position' }}
                                </RadioOption>
                            </div>
                        </div>
                    </template>
                    <p
                        v-if="eventForm.errors.competitor_id"
                        id="competitor-error"
                        class="mt-1 text-sm text-red-700"
                        role="alert"
                    >
                        {{ eventForm.errors.competitor_id }}
                    </p>
                </fieldset>
                <label class="grid gap-1 text-sm font-medium"
                    >Occurred at
                    <input
                        v-model="eventForm.occurred_at"
                        type="datetime-local"
                        step="1"
                        required
                        class="rounded border border-slate-300 p-2"
                    />
                    <span
                        v-if="eventForm.errors.occurred_at"
                        class="text-red-700"
                        >{{ eventForm.errors.occurred_at }}</span
                    >
                </label>
                <label class="grid gap-1 text-sm font-medium"
                    >Description (optional)
                    <textarea
                        v-model="eventForm.description"
                        rows="3"
                        class="rounded border border-slate-300 p-2"
                    ></textarea>
                    <span
                        v-if="eventForm.errors.description"
                        class="text-red-700"
                        >{{ eventForm.errors.description }}</span
                    >
                </label>
                <button
                    type="submit"
                    :disabled="eventForm.processing"
                    class="w-fit rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                >
                    Add event
                </button>
            </form>
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
