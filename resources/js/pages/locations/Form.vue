<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as locations from '@/routes/locations';

const props = defineProps<{
    record?: {
        id: string;
        name: string;
        description: string;
        additional_information?: unknown;
    };
}>();
const routes = locations;
const label = 'location';
const form = useForm({
    name: props.record?.name ?? '',
    description: props.record?.description ?? '',
    additional_information: JSON.stringify(
        props.record?.additional_information ?? {},
        null,
        2,
    ),
});
function submit() {
    if (props.record) form.put(routes.update.url(props.record.id));
    else form.post(routes.store.url());
}
</script>

<template>
    <div
        class="min-h-screen bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100"
    >
        <Head :title="`${record ? 'Edit' : 'Add'} ${label}`" />
        <main class="mx-auto max-w-2xl px-6 py-12">
            <Link
                :href="record ? routes.show(record.id) : routes.index()"
                class="text-sm underline"
                >Back to {{ record ? label : 'locations' }}</Link
            >
            <h1 class="mt-6 text-3xl font-semibold">
                {{ record ? 'Edit' : 'Add' }} {{ label }}
            </h1>
            <form class="mt-8 flex flex-col gap-6" @submit.prevent="submit">
                <div>
                    <label for="name" class="mb-2 block font-medium">Name</label
                    ><input
                        id="name"
                        v-model="form.name"
                        required
                        maxlength="255"
                        class="w-full rounded border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900"
                    />
                    <p
                        v-if="form.errors.name"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
                <div>
                    <label for="description" class="mb-2 block font-medium"
                        >Description</label
                    ><textarea
                        id="description"
                        v-model="form.description"
                        required
                        rows="4"
                        class="w-full rounded border border-stone-300 bg-white px-3 py-2 dark:border-stone-700 dark:bg-stone-900"
                    />
                    <p
                        v-if="form.errors.description"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>
                <div>
                    <label
                        for="additional_information"
                        class="mb-2 block font-medium"
                        >Additional information (JSON)</label
                    ><textarea
                        id="additional_information"
                        v-model="form.additional_information"
                        required
                        rows="6"
                        spellcheck="false"
                        class="w-full rounded border border-stone-300 bg-white px-3 py-2 font-mono text-sm dark:border-stone-700 dark:bg-stone-900"
                    />
                    <p
                        v-if="form.errors.additional_information"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.additional_information }}
                    </p>
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="self-start rounded bg-stone-900 px-5 py-2 font-medium text-white disabled:opacity-50 dark:bg-stone-100 dark:text-stone-900"
                >
                    {{ form.processing ? 'Saving…' : 'Save' }}
                </button>
            </form>
        </main>
    </div>
</template>
