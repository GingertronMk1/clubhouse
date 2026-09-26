<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import { Head } from '@inertiajs/vue3';
import { Sport } from '@/types/app';

defineProps<{
    sport: Sport;
}>();

const uppercaseScoreName = (name: string) =>
    name
        .split('_')
        .map((word: string) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
</script>

<template>
    <Layout>
        <Head title="Show" />
        <div class="flex flex-col gap-y-4">
            <h2 class="text-2xl" v-text="sport.name" />
            <p v-text="sport.description" />
            <section>
                <h3 class="text-xl">Scoring Breakdown</h3>
                <table class="table [&_th,&_td]:px-2 [&_th,&_td]:py-1">
                    <thead>
                        <tr>
                            <th>Score name</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(score, name) in sport.scoring" :key="name">
                            <td v-text="uppercaseScoreName(name)" />
                            <td v-text="score" />
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </Layout>
</template>
