<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Layout from '@/layouts/Layout.vue';
import sport from '@/routes/sport';

defineProps<{
    counts: {
        sport: number;
    };
}>();
</script>

<template>
    <Layout>
        <div class="grid grid-cols-3">
            <Link class="group aspect-square p-4" :href="sport.index()">
                <div
                    class="flex h-full flex-col items-center justify-center gap-2 rounded-xl border-2 border-gray-900 group-hover:shadow-lg"
                >
                    <h3 class="text-3xl">Sports</h3>
                    <p class="text-xl">{{ counts.sport }}</p>
                </div>
            </Link>
        </div>
    </Layout>
</template>
