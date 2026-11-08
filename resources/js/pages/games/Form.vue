<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
defineOptions({ layout: AppLayout });
type Option = { id: string; name: string };
type ScoringType = {
    id: string;
    name: string;
    value: number;
    deleted_at: string | null;
};
type Sport = Option & { scoring_events: ScoringType[] };
type ScoringEntry = {
    id: string | null;
    scoring_event_id: string;
    team_id: string | null;
    timestamp: number | '' | null;
};
type Team = Option & { club: { name: string } | null };
const props = defineProps<{
    record?: {
        id: string;
        name: string;
        description: string;
        summary: string;
        sport_id: string;
        competition_id: string;
        club1_id: string;
        club2_id: string;
        location_id: string;
        scoring_events: ScoringEntry[];
    };
    start?: string | null;
    sports: Sport[];
    competitions: Option[];
    teams: Team[];
    locations: Option[];
}>();
const form = useForm({
    name: props.record?.name ?? '',
    start: props.start ?? '',
    description: props.record?.description ?? '',
    summary: props.record?.summary ?? '',
    sport_id: props.record?.sport_id ?? '',
    competition_id: props.record?.competition_id ?? '',
    club1_id: props.record?.club1_id ?? '',
    club2_id: props.record?.club2_id ?? '',
    location_id: props.record?.location_id ?? '',
    scoring_events: (props.record?.scoring_events ?? []).map(
        ({ id, scoring_event_id, team_id, timestamp }) => ({
            id: id as string | null,
            scoring_event_id,
            team_id,
            timestamp: timestamp as number | '' | null,
        }),
    ),
});
const selectedSport = computed(() =>
    props.sports.find((sport) => sport.id === form.sport_id),
);
const selectedTeams = computed(() =>
    props.teams.filter((team) =>
        [form.club1_id, form.club2_id].includes(team.id),
    ),
);
function addScoringEvent() {
    form.scoring_events.push({
        id: null,
        scoring_event_id: '',
        team_id: '',
        timestamp: null,
    });
}
function submit() {
    form.transform((data) => ({
        ...data,
        scoring_events: data.scoring_events.map((event) => ({
            ...event,
            timestamp: event.timestamp === '' ? null : event.timestamp,
        })),
    }));
    if (props.record) form.put(routes.update.url(props.record.id));
    else form.post(routes.store.url());
}
</script>
<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${record ? 'Edit' : 'Add'} game`" /><Link
            :href="record ? routes.show(record.id) : routes.index()"
            class="text-sm underline"
            >Back to {{ record ? 'game' : 'games' }}</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ record ? 'Edit' : 'Add' }} game
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block font-medium">Name</label
                ><input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="255"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.name"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <label for="start" class="block font-medium">Start</label
                ><input
                    id="start"
                    v-model="form.start"
                    type="datetime-local"
                    step="1"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.start"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.start }}
                </p>
            </div>
            <div>
                <label for="description" class="block font-medium"
                    >Description</label
                ><textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="3"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.description"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <div>
                <label for="summary" class="block font-medium">Summary</label
                ><textarea
                    id="summary"
                    v-model="form.summary"
                    required
                    rows="3"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.summary"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.summary }}
                </p>
            </div>
            <div>
                <label for="sport_id" class="block font-medium">Sport</label
                ><select
                    id="sport_id"
                    v-model="form.sport_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a sport</option>
                    <option
                        v-for="item in sports"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.sport_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.sport_id }}
                </p>
            </div>
            <div>
                <label for="competition_id" class="block font-medium"
                    >Competition</label
                ><select
                    id="competition_id"
                    v-model="form.competition_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a competition</option>
                    <option
                        v-for="item in competitions"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.competition_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.competition_id }}
                </p>
            </div>
            <div>
                <label for="club1_id" class="block font-medium"
                    >First team</label
                ><select
                    id="club1_id"
                    v-model="form.club1_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a team</option>
                    <option
                        v-for="item in teams"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.club?.name }} — {{ item.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.club1_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.club1_id }}
                </p>
            </div>
            <div>
                <label for="club2_id" class="block font-medium"
                    >Second team</label
                ><select
                    id="club2_id"
                    v-model="form.club2_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a team</option>
                    <option
                        v-for="item in teams"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.club?.name }} — {{ item.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.club2_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.club2_id }}
                </p>
            </div>
            <div>
                <label for="location_id" class="block font-medium"
                    >Location</label
                ><select
                    id="location_id"
                    v-model="form.location_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a location</option>
                    <option
                        v-for="item in locations"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.location_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.location_id }}
                </p>
            </div>
            <fieldset class="space-y-4 rounded-lg border border-slate-200 p-4">
                <legend class="px-1 font-medium">Scoring events</legend>
                <p class="text-sm text-slate-600">
                    Record each scoring event for the team that scored. Time is
                    optional and measured in seconds from the start of the game.
                </p>
                <p
                    v-if="!form.scoring_events.length"
                    class="text-sm text-slate-600"
                >
                    No scoring events recorded.
                </p>
                <div
                    v-for="(event, index) in form.scoring_events"
                    :key="event.id ?? `new-${index}`"
                    class="space-y-3 rounded border border-slate-200 p-4"
                >
                    <div>
                        <label
                            :for="`scoring-type-${index}`"
                            class="block font-medium"
                            >Scoring type</label
                        >
                        <select
                            :id="`scoring-type-${index}`"
                            v-model="event.scoring_event_id"
                            required
                            class="mt-1 w-full rounded border border-slate-300 p-2"
                        >
                            <option value="" disabled>
                                Select a scoring type
                            </option>
                            <option
                                v-for="item in (
                                    selectedSport?.scoring_events ?? []
                                ).filter(
                                    (item) =>
                                        !item.deleted_at ||
                                        item.id === event.scoring_event_id,
                                )"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }} ({{ item.value }} points){{
                                    item.deleted_at ? ' — archived' : ''
                                }}
                            </option>
                        </select>
                        <p
                            v-if="
                                form.errors[
                                    `scoring_events.${index}.scoring_event_id`
                                ]
                            "
                            role="alert"
                            class="mt-1 text-sm text-red-700"
                        >
                            {{
                                form.errors[
                                    `scoring_events.${index}.scoring_event_id`
                                ]
                            }}
                        </p>
                    </div>
                    <div>
                        <label
                            :for="`scoring-team-${index}`"
                            class="block font-medium"
                            >Team</label
                        >
                        <select
                            :id="`scoring-team-${index}`"
                            v-model="event.team_id"
                            :required="!event.id"
                            class="mt-1 w-full rounded border border-slate-300 p-2"
                        >
                            <option :value="null" disabled>Not recorded</option>
                            <option value="" disabled>Select a team</option>
                            <option
                                v-for="team in selectedTeams"
                                :key="team.id"
                                :value="team.id"
                            >
                                {{ team.club?.name }} — {{ team.name }}
                            </option>
                        </select>
                        <p
                            v-if="
                                form.errors[`scoring_events.${index}.team_id`]
                            "
                            role="alert"
                            class="mt-1 text-sm text-red-700"
                        >
                            {{ form.errors[`scoring_events.${index}.team_id`] }}
                        </p>
                    </div>
                    <div>
                        <label
                            :for="`scoring-time-${index}`"
                            class="block font-medium"
                            >Elapsed seconds (optional)</label
                        >
                        <input
                            :id="`scoring-time-${index}`"
                            v-model.number="event.timestamp"
                            type="number"
                            min="0"
                            max="4294967295"
                            step="1"
                            class="mt-1 w-full rounded border border-slate-300 p-2"
                        />
                        <p
                            v-if="
                                form.errors[`scoring_events.${index}.timestamp`]
                            "
                            role="alert"
                            class="mt-1 text-sm text-red-700"
                        >
                            {{
                                form.errors[`scoring_events.${index}.timestamp`]
                            }}
                        </p>
                    </div>
                    <p
                        v-if="form.errors[`scoring_events.${index}.id`]"
                        role="alert"
                        class="text-sm text-red-700"
                    >
                        {{ form.errors[`scoring_events.${index}.id`] }}
                    </p>
                    <button
                        type="button"
                        class="text-sm text-red-700 underline"
                        @click="form.scoring_events.splice(index, 1)"
                    >
                        Remove scoring event
                    </button>
                </div>
                <p
                    v-if="form.errors.scoring_events"
                    role="alert"
                    class="text-sm text-red-700"
                >
                    {{ form.errors.scoring_events }}
                </p>
                <button
                    type="button"
                    :disabled="
                        !selectedSport ||
                        selectedTeams.length < 2 ||
                        !selectedSport.scoring_events.some(
                            (item) => !item.deleted_at,
                        )
                    "
                    class="rounded border border-slate-300 px-3 py-2 text-sm disabled:opacity-50"
                    @click="addScoringEvent"
                >
                    Add scoring event
                </button>
                <p
                    v-if="
                        selectedSport &&
                        !selectedSport.scoring_events.some(
                            (item) => !item.deleted_at,
                        )
                    "
                    class="text-sm text-slate-600"
                >
                    Add scoring types to this sport before recording new events.
                </p>
            </fieldset>
            <p
                v-if="
                    !sports.length ||
                    !competitions.length ||
                    teams.length < 2 ||
                    !locations.length
                "
                class="text-sm text-slate-600"
            >
                A sport, competition, location and two teams are needed before
                adding a game.
            </p>
            <button
                type="submit"
                :disabled="
                    form.processing ||
                    !sports.length ||
                    !competitions.length ||
                    teams.length < 2 ||
                    !locations.length
                "
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Saving…' : 'Save game' }}
            </button>
        </form>
    </main>
</template>
