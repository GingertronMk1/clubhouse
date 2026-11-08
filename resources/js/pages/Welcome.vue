<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as sportsIndex, show as sportShow } from '@/routes/sports';
import { index as clubsIndex, show as clubShow } from '@/routes/clubs';

defineOptions({ layout: AppLayout });
defineProps<{
    sports: { id: string; name: string; description: string }[];
    clubs: { id: string; name: string; description: string }[];
}>();
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Home" />
        <h1 class="text-3xl font-semibold">Welcome to Clubhouse</h1>
        <p class="mt-2 text-slate-600">
            Explore the sports and clubs in your clubhouse.
        </p>
        <div class="mt-10 grid gap-8 md:grid-cols-2">
            <section
                aria-labelledby="sports-heading"
                class="rounded-lg border border-slate-200 p-6"
            >
                <div class="flex items-center justify-between gap-4">
                    <h2 id="sports-heading" class="text-xl font-semibold">
                        Sports
                        <span class="text-sm font-normal text-slate-500"
                            >({{ sports.length }})</span
                        >
                    </h2>
                    <Link
                        :href="sportsIndex()"
                        class="text-sm font-medium underline"
                        >View all</Link
                    >
                </div>
                <p
                    v-if="sports.length === 0"
                    class="mt-5 text-sm text-slate-600"
                >
                    No sports yet.
                </p>
                <ul v-else class="mt-5 divide-y divide-slate-100">
                    <li
                        v-for="sport in sports"
                        :key="sport.id"
                        class="py-3 first:pt-0 last:pb-0"
                    >
                        <Link
                            :href="sportShow(sport.id)"
                            class="font-medium underline"
                            >{{ sport.name }}</Link
                        >
                        <p class="mt-1 text-sm text-slate-600">
                            {{ sport.description }}
                        </p>
                    </li>
                </ul>
            </section>
            <section
                aria-labelledby="clubs-heading"
                class="rounded-lg border border-slate-200 p-6"
            >
                <div class="flex items-center justify-between gap-4">
                    <h2 id="clubs-heading" class="text-xl font-semibold">
                        Clubs
                        <span class="text-sm font-normal text-slate-500"
                            >({{ clubs.length }})</span
                        >
                    </h2>
                    <Link
                        :href="clubsIndex()"
                        class="text-sm font-medium underline"
                        >View all</Link
                    >
                </div>
                <p
                    v-if="clubs.length === 0"
                    class="mt-5 text-sm text-slate-600"
                >
                    No clubs yet.
                </p>
                <ul v-else class="mt-5 divide-y divide-slate-100">
                    <li
                        v-for="club in clubs"
                        :key="club.id"
                        class="py-3 first:pt-0 last:pb-0"
                    >
                        <Link
                            :href="clubShow(club.id)"
                            class="font-medium underline"
                            >{{ club.name }}</Link
                        >
                        <p class="mt-1 text-sm text-slate-600">
                            {{ club.description }}
                        </p>
                    </li>
                </ul>
            </section>
        </div>
    </main>
</template>
