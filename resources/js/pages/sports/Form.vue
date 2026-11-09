<script setup lang="ts">
import FormInput from '@/components/FormInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import { index, store, update } from '@/routes/sports';

const props = defineProps<{
    sport?: {
        id: string;
        slug: string;
        name: string;
        description: string;
        events: { name: string; description: string; score: number; is_team_event: boolean }[];
    };
}>();
const form = useForm({
    name: props.sport?.name ?? '',
    description: props.sport?.description ?? '',
    events:
        props.sport?.events.map(({ name, description, score, is_team_event }) => ({ name, description, score, is_team_event })) ?? [],
});
function submit() {
    if (props.sport) form.put(update.url(props.sport.slug));
    else form.post(store.url());
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="sport ? 'Edit sport' : 'Add sport'" />
        <Link :href="index.url()" class="text-sm underline"
            >Back to sports</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ sport ? 'Edit sport' : 'Add sport' }}
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block font-medium">Name</label>
                <FormInput
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="255"
                />
                <p
                    v-if="form.errors.name"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <label for="description" class="block font-medium"
                    >Description</label
                >
                <FormInput
                    textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="4"
                />
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <fieldset class="space-y-4">
                <legend class="font-medium">Events</legend>
                <p v-if="form.errors.events" class="text-sm text-red-700">
                    {{ form.errors.events }}
                </p>
                <div
                    v-for="(score, i) in form.events"
                    :key="i"
                    class="rounded border border-slate-200 p-4"
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                :for="`score-name-${i}`"
                                class="block text-sm"
                                >Name</label
                            ><FormInput
                                :id="`score-name-${i}`"
                                v-model="score.name"
                                required
                                maxlength="255"
                            />
                            <p
                                v-if="form.errors[`events.${i}.name`]"
                                class="text-sm text-red-700"
                            >
                                {{ form.errors[`events.${i}.name`] }}
                            </p>
                        </div>
                        <div>
                            <label
                                :for="`score-score-${i}`"
                                class="block text-sm"
                                >Score</label
                            ><FormInput
                                :id="`score-score-${i}`"
                                v-model.number="score.score"
                                type="number"
                                required
                                step="1"
                            />
                            <p
                                v-if="form.errors[`events.${i}.score`]"
                                class="text-sm text-red-700"
                            >
                                {{ form.errors[`events.${i}.score`] }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label :for="`event-description-${i}`" class="block text-sm">Description</label>
                        <FormInput :id="`event-description-${i}`" v-model="score.description" textarea rows="2" />
                        <p v-if="form.errors[`events.${i}.description`]" class="text-sm text-red-700">{{ form.errors[`events.${i}.description`] }}</p>
                    </div>
                    <div class="mt-4">
                        <label :for="`event-scope-${i}`" class="block text-sm">Applies to</label>
                        <select :id="`event-scope-${i}`" v-model="score.is_team_event" class="mt-1 rounded border border-slate-300 p-2">
                            <option :value="true">Whole team</option>
                            <option :value="false">Individual</option>
                        </select>
                        <p v-if="form.errors[`events.${i}.is_team_event`]" class="text-sm text-red-700">{{ form.errors[`events.${i}.is_team_event`] }}</p>
                    </div>
                    <button
                        type="button"
                        class="mt-3 text-sm text-red-700 underline"
                        @click="form.events.splice(i, 1)"
                    >
                        Remove events entry
                    </button>
                </div>
                <button
                    type="button"
                    class="text-sm underline"
                    @click="form.events.push({ name: '', description: '', score: 0, is_team_event: true })"
                >
                    Add events entry
                </button>
            </fieldset>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                Save sport
            </button>
        </form>
    </main>
</template>
