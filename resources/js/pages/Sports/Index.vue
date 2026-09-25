<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Sport } from '@/types/app';
import { show } from '@/routes/sport';

defineProps<{
    sports: Sport[];
}>();
</script>

<template>
    <Layout>
        <Head title="Index" />
        <div class="flex flex-col gap-2">
            <Link v-for="sport in sports" :key="sport.id" :href="show(sport)">
                <h2 class="text-2xl" v-text="sport.name" />
            </Link>
        </div>
    </Layout>
</template>
