<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { index as sportsIndex } from '@/routes/sports';
import { create, destroy, edit } from '@/routes/sports/positions';

defineProps<{
    sport: { id: string; name: string };
    positions: { id: string; name: string; description: string; preview_x: number; preview_y: number; sort_order: number; default_number: number }[];
}>();

function deletePosition(sportId: string, positionId: string) {
    if (window.confirm('Delete this position?')) router.delete(destroy.url([sportId, positionId]));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="`${sport.name} positions`" />
        <Link :href="sportsIndex.url()" class="text-sm underline">Back to sports</Link>
        <div class="mb-8 mt-5 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ sport.name }} positions</h1>
            <Link :href="create.url(sport.id)" class="rounded bg-slate-900 px-4 py-2 text-white">Add position</Link>
        </div>
        <p v-if="positions.length === 0" class="text-slate-600">No positions yet. Add one to get started.</p>
        <ul v-else class="space-y-4">
            <li v-for="position in positions" :key="position.id" class="rounded-lg border border-slate-200 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ position.name }}</h2>
                        <p class="mt-2 whitespace-pre-wrap text-slate-600">{{ position.description }}</p>
                        <p class="mt-3 text-sm text-slate-700">Number: {{ position.default_number }} · Order: {{ position.sort_order }} · Preview: {{ position.preview_x }}, {{ position.preview_y }}</p>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <Link :href="edit.url([sport.id, position.id])" class="underline">Edit</Link>
                        <button type="button" class="text-red-700 underline" @click="deletePosition(sport.id, position.id)">Delete</button>
                    </div>
                </div>
            </li>
        </ul>
    </main>
</template>
