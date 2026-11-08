<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { index, store, update } from '@/routes/sports/positions';

const props = defineProps<{
    sport: { id: string; name: string };
    otherPositions?: {
        id: string;
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    }[];
    position?: {
        id: string;
        name: string;
        description: string;
        preview_x: number;
        preview_y: number;
        sort_order: number;
        default_number: number;
    };
}>();
const form = useForm({
    name: props.position?.name ?? '',
    description: props.position?.description ?? '',
    preview_x: props.position?.preview_x ?? 0,
    preview_y: props.position?.preview_y ?? 0,
    sort_order: props.position?.sort_order ?? 0,
    default_number: props.position?.default_number ?? 0,
});
const numberFields = [
    { key: 'preview_x', label: 'Preview X' },
    { key: 'preview_y', label: 'Preview Y' },
    { key: 'sort_order', label: 'Sort order' },
    { key: 'default_number', label: 'Default number' },
] as const;
function fieldCoordinate(value: number): string {
    return `${Math.max(0, Math.min(100, Number(value) || 0))}%`;
}
function submit() {
    if (props.position)
        form.put(update.url([props.sport.id, props.position.id]));
    else form.post(store.url(props.sport.id));
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="position ? 'Edit position' : 'Add position'" />
        <Link :href="index.url(sport.id)" class="text-sm underline"
            >Back to {{ sport.name }} positions</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ position ? 'Edit position' : 'Add position' }}
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
            <div v-for="field in numberFields" :key="field.key">
                <label :for="field.key" class="block font-medium">{{
                    field.label
                }}</label>
                <input
                    :id="field.key"
                    v-model.number="form[field.key]"
                    type="number"
                    required
                    step="1"
                    min="-2147483648"
                    max="2147483647"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors[field.key]"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors[field.key] }}
                </p>
            </div>
            <section v-if="position" aria-labelledby="preview-heading">
                <h2 id="preview-heading" class="text-xl font-semibold">
                    Field preview
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    The highlighted marker moves as you change Preview X and
                    Preview Y. Coordinates outside 0–100 appear at the edge of
                    the field.
                </p>
                <div
                    class="relative mt-4 aspect-square w-full overflow-hidden rounded-lg border-4 border-emerald-800 bg-emerald-700"
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
                        <div
                            v-for="other in otherPositions"
                            :key="other.id"
                            :style="{
                                left: fieldCoordinate(other.preview_x),
                                top: fieldCoordinate(other.preview_y),
                            }"
                            :title="other.name"
                            :aria-label="`${other.name}, number ${other.default_number}`"
                            class="absolute flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-slate-900 text-xs font-semibold text-white shadow"
                        >
                            {{ other.default_number }}
                        </div>
                        <div
                            :style="{
                                left: fieldCoordinate(form.preview_x),
                                top: fieldCoordinate(form.preview_y),
                            }"
                            :aria-label="`Current position: ${form.name || position.name}, number ${form.default_number}`"
                            class="absolute z-10 flex h-9 w-9 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-amber-500 text-xs font-bold text-slate-950 shadow ring-2 ring-amber-200"
                        >
                            {{ form.default_number }}
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-sm text-slate-600">
                    Highlighted: {{ form.name || position.name }} ({{
                        form.default_number
                    }})
                </p>
            </section>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                Save position
            </button>
        </form>
    </main>
</template>
