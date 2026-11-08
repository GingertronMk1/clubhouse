<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
defineOptions({ layout: AppLayout });
type Option = { id: string; name: string };
type Team = Option & { club: { name: string } | null };
const props = defineProps<{
    record?: {
        id: string;
        name: string;
        description: string;
        summary: string;
        sport_id: string;
        competition_id: string;
        team1_id: string;
        team2_id: string;
        location_id: string;
    };
    start?: string | null;
    sports: Option[];
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
    team1_id: props.record?.team1_id ?? '',
    team2_id: props.record?.team2_id ?? '',
    location_id: props.record?.location_id ?? '',
});
function submit() {
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
                <label for="team1_id" class="block font-medium"
                    >First team</label
                ><select
                    id="team1_id"
                    v-model="form.team1_id"
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
                    v-if="form.errors.team1_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.team1_id }}
                </p>
            </div>
            <div>
                <label for="team2_id" class="block font-medium"
                    >Second team</label
                ><select
                    id="team2_id"
                    v-model="form.team2_id"
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
                    v-if="form.errors.team2_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.team2_id }}
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
