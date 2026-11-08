<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
defineOptions({ layout: AppLayout });
type Option = { id: string; name: string };
type ScoringMethod = { name: string; value: number; count: number | null | '' };
type Sport = Option & { scoring: { name: string; value: number }[] };
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
        club1_score: ScoringMethod[] | null;
        club2_score: ScoringMethod[] | null;
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
    club1_score: props.record?.club1_score ?? [],
    club2_score: props.record?.club2_score ?? [],
});
const selectedSport = computed(() =>
    props.sports.find((sport) => sport.id === form.sport_id),
);
function scoringForSport(existing: ScoringMethod[] = []): ScoringMethod[] {
    return (selectedSport.value?.scoring ?? []).map((method) => ({
        ...method,
        count:
            existing.find(
                (item) =>
                    item.name === method.name && item.value === method.value,
            )?.count ?? null,
    }));
}
watch(
    () => form.sport_id,
    () => {
        form.club1_score = scoringForSport();
        form.club2_score = scoringForSport();
    },
);
if (selectedSport.value) {
    form.club1_score = scoringForSport(form.club1_score);
    form.club2_score = scoringForSport(form.club2_score);
}
function submit() {
    form.transform((data) => ({
        ...data,
        club1_score: data.club1_score.filter(
            (item) => item.count !== null && item.count !== '',
        ),
        club2_score: data.club2_score.filter(
            (item) => item.count !== null && item.count !== '',
        ),
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
            <fieldset
                v-for="side in ['club1_score', 'club2_score'] as const"
                :key="side"
                class="space-y-3"
            >
                <legend class="font-medium">
                    {{
                        side === 'club1_score'
                            ? 'First team scoring'
                            : 'Second team scoring'
                    }}
                </legend>
                <p v-if="!selectedSport" class="text-sm text-slate-600">
                    Select a sport to enter scoring.
                </p>
                <p
                    v-else-if="!selectedSport.scoring.length"
                    class="text-sm text-slate-600"
                >
                    This sport has no scoring methods.
                </p>
                <div
                    v-for="(method, index) in form[side]"
                    :key="method.name"
                    class="flex items-center gap-3"
                >
                    <label :for="`${side}-${index}`" class="flex-1"
                        >{{ method.name }} ({{ method.value }} points)</label
                    >
                    <input
                        :id="`${side}-${index}`"
                        v-model.number="method.count"
                        type="number"
                        min="0"
                        step="1"
                        class="w-24 rounded border border-slate-300 p-2"
                    />
                </div>
                <p
                    v-for="(error, key) in form.errors"
                    v-show="key.startsWith(side)"
                    :key="key"
                    role="alert"
                    class="text-sm text-red-700"
                >
                    {{ error }}
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
