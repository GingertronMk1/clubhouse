<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import { destroy, edit, index } from '@/routes/sports';
import {
    index as positionsIndex,
    show as positionShow,
} from '@/routes/sports/positions';

defineProps<{
    sport: {
        id: string;
        slug: string;
        name: string;
        description: string;
        events: { name: string; description: string; score: number }[];
    };
    positions: {
        id: string;
        slug: string;
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    }[];
}>();
function fieldCoordinate(value: number): string {
    return `${Math.max(0, Math.min(100, value))}%`;
}
function deleteSport(id: string) {
    if (window.confirm('Delete this sport?')) router.delete(destroy.url(id));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="sport.name" />
        <Link :href="index.url()" class="text-sm underline"
            >Back to sports</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ sport.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link :href="edit.url(sport.slug)" class="underline">Edit</Link>
                <button
                    type="button"
                    class="text-red-700 underline"
                    @click="deleteSport(sport.slug)"
                >
                    Delete
                </button>
            </div>
        </div>
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ sport.description }}
        </p>
        <section v-if="sport.events.length" class="mt-6">
            <h2 class="text-xl font-semibold">Events</h2>
            <ul class="mt-2 text-slate-700">
                <li v-for="(score, i) in sport.events" :key="i">
                    {{ score.name }}: {{ score.score }}
                    <span v-if="score.description">
                        — {{ score.description }}</span
                    >
                </li>
            </ul>
        </section>
        <section class="mt-8" aria-labelledby="positions-heading">
            <div class="flex items-center justify-between gap-4">
                <h2 id="positions-heading" class="text-xl font-semibold">
                    Positions
                </h2>
                <Link
                    :href="positionsIndex.url(sport.slug)"
                    class="text-sm underline"
                    >Manage positions</Link
                >
            </div>
            <p v-if="positions.length === 0" class="mt-3 text-slate-600">
                No positions yet.
            </p>
            <template v-else>
                <div
                    class="relative mt-4 aspect-square w-full max-w-2xl overflow-hidden rounded-lg border-4 border-emerald-800 bg-emerald-700"
                    aria-label="Position preview field"
                >
                    <div
                        class="pointer-events-none absolute inset-4 border-2 border-white/70"
                        aria-hidden="true"
                    >
                        <div
                            class="absolute top-1/2 w-full border-t-2 border-white/70"
                        ></div>
                        <div
                            class="absolute top-1/2 left-1/2 h-1/4 w-1/4 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white/70"
                        ></div>
                    </div>
                    <div class="absolute inset-5">
                        <Link
                            v-for="position in positions"
                            :key="position.id"
                            :href="
                                positionShow.url([sport.slug, position.slug])
                            "
                            :style="{
                                left: fieldCoordinate(position.preview_x),
                                top: fieldCoordinate(position.preview_y),
                            }"
                            :aria-label="`${position.name}, number ${position.default_number}`"
                            class="group absolute flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-slate-900 text-xs font-semibold text-white shadow hover:z-10 focus:z-10 focus:outline-2 focus:outline-offset-2 focus:outline-white"
                        >
                            {{ position.default_number }}
                            <span
                                aria-hidden="true"
                                :class="[
                                    position.preview_x > 50
                                        ? 'right-0'
                                        : 'left-0',
                                    position.preview_y < 20
                                        ? 'top-full mt-2'
                                        : 'bottom-full mb-2',
                                ]"
                                class="pointer-events-none absolute hidden w-max max-w-48 rounded bg-slate-900 px-2 py-1 text-left text-xs font-medium text-white shadow group-hover:block group-focus:block"
                                >{{ position.name }}</span
                            >
                        </Link>
                    </div>
                </div>
                <ul class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                    <li v-for="position in positions" :key="position.id">
                        <Link
                            :href="
                                positionShow.url([sport.slug, position.slug])
                            "
                            class="underline"
                            >{{ position.default_number }} —
                            {{ position.name }}</Link
                        >
                    </li>
                </ul>
            </template>
        </section>
    </main>
</template>
