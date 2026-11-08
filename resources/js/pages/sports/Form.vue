<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { index, store, update } from '@/routes/sports';

const props = defineProps<{
    sport?: {
        id: string;
        name: string;
        description: string;
        scoring: { name: string; value: number }[];
    };
}>();
const form = useForm({
    name: props.sport?.name ?? '',
    description: props.sport?.description ?? '',
    scoring:
        props.sport?.scoring.map(({ name, value }) => ({ name, value })) ?? [],
});
function submit() {
    if (props.sport) form.put(update.url(props.sport.id));
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
                <input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="255"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-700">
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <label for="description" class="block font-medium"
                    >Description</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="4"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <fieldset class="space-y-4">
                <legend class="font-medium">Scoring</legend>
                <p v-if="form.errors.scoring" class="text-sm text-red-700">
                    {{ form.errors.scoring }}
                </p>
                <div
                    v-for="(score, i) in form.scoring"
                    :key="i"
                    class="rounded border border-slate-200 p-4"
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                :for="`score-name-${i}`"
                                class="block text-sm"
                                >Name</label
                            ><input
                                :id="`score-name-${i}`"
                                v-model="score.name"
                                required
                                maxlength="255"
                                class="mt-1 w-full rounded border border-slate-300 p-2"
                            />
                            <p
                                v-if="form.errors[`scoring.${i}.name`]"
                                class="text-sm text-red-700"
                            >
                                {{ form.errors[`scoring.${i}.name`] }}
                            </p>
                        </div>
                        <div>
                            <label
                                :for="`score-value-${i}`"
                                class="block text-sm"
                                >Value</label
                            ><input
                                :id="`score-value-${i}`"
                                v-model.number="score.value"
                                type="number"
                                required
                                step="1"
                                class="mt-1 w-full rounded border border-slate-300 p-2"
                            />
                            <p
                                v-if="form.errors[`scoring.${i}.value`]"
                                class="text-sm text-red-700"
                            >
                                {{ form.errors[`scoring.${i}.value`] }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="mt-3 text-sm text-red-700 underline"
                        @click="form.scoring.splice(i, 1)"
                    >
                        Remove scoring entry
                    </button>
                </div>
                <button
                    type="button"
                    class="text-sm underline"
                    @click="form.scoring.push({ name: '', value: 0 })"
                >
                    Add scoring entry
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
