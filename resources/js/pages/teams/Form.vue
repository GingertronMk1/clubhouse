<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as teams from '@/routes/clubs/teams';

const props = defineProps<{
    club: { id: string; name: string };
    team?: {
        id: string;
        name: string;
        description: string;
    };
}>();
const routes = teams;
const label = 'team';
const form = useForm({
    name: props.team?.name ?? '',
    description: props.team?.description ?? '',
});
function submit() {
    if (props.team) form.put(routes.update.url([props.club.id, props.team.id]));
    else form.post(routes.store.url(props.club.id));
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${team ? 'Edit' : 'Add'} ${label}`" />
        <Link
            :href="routes.index(club.id)"
            class="text-sm underline"
            >Back to {{ club.name }} teams</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ team ? 'Edit' : 'Add' }} {{ label }}
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
                    role="alert"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Saving…' : `Save ${label}` }}
            </button>
        </form>
    </main>
</template>
