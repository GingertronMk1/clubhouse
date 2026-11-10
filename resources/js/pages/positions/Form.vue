<script setup lang="ts">
import FormInput from '@/components/FormInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import PositionField from './PositionField.vue';
import { index, store, update } from '@/routes/sports/positions';

const props = defineProps<{
    sport: { id: string; slug: string; name: string };
    otherPositions?: {
        id: string;
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    }[];
    position?: {
        id: string;
        slug: string;
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
const coordinateFields = [
    { key: 'preview_x', label: 'Preview X' },
    { key: 'preview_y', label: 'Preview Y' },
] as const;
const numberFields = [
    { key: 'sort_order', label: 'Sort order' },
    { key: 'default_number', label: 'Default number' },
] as const;
function submit() {
    if (props.position)
        form.put(update.url([props.sport.slug, props.position.slug]));
    else form.post(store.url(props.sport.slug));
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="position ? 'Edit position' : 'Add position'" />
        <Link
            :href="index.url(sport.slug)"
            class="text-sm underline"
            >Back to {{ sport.name }} positions</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ position ? 'Edit position' : 'Add position' }}
        </h1>
        <form
            class="mt-8 space-y-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="name"
                    class="block font-medium"
                    >Name</label
                >
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
                <label
                    for="description"
                    class="block font-medium"
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
            <div
                v-for="field in numberFields"
                :key="field.key"
            >
                <label
                    :for="field.key"
                    class="block font-medium"
                    >{{ field.label }}</label
                >
                <FormInput
                    :id="field.key"
                    v-model.number="form[field.key]"
                    type="number"
                    required
                    step="1"
                    min="-2147483648"
                    max="2147483647"
                />
                <p
                    v-if="form.errors[field.key]"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors[field.key] }}
                </p>
            </div>
            <section aria-labelledby="preview-heading">
                <h2
                    id="preview-heading"
                    class="text-xl font-semibold"
                >
                    Field preview
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    Move the sliders to position the highlighted marker.
                </p>
                <div
                    class="mt-4 grid items-start gap-5 sm:grid-cols-[minmax(0,1fr)_12rem]"
                >
                    <PositionField
                        :position="{
                            name: form.name || position?.name || 'New position',
                            preview_x: form.preview_x,
                            preview_y: form.preview_y,
                            default_number: form.default_number,
                        }"
                        :other-positions="otherPositions ?? []"
                    />
                    <div class="space-y-5">
                        <div
                            v-for="field in coordinateFields"
                            :key="field.key"
                        >
                            <label
                                :for="field.key"
                                class="block font-medium"
                                >{{ field.label }}: {{ form[field.key] }}</label
                            >
                            <FormInput
                                :id="field.key"
                                v-model.number="form[field.key]"
                                type="range"
                                min="0"
                                max="100"
                                step="1"
                            />
                            <p
                                v-if="form.errors[field.key]"
                                class="mt-1 text-sm text-red-700"
                            >
                                {{ form.errors[field.key] }}
                            </p>
                            <p
                                v-if="
                                    form[field.key] < 0 || form[field.key] > 100
                                "
                                class="mt-1 text-sm text-slate-600"
                            >
                                This saved value is outside the field. Move the
                                slider to choose a value from 0 to 100.
                            </p>
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-sm text-slate-600">
                    Highlighted:
                    {{ form.name || position?.name || 'New position' }} ({{
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
