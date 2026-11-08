<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import { destroy, edit, index } from '@/routes/clubs/teams';

defineProps<{
    club: { id: string; name: string };
    team: {
        id: string;
        name: string;
        description: string;
    };
}>();
function deleteTeam(clubId: string, teamId: string) {
    if (window.confirm('Delete this team?'))
        router.delete(destroy.url([clubId, teamId]));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="team.name" />
        <Link :href="index.url(club.id)" class="text-sm underline"
            >Back to {{ club.name }} teams</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ team.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link :href="edit.url([club.id, team.id])" class="underline"
                    >Edit</Link
                >
                <button
                    type="button"
                    class="text-red-700 underline"
                    @click="deleteTeam(club.id, team.id)"
                >
                    Delete
                </button>
            </div>
        </div>
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ team.description }}
        </p>
    </main>
</template>
