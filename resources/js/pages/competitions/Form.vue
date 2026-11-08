<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as routes from '@/routes/competitions';
defineOptions({ layout: AppLayout });
type Option = { id: string; name: string };
const props = defineProps<{
    record?: {
        id: string;
        name: string;
        description: string;
        location_id: string;
        parent_id: string | null;
    };
    locations: Option[];
    competitions: Option[];
}>();
const form = useForm({
    name: props.record?.name ?? '',
    description: props.record?.description ?? '',
    location_id: props.record?.location_id ?? '',
    parent_id: props.record?.parent_id ?? '',
});
function submit() {
    if (props.record) form.put(routes.update.url(props.record.id));
    else form.post(routes.store.url());
}
</script>
<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${record ? 'Edit' : 'Add'} competition`" />
        <Link
            :href="record ? routes.show(record.id) : routes.index()"
            class="text-sm underline"
            >Back to {{ record ? 'competition' : 'competitions' }}</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ record ? 'Edit' : 'Add' }} competition
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block font-medium">Name</label
                ><input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="255"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.name"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <label for="description" class="block font-medium"
                    >Description</label
                ><textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="4"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.description"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <div>
                <label for="location_id" class="block font-medium"
                    >Location</label
                ><select
                    id="location_id"
                    v-model="form.location_id"
                    required
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="" disabled>Select a location</option>
                    <option
                        v-for="location in locations"
                        :key="location.id"
                        :value="location.id"
                    >
                        {{ location.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.location_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.location_id }}
                </p>
                <p v-if="!locations.length" class="mt-1 text-sm text-slate-600">
                    Add a location before creating a competition.
                </p>
            </div>
            <div>
                <label for="parent_id" class="block font-medium"
                    >Parent competition (optional)</label
                ><select
                    id="parent_id"
                    v-model="form.parent_id"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                >
                    <option value="">None</option>
                    <option
                        v-for="competition in competitions.filter(
                            (item) => item.id !== record?.id,
                        )"
                        :key="competition.id"
                        :value="competition.id"
                    >
                        {{ competition.name }}
                    </option>
                </select>
                <p
                    v-if="form.errors.parent_id"
                    role="alert"
                    class="mt-1 text-sm text-red-700"
                >
                    {{ form.errors.parent_id }}
                </p>
            </div>
            <button
                type="submit"
                :disabled="form.processing || !locations.length"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Saving…' : 'Save competition' }}
            </button>
        </form>
    </main>
</template>
