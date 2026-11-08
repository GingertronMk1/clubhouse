<script setup lang="ts">
import NameDescriptionFields from '@/components/NameDescriptionFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as teams from '@/routes/clubs/teams';

const props = defineProps<{
    club: { id: string; slug: string; name: string };
    team?: {
        id: string;
        slug: string;
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
    if (props.team)
        form.put(routes.update.url([props.club.slug, props.team.slug]));
    else form.post(routes.store.url(props.club.slug));
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${team ? 'Edit' : 'Add'} ${label}`" />
        <Link :href="routes.index(club.slug)" class="text-sm underline"
            >Back to {{ club.name }} teams</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ team ? 'Edit' : 'Add' }} {{ label }}
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <NameDescriptionFields
                v-model:name="form.name"
                v-model:description="form.description"
                :errors="form.errors"
            />
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
