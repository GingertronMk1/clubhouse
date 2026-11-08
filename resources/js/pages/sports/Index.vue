<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { create, destroy, edit } from '@/routes/sports';

defineProps<{
    sports: {
        id: string;
        name: string;
        description: string;
        scoring: { id: string; name: string; value: number }[];
    }[];
}>();

function deleteSport(id: string) {
    if (window.confirm('Delete this sport?')) router.delete(destroy.url(id));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Sports" />
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-semibold">Sports</h1>
            <Link
                :href="create.url()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add sport</Link
            >
        </div>
        <p v-if="sports.length === 0" class="text-slate-600">
            No sports yet. Add one to get started.
        </p>
        <ul v-else class="space-y-4">
            <li
                v-for="sport in sports"
                :key="sport.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ sport.name }}</h2>
                        <p class="mt-2 whitespace-pre-wrap text-slate-600">
                            {{ sport.description }}
                        </p>
                        <ul
                            v-if="sport.scoring.length"
                            class="mt-3 text-sm text-slate-700"
                        >
                            <li v-for="score in sport.scoring" :key="score.id">
                                {{ score.name }}: {{ score.value }}
                            </li>
                        </ul>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <Link :href="edit.url(sport.id)" class="underline"
                            >Edit</Link
                        >
                        <button
                            type="button"
                            class="text-red-700 underline"
                            @click="deleteSport(sport.id)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </li>
        </ul>
    </main>
</template>
