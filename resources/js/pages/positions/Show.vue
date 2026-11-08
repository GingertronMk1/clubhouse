<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import PositionField from './PositionField.vue';
import { destroy, edit, index } from '@/routes/sports/positions';

defineProps<{
    sport: { id: string; slug: string; name: string };
    otherPositions: {
        id: string;
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    }[];
    position: {
        id: string;
        name: string;
        description: string;
        preview_x: number;
        preview_y: number;
        sort_order: number;
        default_number: number;
    };
}>();
function deletePosition(sportId: string, positionId: string) {
    if (window.confirm('Delete this position?'))
        router.delete(destroy.url([sportId, positionId]));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="position.name" />
        <Link :href="index.url(sport.slug)" class="text-sm underline"
            >Back to {{ sport.name }} positions</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ position.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link
                    :href="edit.url([sport.slug, position.id])"
                    class="underline"
                    >Edit</Link
                >
                <button
                    type="button"
                    class="text-red-700 underline"
                    @click="deletePosition(sport.slug, position.id)"
                >
                    Delete
                </button>
            </div>
        </div>
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ position.description }}
        </p>
        <dl class="mt-6 space-y-2 text-slate-700">
            <div>
                <dt class="inline font-medium">Default number:</dt>
                <dd class="inline">{{ position.default_number }}</dd>
            </div>
            <div>
                <dt class="inline font-medium">Sort order:</dt>
                <dd class="inline">{{ position.sort_order }}</dd>
            </div>
            <div>
                <dt class="inline font-medium">Preview coordinates:</dt>
                <dd class="inline">
                    {{ position.preview_x }}, {{ position.preview_y }}
                </dd>
            </div>
        </dl>
        <section class="mt-8 max-w-2xl" aria-labelledby="preview-heading">
            <h2 id="preview-heading" class="text-xl font-semibold">
                Field preview
            </h2>
            <PositionField
                class="mt-4"
                :position="position"
                :other-positions="otherPositions"
            />
            <p class="mt-2 text-sm text-slate-600">
                Highlighted: {{ position.name }} ({{ position.default_number }})
            </p>
        </section>
    </main>
</template>
